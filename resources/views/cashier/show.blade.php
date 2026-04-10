<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesa {{ $table->number }} — Caixa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-screen flex flex-col bg-gray-50 overflow-hidden" x-data="cashierShow()">

{{-- Modal de confirmação --}}
<div x-show="confirmModal"
     x-transition:enter="transition-opacity duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
     style="display:none">

    <div class="bg-white rounded-2xl w-full max-w-sm p-6 shadow-2xl" @click.stop>
        <div class="flex items-start gap-3 mb-6">
            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-800">Confirmar fechamento</h3>
                <p class="text-sm text-gray-500 mt-0.5">
                    Mesa {{ $table->number }} · R$ {{ number_format($order->total, 2, ',', '.') }} via
                    <span x-text="paymentLabel" class="font-medium text-gray-700"></span>
                </p>
            </div>
        </div>
        <div class="flex gap-3">
            <button @click="confirmModal = false"
                    class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors text-sm">
                Cancelar
            </button>
            <button @click="submitting = true; $refs.closeForm.submit()"
                    :disabled="submitting"
                    :class="submitting ? 'bg-green-300 cursor-not-allowed' : 'bg-green-500 hover:bg-green-600'"
                    class="flex-1 py-2.5 text-white font-semibold rounded-xl transition-colors text-sm">
                <span x-show="!submitting">Confirmar</span>
                <span x-show="submitting">Aguarde...</span>
            </button>
        </div>
    </div>
</div>

{{-- Form oculto --}}
<form x-ref="closeForm" method="POST" action="{{ route('cashier.close', $table) }}" class="hidden">
    @csrf
    <input type="hidden" name="payment_method" :value="paymentMethod">
</form>

{{-- Header --}}
<header class="bg-white border-b border-gray-100 px-6 py-3.5 flex items-center gap-4 shrink-0">
    <a href="{{ route('cashier.index') }}"
       class="flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition-colors font-medium">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Voltar
    </a>
    <div class="w-px h-5 bg-gray-200"></div>
    <h1 class="text-base font-bold text-gray-800">Mesa {{ $table->number }}</h1>
    @if ($table->status === \App\Enums\TableStatus::WaitingPayment)
        <span class="text-xs font-semibold bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full">
            Aguardando Pagamento
        </span>
    @else
        <span class="text-xs font-semibold bg-orange-100 text-orange-600 px-3 py-1 rounded-full">
            Ocupada
        </span>
    @endif
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
                    <p class="text-xs text-gray-400">{{ $order->opened_at->diffForHumans() }}</p>
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
                        Nenhum item na comanda.
                    </div>
                @endforelse
            </div>

        </div>
    </div>

    {{-- Painel direito: pagamento --}}
    <div class="w-80 bg-white border-l border-gray-100 flex flex-col shrink-0">

        {{-- Resumo do total --}}
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

        {{-- Forma de pagamento --}}
        <div class="px-6 py-5 flex-1">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Forma de pagamento</p>
            <div class="grid grid-cols-2 gap-2">
                @foreach ([
                    ['value' => 'dinheiro', 'label' => 'Dinheiro', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
                    ['value' => 'debito',   'label' => 'Débito',   'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                    ['value' => 'credito',  'label' => 'Crédito',  'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                    ['value' => 'pix',      'label' => 'Pix',      'icon' => 'M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01'],
                ] as $method)
                    <button type="button"
                            @click="paymentMethod = '{{ $method['value'] }}'; paymentLabel = '{{ $method['label'] }}'"
                            :class="paymentMethod === '{{ $method['value'] }}'
                                ? 'bg-orange-500 text-white border-orange-500 shadow-sm scale-[1.02]'
                                : 'bg-white text-gray-600 border-gray-200 hover:border-orange-300 hover:text-orange-500'"
                            class="flex flex-col items-center gap-1.5 py-3 px-2 rounded-xl border-2 transition-all text-xs font-semibold cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $method['icon'] }}"/>
                        </svg>
                        {{ $method['label'] }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Botão fechar --}}
        <div class="px-6 pb-6 pt-0">
            <button @click="openConfirmModal()"
                    :disabled="! paymentMethod"
                    :class="paymentMethod
                        ? 'bg-green-500 hover:bg-green-600 active:bg-green-700 cursor-pointer'
                        : 'bg-gray-200 cursor-not-allowed text-gray-400'"
                    class="w-full h-12 text-white font-bold rounded-xl transition-colors flex items-center justify-center gap-2 text-sm">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Fechar Comanda
            </button>
        </div>
    </div>

</div>

<script>
    function cashierShow() {
        return {
            paymentMethod: '',
            paymentLabel: '',
            confirmModal: false,
            submitting: false,

            openConfirmModal() {
                if (! this.paymentMethod) { return; }
                this.confirmModal = true;
            },
        };
    }
</script>

</body>
</html>
