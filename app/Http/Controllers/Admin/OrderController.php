<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function closed(Request $request): View
    {
        $query = Order::with(['table', 'waiter', 'cashier'])
            ->where('status', OrderStatus::Closed)
            ->orderByDesc('closed_at');

        if ($request->filled('mesa')) {
            $query->whereHas('table', fn ($q) => $q->where('number', $request->integer('mesa')));
        }

        if ($request->filled('garcom')) {
            $query->where('user_id', $request->integer('garcom'));
        }

        if ($request->filled('caixa')) {
            $query->where('cashier_id', $request->integer('caixa'));
        }

        if ($request->filled('pagamento')) {
            $query->where('payment_method', $request->get('pagamento'));
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('closed_at', '>=', $request->date('data_inicio'));
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('closed_at', '<=', $request->date('data_fim'));
        }

        $orders = $query->paginate(25)->withQueryString();

        $waiters = User::where('role', 'waiter')->orderBy('name')->get(['id', 'name']);
        $cashiers = User::where('role', 'cashier')->orderBy('name')->get(['id', 'name']);

        return view('admin.orders.closed', compact('orders', 'waiters', 'cashiers'));
    }

    public function show(Order $order): View
    {
        abort_if($order->status !== OrderStatus::Closed, 404);

        $order->load([
            'table',
            'waiter',
            'cashier',
            'items' => fn ($q) => $q->with(['product', 'options.option', 'options.choice'])->orderBy('created_at'),
        ]);

        return view('admin.orders.show', compact('order'));
    }
}
