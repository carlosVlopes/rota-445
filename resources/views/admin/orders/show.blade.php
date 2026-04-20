@extends('admin.layouts.app')

@section('title', 'Pedido #' . $order->id)
@section('page-title', 'Pedido #' . $order->id)
@section('page-subtitle', 'Mesa ' . $order->table->number . ' · Fechada em ' . $order->closed_at->format('d/m/Y H:i'))

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
    $itemsCount = $order->items->sum('quantity');
@endphp

@section('header-actions')
    <a href="{{ route('admin.pedidos.closed') }}"
       class="flex items-center gap-1.5 h-9 px-4 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-sm font-medium transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Voltar
    </a>
@endsection

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Coluna principal: itens --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Cards de resumo --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="bg-white rounded-2xl border border-gray-100 p-4">
                <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Mesa</p>
                <p class="text-2xl font-black text-gray-800 mt-1">{{ $order->table->number }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-4">
                <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Itens</p>
                <p class="text-2xl font-black text-gray-800 mt-1">{{ $itemsCount }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-4">
                <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Duração</p>
                <p class="text-2xl font-black text-gray-800 mt-1">{{ $order->opened_at->diffForHumans($order->closed_at, true, true, 2) }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-4">
                <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Total</p>
                <p class="text-2xl font-black text-gray-800 mt-1">R$ {{ number_format($order->total, 2, ',', '.') }}</p>
            </div>
        </div>

        {{-- Itens --}}
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-semibold text-gray-700">Itens do pedido</h2>
                <span class="text-sm text-gray-400">
                    {{ $order->items->count() }} {{ $order->items->count() === 1 ? 'linha' : 'linhas' }}
                </span>
            </div>

            @forelse ($order->items as $item)
                @php
                    $optionsDelta = $item->options->sum('price_delta');
                    $itemTotal = ($item->unit_price + $optionsDelta) * $item->quantity;
                @endphp
                <div class="px-5 py-4 {{ ! $loop->last ? 'border-b border-gray-50' : '' }}">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-gray-400 bg-gray-50 px-2 py-0.5 rounded-md shrink-0">
                                    {{ $item->quantity }}×
                                </span>
                                <p class="text-sm font-semibold text-gray-800">{{ $item->product->name }}</p>
                            </div>
                            @if ($item->options->isNotEmpty())
                                <div class="mt-1.5 space-y-0.5 ml-8">
                                    @foreach ($item->options as $option)
                                        <p class="text-xs text-gray-500">
                                            <span class="text-gray-400">{{ $option->option->label }}:</span>
                                            @if ($option->choice)
                                                {{ $option->choice->label }}
                                                @if ($option->price_delta > 0)
                                                    <span class="text-orange-500 font-semibold">+R$ {{ number_format($option->price_delta, 2, ',', '.') }}</span>
                                                @endif
                                            @elseif ($option->text_value)
                                                <span class="italic">{{ $option->text_value }}</span>
                                            @else
                                                <span class="text-gray-400">sim</span>
                                            @endif
                                        </p>
                                    @endforeach
                                </div>
                            @endif
                            @if ($item->notes)
                                <p class="text-xs text-gray-400 mt-1.5 ml-8 italic">"{{ $item->notes }}"</p>
                            @endif
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-sm font-bold text-gray-800">
                                R$ {{ number_format($itemTotal, 2, ',', '.') }}
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                R$ {{ number_format($item->unit_price, 2, ',', '.') }} un.
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-5 py-12 text-center text-gray-400 text-sm">
                    Nenhum item registrado.
                </div>
            @endforelse
        </div>

    </div>

    {{-- Coluna lateral: detalhes --}}
    <div class="space-y-5">

        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Responsáveis</p>

            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                        <span class="text-blue-600 font-bold text-sm">
                            {{ mb_strtoupper(mb_substr($order->waiter->name, 0, 1)) }}
                        </span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs text-gray-400">Garçom</p>
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $order->waiter->name }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center shrink-0">
                        @if ($order->cashier)
                            <span class="text-orange-600 font-bold text-sm">
                                {{ mb_strtoupper(mb_substr($order->cashier->name, 0, 1)) }}
                            </span>
                        @else
                            <span class="text-gray-400 font-bold text-sm">—</span>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs text-gray-400">Caixa</p>
                        <p class="text-sm font-semibold text-gray-800 truncate">
                            {{ $order->cashier?->name ?? 'Não registrado' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Detalhes</p>

            <div class="space-y-3 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Pagamento</span>
                    @if ($order->payment_method)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $paymentColors[$order->payment_method] ?? 'bg-gray-100 text-gray-500' }}">
                            {{ $paymentLabels[$order->payment_method] ?? $order->payment_method }}
                        </span>
                    @else
                        <span class="text-gray-400 text-xs">—</span>
                    @endif
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Aberta em</span>
                    <span class="font-semibold text-gray-800">
                        {{ $order->opened_at->format('d/m/Y H:i') }}
                    </span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Fechada em</span>
                    <span class="font-semibold text-gray-800">
                        {{ $order->closed_at->format('d/m/Y H:i') }}
                    </span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Duração</span>
                    <span class="font-semibold text-gray-800">
                        {{ $order->opened_at->diffForHumans($order->closed_at, true) }}
                    </span>
                </div>
            </div>

            <div class="border-t border-gray-100 mt-4 pt-4 flex items-end justify-between">
                <span class="text-sm font-semibold text-gray-600">Total</span>
                <span class="text-2xl font-black text-gray-800">
                    R$ {{ number_format($order->total, 2, ',', '.') }}
                </span>
            </div>
        </div>

        <div class="w-full h-12 bg-green-50 border-2 border-green-200 text-green-700 font-bold rounded-xl flex items-center justify-center gap-2 text-sm">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Comanda Fechada
        </div>

    </div>
</div>
@endsection
