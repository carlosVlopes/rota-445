<?php

namespace App\Http\Controllers\Waiter;

use App\Enums\OrderStatus;
use App\Enums\TableStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Table;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TableController extends Controller
{
    public function index(): View
    {
        $tables = Table::with('openOrder')->orderBy('number')->get();

        return view('waiter.tables', compact('tables'));
    }

    public function open(Table $table): RedirectResponse
    {
        abort_if($table->status !== TableStatus::Free, 422, 'Mesa não está livre.');

        $order = Order::create([
            'table_id'  => $table->id,
            'user_id'   => Auth::id(),
            'status'    => OrderStatus::Open,
            'total'     => 0,
            'opened_at' => now(),
        ]);

        $table->update(['status' => TableStatus::Occupied]);

        return redirect()->route('waiter.orders.show', $order);
    }

    public function close(Table $table): RedirectResponse
    {
        abort_if($table->status !== TableStatus::Occupied, 422, 'Mesa não está ocupada.');

        $table->openOrder?->update([
            'status'    => OrderStatus::Closed,
            'closed_at' => now(),
        ]);

        $table->update(['status' => TableStatus::Free]);

        return redirect()->route('waiter.tables')->with('success', "Mesa {$table->number} fechada.");
    }
}
