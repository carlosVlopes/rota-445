<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comanda #{{ $order->id }} — Caixa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-screen flex flex-col bg-gray-50 overflow-hidden">

{{-- Header --}}
<header class="bg-white border-b border-gray-100 px-6 py-3.5 flex items-center gap-4 shrink-0">
    <a href="{{ route('cashier.closed') }}"
       class="flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition-colors font-medium">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Voltar
    </a>
    <div class="w-px h-5 bg-gray-200"></div>
    <h1 class="text-base font-bold text-gray-800">Mesa {{ $order->table->number }}</h1>
    <span class="text-xs font-semibold bg-green-100 text-green-700 px-3 py-1 rounded-full">
        Fechada
    </span>
</header>

<x-toast />

{{-- Conteúdo em dois painéis --}}
<div class="flex-1 flex overflow-hidden">

    {{-- Painel esquerdo: itens --}}
    <div class="flex-1 overflow-y-auto p-8">
        <div class="max-w-2xl mx-auto space-y-6">

            {{-- Info da comanda --}}
            <div class="flex items-center justify-between bg-white rounded-2xl px-5 py-4 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $order->waiter->name }}</p>
                        <p class="text-xs text-gray-400">Garçom responsável</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-gray-800">{{ $order->opened_at->format('H:i') }}</p>
                    <p class="text-xs text-gray-400">Aberta em {{ $order->opened_at->format('d/m/Y') }}</p>
                </div>
            </div>

            {{-- Lista de itens --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-700">Itens do pedido</h2>
                    <span class="text-sm text-gray-400">
                        {{ $order->items->count() }} {{ $order->items->count() === 1 ? 'item' : 'itens' }}
                    </span>
                </div>

                @forelse ($order->items as $item)
                    @php
                        $itemTotal = ($item->unit_price + $item->options->sum('price_delta')) * $item->quantity;
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
                                            <p class="text-xs text-gray-400">
                                                {{ $option->option->label }}:
                                                @if ($option->choice)
                                                    {{ $option->choice->label }}
                                                    @if ($option->price_delta > 0)
                                                        <span class="text-orange-500">+R$ {{ number_format($option->price_delta, 2, ',', '.') }}</span>
                                                    @endif
                                                @elseif ($option->text_value)
                                                    {{ $option->text_value }}
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
    </div>

    {{-- Painel direito: resumo --}}
    <div class="w-80 bg-white border-l border-gray-100 flex flex-col shrink-0">

        {{-- Total --}}
        <div class="px-6 py-5 border-b border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Resumo</p>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between text-gray-500">
                    <span>{{ $order->items->count() }} {{ $order->items->count() === 1 ? 'item' : 'itens' }}</span>
                    <span>R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                </div>
            </div>
            <div class="border-t border-gray-100 mt-3 pt-3 flex items-end justify-between">
                <span class="text-sm font-semibold text-gray-600">Total</span>
                <span class="text-3xl font-black text-gray-800">
                    R$ {{ number_format($order->total, 2, ',', '.') }}
                </span>
            </div>
        </div>

        {{-- Detalhes do fechamento --}}
        <div class="px-6 py-5 flex-1 space-y-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Detalhes</p>

            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Pagamento</span>
                    @php
                        $paymentLabels = [
                            'dinheiro' => 'Dinheiro',
                            'debito'   => 'Débito',
                            'credito'  => 'Crédito',
                            'pix'      => 'Pix',
                        ];
                    @endphp
                    @if ($order->payment_method)
                        <span class="text-sm font-semibold text-gray-800">
                            {{ $paymentLabels[$order->payment_method] ?? $order->payment_method }}
                        </span>
                    @else
                        <span class="text-sm text-gray-400">—</span>
                    @endif
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Aberta às</span>
                    <span class="text-sm font-semibold text-gray-800">
                        {{ $order->opened_at->format('H:i') }} · {{ $order->opened_at->format('d/m/Y') }}
                    </span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Fechada às</span>
                    <span class="text-sm font-semibold text-gray-800">
                        {{ $order->closed_at->format('H:i') }} · {{ $order->closed_at->format('d/m/Y') }}
                    </span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Duração</span>
                    <span class="text-sm font-semibold text-gray-800">
                        {{ $order->opened_at->diffForHumans($order->closed_at, true) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Badge fechada --}}
        <div class="px-6 pb-6">
            <div class="w-full h-12 bg-green-50 border-2 border-green-200 text-green-700 font-bold rounded-xl flex items-center justify-center gap-2 text-sm">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Comanda Fechada
            </div>
        </div>
    </div>

</div>

</body>
</html>
