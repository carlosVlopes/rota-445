<?php

namespace App\Http\Controllers\Cashier;

use App\Enums\OrderStatus;
use App\Enums\TableStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Table;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Table::with(['openOrder.waiter'])
            ->whereIn('status', [TableStatus::Occupied, TableStatus::WaitingPayment])
            ->orderBy('number');

        if ($request->filled('mesa')) {
            $query->where('number', $request->integer('mesa'));
        }

        if ($request->filled('garcom')) {
            $query->whereHas('openOrder', fn ($q) => $q->where('user_id', $request->integer('garcom')));
        }

        $tables = $query->get();

        $waiters = User::whereHas('orders', fn ($q) => $q->where('status', OrderStatus::Open))
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('cashier.index', compact('tables', 'waiters'));
    }

    public function show(Table $table): View|RedirectResponse
    {
        if (! in_array($table->status, [TableStatus::Occupied, TableStatus::WaitingPayment])) {
            return redirect()->route('cashier.index')
                ->with('error', "Mesa {$table->number} não tem comanda aberta.");
        }

        $order = $table->openOrder;

        if (! $order) {
            return redirect()->route('cashier.index')
                ->with('error', "Mesa {$table->number} não tem comanda aberta.");
        }

        $order->load([
            'waiter',
            'items' => fn ($q) => $q->with(['product', 'options.option', 'options.choice'])->orderBy('created_at'),
        ]);

        return view('cashier.show', compact('table', 'order'));
    }

    public function close(Request $request, Table $table): RedirectResponse
    {
        if (! in_array($table->status, [TableStatus::Occupied, TableStatus::WaitingPayment])) {
            return redirect()->route('cashier.index')
                ->with('success', "Mesa {$table->number} já foi fechada.");
        }

        $validated = $request->validate([
            'payment_method' => ['required', 'string', 'in:dinheiro,credito,debito,pix'],
        ]);

        $order = $table->openOrder;

        if (! $order) {
            return redirect()->route('cashier.index')
                ->with('success', "Mesa {$table->number} já foi fechada.");
        }

        $order->update([
            'status'         => OrderStatus::Closed,
            'closed_at'      => now(),
            'payment_method' => $validated['payment_method'],
        ]);

        $table->update(['status' => TableStatus::Free]);

        return redirect()->route('cashier.index')
            ->with('success', "Mesa {$table->number} fechada com sucesso.");
    }

    public function closed(Request $request): View
    {
        $query = Order::with(['table', 'waiter'])
            ->where('status', OrderStatus::Closed)
            ->orderByDesc('closed_at');

        if ($request->filled('mesa')) {
            $query->whereHas('table', fn ($q) => $q->where('number', $request->integer('mesa')));
        }

        if ($request->filled('garcom')) {
            $query->where('user_id', $request->integer('garcom'));
        }

        $orders = $query->paginate(25)->withQueryString();

        $waiters = User::whereHas('orders', fn ($q) => $q->where('status', OrderStatus::Closed))
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('cashier.closed', compact('orders', 'waiters'));
    }

    public function closedShow(Order $order): View
    {
        abort_if($order->status !== OrderStatus::Closed, 404);

        $order->load([
            'table',
            'waiter',
            'items' => fn ($q) => $q->with(['product', 'options.option', 'options.choice'])->orderBy('created_at'),
        ]);

        return view('cashier.closed-show', compact('order'));
    }
}
