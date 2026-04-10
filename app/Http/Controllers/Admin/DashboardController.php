<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Table;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $year = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);

        // Faturamento do mês selecionado
        $monthlyRevenue = Order::query()
            ->where('status', OrderStatus::Closed)
            ->whereYear('closed_at', $year)
            ->whereMonth('closed_at', $month)
            ->sum('total');

        // Faturamento do mês anterior (para comparação)
        $prevMonth = Carbon::create($year, $month, 1)->subMonth();
        $prevMonthRevenue = Order::query()
            ->where('status', OrderStatus::Closed)
            ->whereYear('closed_at', $prevMonth->year)
            ->whereMonth('closed_at', $prevMonth->month)
            ->sum('total');

        $revenueGrowth = $prevMonthRevenue > 0
            ? (($monthlyRevenue - $prevMonthRevenue) / $prevMonthRevenue) * 100
            : null;

        // Pedidos fechados no mês
        $monthlyOrders = Order::query()
            ->where('status', OrderStatus::Closed)
            ->whereYear('closed_at', $year)
            ->whereMonth('closed_at', $month)
            ->count();

        // Ticket médio
        $averageTicket = $monthlyOrders > 0 ? $monthlyRevenue / $monthlyOrders : 0;

        // Contadores gerais
        $totalProducts = Product::count();
        $activeProducts = Product::where('active', true)->count();
        $totalUsers = User::count();
        $totalTables = Table::count();

        // Dados do gráfico: faturamento diário no mês selecionado
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
        $dailyRevenueRaw = Order::query()
            ->where('status', OrderStatus::Closed)
            ->whereYear('closed_at', $year)
            ->whereMonth('closed_at', $month)
            ->selectRaw('DAY(closed_at) as day, SUM(total) as total, COUNT(*) as orders')
            ->groupByRaw('DAY(closed_at)')
            ->pluck('total', 'day');

        $dailyOrdersRaw = Order::query()
            ->where('status', OrderStatus::Closed)
            ->whereYear('closed_at', $year)
            ->whereMonth('closed_at', $month)
            ->selectRaw('DAY(closed_at) as day, COUNT(*) as orders')
            ->groupByRaw('DAY(closed_at)')
            ->pluck('orders', 'day');

        $chartLabels = [];
        $chartRevenue = [];
        $chartOrders = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $chartLabels[] = $d;
            $chartRevenue[] = (float) ($dailyRevenueRaw[$d] ?? 0);
            $chartOrders[] = (int) ($dailyOrdersRaw[$d] ?? 0);
        }

        // Faturamento dos últimos 12 meses (gráfico de barras)
        $last12Months = [];
        $last12Labels = [];
        $last12Revenue = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->startOfMonth()->subMonths($i);
            $rev = Order::query()
                ->where('status', OrderStatus::Closed)
                ->whereYear('closed_at', $date->year)
                ->whereMonth('closed_at', $date->month)
                ->sum('total');
            $last12Labels[] = $date->isoFormat('MMM/YY');
            $last12Revenue[] = (float) $rev;
        }

        // Top 5 produtos mais vendidos no mês
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.status', OrderStatus::Closed->value)
            ->whereYear('orders.closed_at', $year)
            ->whereMonth('orders.closed_at', $month)
            ->selectRaw('products.name, SUM(order_items.quantity) as qty, SUM(order_items.quantity * order_items.unit_price) as revenue')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('qty')
            ->limit(5)
            ->get();

        // Métodos de pagamento no mês
        $paymentMethods = Order::query()
            ->where('status', OrderStatus::Closed)
            ->whereYear('closed_at', $year)
            ->whereMonth('closed_at', $month)
            ->whereNotNull('payment_method')
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total) as total')
            ->groupBy('payment_method')
            ->get();

        // Anos disponíveis para o filtro
        $availableYears = Order::query()
            ->where('status', OrderStatus::Closed)
            ->whereNotNull('closed_at')
            ->selectRaw('YEAR(closed_at) as year')
            ->groupBy(DB::raw('YEAR(closed_at)'))
            ->orderByDesc('year')
            ->pluck('year')
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [now()->year];
        }

        return view('admin.dashboard', compact(
            'year',
            'month',
            'monthlyRevenue',
            'prevMonthRevenue',
            'revenueGrowth',
            'monthlyOrders',
            'averageTicket',
            'totalProducts',
            'activeProducts',
            'totalUsers',
            'totalTables',
            'chartLabels',
            'chartRevenue',
            'chartOrders',
            'last12Labels',
            'last12Revenue',
            'topProducts',
            'paymentMethods',
            'availableYears',
        ));
    }
}
