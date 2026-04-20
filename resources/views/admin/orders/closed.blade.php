@extends('admin.layouts.app')

@section('title', 'Pedidos Fechados')
@section('page-title', 'Pedidos Fechados')
@section('page-subtitle', $orders->total() . ' ' . ($orders->total() === 1 ? 'pedido encontrado' : 'pedidos encontrados'))

@php
    $paymentLabels = [
        'dinheiro' => 'Dinheiro',
        'debito'   => 'Débito',
        'credito'  => 'Crédito',
        'pix'      => 'Pix',
    ];
    $paymentColors = [
        'dinheiro' => 'bg-green-100 text-green-700',
        'debito'   => 'bg-blue-100 text-blue-700',
        'credito'  => 'bg-purple-100 text-purple-700',
        'pix'      => 'bg-teal-100 text-teal-700',
    ];
@endphp

@section('content')

{{-- Filtros --}}
<form method="GET" action="{{ route('admin.pedidos.closed') }}"
      class="bg-white rounded-2xl border border-gray-100 p-4 mb-4 flex flex-wrap gap-3 items-end">
    <div class="w-32">
        <label class="block text-xs text-gray-500 mb-1">Mesa</label>
        <input type="number" name="mesa" value="{{ request('mesa') }}" min="1"
               placeholder="Nº"
               class="w-full h-9 px-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-orange-400">
    </div>
    <div class="min-w-40">
        <label class="block text-xs text-gray-500 mb-1">Garçom</label>
        <select name="garcom"
                class="w-full h-9 px-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-orange-400 bg-white">
            <option value="">Todos</option>
            @foreach ($waiters as $waiter)
                <option value="{{ $waiter->id }}" {{ (string) request('garcom') === (string) $waiter->id ? 'selected' : '' }}>
                    {{ $waiter->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="min-w-40">
        <label class="block text-xs text-gray-500 mb-1">Caixa</label>
        <select name="caixa"
                class="w-full h-9 px-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-orange-400 bg-white">
            <option value="">Todos</option>
            @foreach ($cashiers as $cashier)
                <option value="{{ $cashier->id }}" {{ (string) request('caixa') === (string) $cashier->id ? 'selected' : '' }}>
                    {{ $cashier->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="min-w-36">
        <label class="block text-xs text-gray-500 mb-1">Pagamento</label>
        <select name="pagamento"
                class="w-full h-9 px-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-orange-400 bg-white">
            <option value="">Todos</option>
            @foreach ($paymentLabels as $value => $label)
                <option value="{{ $value }}" {{ request('pagamento') === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="w-40">
        <label class="block text-xs text-gray-500 mb-1">De</label>
        <input type="date" name="data_inicio" value="{{ request('data_inicio') }}"
               class="w-full h-9 px-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-orange-400">
    </div>
    <div class="w-40">
        <label class="block text-xs text-gray-500 mb-1">Até</label>
        <input type="date" name="data_fim" value="{{ request('data_fim') }}"
               class="w-full h-9 px-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-orange-400">
    </div>
    <button type="submit"
            class="h-9 px-4 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-sm font-medium transition-colors">
        Filtrar
    </button>
    @if (request()->hasAny(['mesa', 'garcom', 'caixa', 'pagamento', 'data_inicio', 'data_fim']))
        <a href="{{ route('admin.pedidos.closed') }}"
           class="h-9 px-4 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-sm font-medium transition-colors flex items-center">
            Limpar
        </a>
    @endif
</form>

{{-- Tabela --}}
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
    @if ($orders->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-gray-400">
            <svg class="w-12 h-12 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="font-medium">Nenhum pedido encontrado</p>
        </div>
    @else
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">#</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Mesa</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Garçom</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Caixa</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Pagamento</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Total</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Fechada em</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($orders as $order)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-3.5 text-gray-400 font-mono text-xs">#{{ $order->id }}</td>
                        <td class="px-5 py-3.5">
                            <span class="font-bold text-gray-800">{{ $order->table->number }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-gray-600">{{ $order->waiter->name }}</td>
                        <td class="px-5 py-3.5 text-gray-600">
                            {{ $order->cashier?->name ?? '—' }}
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if ($order->payment_method)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $paymentColors[$order->payment_method] ?? 'bg-gray-100 text-gray-500' }}">
                                    {{ $paymentLabels[$order->payment_method] ?? $order->payment_method }}
                                </span>
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right font-bold text-gray-800">
                            R$ {{ number_format($order->total, 2, ',', '.') }}
                        </td>
                        <td class="px-5 py-3.5">
                            <p class="text-gray-700">{{ $order->closed_at->format('d/m/Y H:i') }}</p>
                            <p class="text-xs text-gray-400">{{ $order->closed_at->diffForHumans() }}</p>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <a href="{{ route('admin.pedidos.show', $order) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 hover:bg-orange-50 hover:text-orange-600 text-gray-600 text-xs font-semibold rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Ver
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if ($orders->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $orders->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
