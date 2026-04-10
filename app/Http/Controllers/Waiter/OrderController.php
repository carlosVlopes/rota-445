<?php

namespace App\Http\Controllers\Waiter;

use App\Enums\OrderItemStatus;
use App\Enums\OrderStatus;
use App\Enums\PrintJobStatus;
use App\Enums\TableStatus;
use App\Events\OrderConfirmed;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemOption;
use App\Models\PrintJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function show(Order $order): View
    {
        $this->authorizeOrder($order);

        $order->load([
            'table',
            'waiter',
            'items' => fn ($q) => $q->with(['product', 'options.option', 'options.choice'])->orderBy('created_at'),
        ]);

        $categories = Category::active()
            ->with(['products' => fn ($q) => $q->active()->orderBy('order')->with(['options.choices'])])
            ->orderBy('order')
            ->get();

        $categoriesData = $categories->map(fn ($cat) => [
            'id'       => $cat->id,
            'name'     => $cat->name,
            'products' => $cat->products->map(fn ($p) => [
                'id'      => $p->id,
                'name'    => $p->name,
                'price'   => (float) $p->price,
                'options' => $p->options->map(fn ($o) => [
                    'id'       => $o->id,
                    'label'    => $o->label,
                    'type'     => $o->type->value,
                    'required' => $o->required,
                    'choices'  => $o->choices->map(fn ($c) => [
                        'id'        => $c->id,
                        'label'     => $c->label,
                        'price_add' => (float) $c->price_add,
                    ])->values()->all(),
                ])->values()->all(),
            ])->values()->all(),
        ])->values()->all();

        $itemsData = $order->items->map(fn ($item) => $this->formatItem($item))->values()->all();

        return view('waiter.order', compact('order', 'categoriesData', 'itemsData'));
    }

    public function addItem(Request $request, Order $order): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $this->authorizeOrder($order);

        $validated = $request->validate([
            'product_id'              => ['required', 'integer', 'exists:products,id'],
            'quantity'                => ['required', 'integer', 'min:1', 'max:99'],
            'notes'                   => ['nullable', 'string', 'max:255'],
            'options'                 => ['nullable', 'array'],
            'options.*.option_id'     => ['required', 'integer', 'exists:product_options,id'],
            'options.*.choice_id'     => ['nullable', 'integer', 'exists:product_option_choices,id'],
            'options.*.text_value'    => ['nullable', 'string', 'max:255'],
        ]);

        $product = \App\Models\Product::with('options.choices')->findOrFail($validated['product_id']);

        $item = null;

        DB::transaction(function () use ($validated, $product, $order, &$item) {
            $item = OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $product->id,
                'quantity'   => $validated['quantity'],
                'unit_price' => $product->price,
                'notes'      => $validated['notes'] ?? null,
                'status'     => OrderItemStatus::Pending,
            ]);

            foreach ($validated['options'] ?? [] as $opt) {
                $priceDelta = 0;

                if (! empty($opt['choice_id'])) {
                    $choice = $product->options
                        ->flatMap(fn ($o) => $o->choices)
                        ->firstWhere('id', $opt['choice_id']);

                    $priceDelta = $choice?->price_add ?? 0;
                }

                OrderItemOption::create([
                    'order_item_id' => $item->id,
                    'option_id'     => $opt['option_id'],
                    'choice_id'     => $opt['choice_id'] ?? null,
                    'text_value'    => $opt['text_value'] ?? null,
                    'price_delta'   => $priceDelta,
                ]);
            }

            $this->recalculateTotal($order);
        });

        if ($request->wantsJson()) {
            $item->load(['product', 'options.option', 'options.choice']);
            $order->refresh();

            return response()->json([
                'item'  => $this->formatItem($item),
                'total' => (float) $order->total,
            ]);
        }

        return redirect()->route('waiter.orders.show', $order);
    }

    public function removeItem(Request $request, Order $order, OrderItem $item): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $this->authorizeOrder($order);

        abort_if($item->order_id !== $order->id, 403);
        abort_if($item->status !== OrderItemStatus::Pending, 422, 'Apenas itens pendentes podem ser removidos.');

        $item->delete();
        $this->recalculateTotal($order);

        if ($request->wantsJson()) {
            $order->refresh();

            return response()->json(['total' => (float) $order->total]);
        }

        return redirect()->route('waiter.orders.show', ['order' => $order, 'tab' => 'order']);
    }

    public function confirm(Order $order): RedirectResponse
    {
        $this->authorizeOrder($order);

        $pendingItems = $order->items()
            ->where('status', OrderItemStatus::Pending)
            ->with(['product', 'options.option', 'options.choice'])
            ->get();

        abort_if($pendingItems->isEmpty(), 422, 'Não há itens pendentes para confirmar.');

        $order->loadMissing(['table', 'waiter']);

        DB::transaction(function () use ($order, $pendingItems) {
            $nextSequence = ($order->items()->whereNotNull('print_sequence')->max('print_sequence') ?? 0) + 1;
            $printJobs = [];

            foreach ($pendingItems as $item) {
                $item->update([
                    'status'         => OrderItemStatus::Printing,
                    'print_sequence' => $nextSequence,
                    'printed_at'     => now(),
                ]);

                $printJob = PrintJob::create([
                    'order_item_id' => $item->id,
                    'status'        => PrintJobStatus::Pending,
                    'payload'       => $this->buildPrintPayload($order, $item, $nextSequence),
                    'attempts'      => 0,
                ]);

                $printJobs[] = $printJob;
                $nextSequence++;
            }

            event(new OrderConfirmed($order, $pendingItems, $printJobs));
        });

        return redirect()->route('waiter.orders.show', $order)
            ->with('success', 'Pedido enviado para impressão!');
    }

    /**
     * @return array{id: int, status: string, quantity: int, unit_price: float, notes: string|null, product: array{name: string}, options: array<array{id: int, option: array{label: string}, choice: array{label: string}|null, text_value: string|null, price_delta: float}>}
     */
    private function formatItem(OrderItem $item): array
    {
        return [
            'id'         => $item->id,
            'status'     => $item->status->value,
            'quantity'   => $item->quantity,
            'unit_price' => (float) $item->unit_price,
            'notes'      => $item->notes,
            'product'    => ['name' => $item->product->name],
            'options'    => $item->options->map(fn ($opt) => [
                'id'          => $opt->id,
                'option'      => ['label' => $opt->option->label],
                'choice'      => $opt->choice ? ['label' => $opt->choice->label] : null,
                'text_value'  => $opt->text_value,
                'price_delta' => (float) $opt->price_delta,
            ])->values()->all(),
        ];
    }

    private function authorizeOrder(Order $order): void
    {
        $order->loadMissing('table');

        abort_if($order->status !== OrderStatus::Open, 403, 'Pedido não está aberto.');
        abort_if($order->table->status !== TableStatus::Occupied, 403, 'Mesa não está ocupada.');
    }

    private function recalculateTotal(Order $order): void
    {
        $total = $order->items()->with('options')->get()->sum(
            fn (OrderItem $item) => ($item->unit_price + $item->options->sum('price_delta')) * $item->quantity
        );

        $order->update(['total' => $total]);
    }

    /**
     * @return array{sequence: int, table: int, waiter: string, order_id: int, timestamp: string, item: array}
     */
    private function buildPrintPayload(Order $order, OrderItem $item, int $sequence): array
    {
        return [
            'sequence'  => $sequence,
            'table'     => $order->table->number,
            'waiter'    => $order->waiter->name,
            'order_id'  => $order->id,
            'timestamp' => now()->toIso8601String(),
            'item'      => [
                'id'       => $item->id,
                'product'  => $item->product->name,
                'quantity' => $item->quantity,
                'notes'    => $item->notes,
                'options'  => $item->options->map(fn ($opt) => [
                    'label'      => $opt->option->label,
                    'choice'     => $opt->choice?->label,
                    'text_value' => $opt->text_value,
                ])->values()->all(),
            ],
        ];
    }
}
