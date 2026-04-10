@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', now()->isoFormat('dddd, D [de] MMMM [de] YYYY'))

@push('head')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@php
    $months = [
        1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
        5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
        9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro',
    ];
    $paymentLabels = [
        'dinheiro' => 'Dinheiro',
        'credito'  => 'Cartão de Crédito',
        'debito'   => 'Cartão de Débito',
        'pix'      => 'PIX',
    ];
@endphp

@section('header-actions')
    <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
        <select name="month"
                class="h-9 px-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-orange-400 bg-white"
                onchange="this.form.submit()">
            @foreach ($months as $num => $label)
                <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <select name="year"
                class="h-9 px-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-orange-400 bg-white"
                onchange="this.form.submit()">
            @foreach ($availableYears as $y)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
    </form>
@endsection

@section('content')

{{-- KPI Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    {{-- Faturamento --}}
    <div class="bg-white rounded-2xl p-5 border border-gray-100">
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            @if ($revenueGrowth !== null)
                <span class="text-xs font-semibold px-2 py-1 rounded-full {{ $revenueGrowth >= 0 ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-500' }}">
                    {{ $revenueGrowth >= 0 ? '+' : '' }}{{ number_format($revenueGrowth, 1) }}%
                </span>
            @endif
        </div>
        <p class="text-2xl font-black text-gray-800">R$ {{ number_format($monthlyRevenue, 2, ',', '.') }}</p>
        <p class="text-xs text-gray-400 mt-1">Faturamento do mês</p>
    </div>

    {{-- Pedidos --}}
    <div class="bg-white rounded-2xl p-5 border border-gray-100">
        <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <p class="text-2xl font-black text-gray-800">{{ number_format($monthlyOrders) }}</p>
        <p class="text-xs text-gray-400 mt-1">Pedidos fechados</p>
    </div>

    {{-- Ticket médio --}}
    <div class="bg-white rounded-2xl p-5 border border-gray-100">
        <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
        </div>
        <p class="text-2xl font-black text-gray-800">R$ {{ number_format($averageTicket, 2, ',', '.') }}</p>
        <p class="text-xs text-gray-400 mt-1">Ticket médio</p>
    </div>

    {{-- Produtos ativos --}}
    <div class="bg-white rounded-2xl p-5 border border-gray-100">
        <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
        </div>
        <p class="text-2xl font-black text-gray-800">{{ $activeProducts }}<span class="text-base font-medium text-gray-400">/{{ $totalProducts }}</span></p>
        <p class="text-xs text-gray-400 mt-1">Produtos ativos</p>
    </div>
</div>

{{-- Gráficos linha: faturamento diário --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
    <div class="lg:col-span-2 bg-white rounded-2xl p-5 border border-gray-100">
        <h2 class="text-sm font-bold text-gray-700 mb-4">Faturamento diário — {{ $months[$month] }} {{ $year }}</h2>
        <canvas id="dailyRevenueChart" height="100"></canvas>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-gray-100">
        <h2 class="text-sm font-bold text-gray-700 mb-4">Pedidos por dia</h2>
        <canvas id="dailyOrdersChart" height="160"></canvas>
    </div>
</div>

{{-- Gráfico anual + Top produtos + Pagamentos --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

    {{-- Últimos 12 meses --}}
    <div class="lg:col-span-2 bg-white rounded-2xl p-5 border border-gray-100">
        <h2 class="text-sm font-bold text-gray-700 mb-4">Faturamento — últimos 12 meses</h2>
        <canvas id="yearlyChart" height="100"></canvas>
    </div>

    {{-- Pagamentos --}}
    <div class="bg-white rounded-2xl p-5 border border-gray-100">
        <h2 class="text-sm font-bold text-gray-700 mb-4">Métodos de pagamento</h2>
        @if ($paymentMethods->isEmpty())
            <div class="flex flex-col items-center justify-center h-32 text-gray-400">
                <svg class="w-8 h-8 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <p class="text-sm">Sem dados</p>
            </div>
        @else
            <canvas id="paymentChart" height="180"></canvas>
        @endif
    </div>
</div>

{{-- Top 5 produtos --}}
<div class="bg-white rounded-2xl p-5 border border-gray-100">
    <h2 class="text-sm font-bold text-gray-700 mb-4">Top 5 produtos mais vendidos — {{ $months[$month] }} {{ $year }}</h2>
    @if ($topProducts->isEmpty())
        <div class="flex items-center justify-center h-20 text-gray-400 text-sm">
            Nenhuma venda registrada neste período.
        </div>
    @else
        <div class="space-y-3">
            @foreach ($topProducts as $i => $product)
                @php
                    $maxQty = $topProducts->first()->qty;
                    $pct = $maxQty > 0 ? ($product->qty / $maxQty) * 100 : 0;
                @endphp
                <div class="flex items-center gap-4">
                    <span class="w-6 text-sm font-bold text-gray-400">{{ $i + 1 }}</span>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-sm font-medium text-gray-700 truncate">{{ $product->name }}</p>
                            <span class="text-xs text-gray-400 ml-2 shrink-0">{{ $product->qty }}x</span>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-2 bg-orange-400 rounded-full transition-all"
                                 style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                    <span class="text-sm font-semibold text-gray-600 w-28 text-right shrink-0">
                        R$ {{ number_format($product->revenue, 2, ',', '.') }}
                    </span>
                </div>
            @endforeach
        </div>
    @endif
</div>

@endsection

@push('head')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const orange = '#f97316';
    const orangeLight = 'rgba(249,115,22,0.15)';
    const blue = '#3b82f6';
    const blueLight = 'rgba(59,130,246,0.15)';

    Chart.defaults.font.family = 'ui-sans-serif, system-ui, sans-serif';
    Chart.defaults.color = '#9ca3af';

    // Faturamento diário
    new Chart(document.getElementById('dailyRevenueChart'), {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Faturamento (R$)',
                data: @json($chartRevenue),
                borderColor: orange,
                backgroundColor: orangeLight,
                borderWidth: 2.5,
                pointRadius: 3,
                pointHoverRadius: 5,
                fill: true,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ' R$ ' + ctx.raw.toLocaleString('pt-BR', { minimumFractionDigits: 2 })
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6' },
                    ticks: {
                        callback: v => 'R$ ' + v.toLocaleString('pt-BR')
                    }
                },
                x: { grid: { display: false } }
            }
        }
    });

    // Pedidos por dia
    new Chart(document.getElementById('dailyOrdersChart'), {
        type: 'bar',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Pedidos',
                data: @json($chartOrders),
                backgroundColor: blue,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f3f4f6' }, ticks: { stepSize: 1 } },
                x: { grid: { display: false } }
            }
        }
    });

    // Últimos 12 meses
    new Chart(document.getElementById('yearlyChart'), {
        type: 'bar',
        data: {
            labels: @json($last12Labels),
            datasets: [{
                label: 'Faturamento (R$)',
                data: @json($last12Revenue),
                backgroundColor: orangeLight,
                borderColor: orange,
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ' R$ ' + ctx.raw.toLocaleString('pt-BR', { minimumFractionDigits: 2 })
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6' },
                    ticks: { callback: v => 'R$ ' + v.toLocaleString('pt-BR') }
                },
                x: { grid: { display: false } }
            }
        }
    });

    @if ($paymentMethods->isNotEmpty())
    @php
        $paymentChartLabels = $paymentMethods->pluck('payment_method')->map(function ($m) {
            return match ($m) {
                'dinheiro' => 'Dinheiro',
                'credito'  => 'Crédito',
                'debito'   => 'Débito',
                'pix'      => 'PIX',
                default    => $m,
            };
        })->values();
    @endphp
    // Pagamentos
    new Chart(document.getElementById('paymentChart'), {
        type: 'doughnut',
        data: {
            labels: @json($paymentChartLabels),
            datasets: [{
                data: @json($paymentMethods->pluck('count')->values()),
                backgroundColor: ['#f97316','#3b82f6','#8b5cf6','#10b981'],
                borderWidth: 0,
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: {
                legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true } }
            }
        }
    });
    @endif
});
</script>
@endpush
