<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesa {{ $order->table->number }} — Espetaria</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="min-h-screen bg-gray-50 flex flex-col" x-data="orderPage">

{{-- Header --}}
<header class="bg-white border-b border-gray-100 px-4 py-3 flex items-center gap-3 sticky top-0 z-20">
    <a href="{{ route('waiter.tables') }}"
        class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors shrink-0">
        &#8592;
    </a>
    <div class="flex-1 min-w-0">
        <p class="text-base font-semibold text-gray-800 leading-tight">Mesa {{ $order->table->number }}</p>
    </div>
    <div class="text-right shrink-0">
        <p class="text-lg font-bold text-gray-800" x-text="fmt(total)"></p>
    </div>
</header>

<x-toast />

{{-- Tab Bar --}}
<div class="bg-white border-b border-gray-100 flex sticky top-[57px] z-10">
    <button @click="tab = 'menu'"
        :class="tab === 'menu' ? 'border-b-2 border-orange-500 text-orange-600' : 'text-gray-400'"
        class="flex-1 py-3 text-sm font-medium transition-colors">
        Cardápio
    </button>
    <button @click="tab = 'order'"
        :class="tab === 'order' ? 'border-b-2 border-orange-500 text-orange-600' : 'text-gray-400'"
        class="flex-1 py-3 text-sm font-medium transition-colors">
        Pedido
        <span x-cloak x-show="pendingCount > 0" x-text="pendingCount"
            class="ml-1.5 inline-flex items-center justify-center w-5 h-5 rounded-full bg-orange-500 text-white text-xs font-bold"></span>
    </button>
</div>

{{-- ===== CARDÁPIO TAB ===== --}}
<div x-show="tab === 'menu'" class="flex-1 overflow-y-auto pb-28">
    @forelse ($categoriesData as $cat)
        <div class="mt-4 px-4">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                {{ $cat['name'] }}
            </h2>
            <div class="space-y-2">
                @foreach ($cat['products'] as $product)
                    <button type="button"
                        data-product="{{ json_encode($product) }}"
                        @click="openProduct(JSON.parse($el.dataset.product))"
                        class="w-full bg-white rounded-2xl px-4 py-3 flex items-center justify-between text-left shadow-sm active:shadow-none active:scale-[0.99] transition-all">
                        <span class="text-sm font-medium text-gray-800">{{ $product['name'] }}</span>
                        <span class="text-sm font-bold text-orange-500 ml-3 shrink-0">
                            R$ {{ number_format($product['price'], 2, ',', '.') }}
                        </span>
                    </button>
                @endforeach
            </div>
        </div>
    @empty
        <p class="text-center text-gray-400 text-sm mt-12">Nenhum produto disponível.</p>
    @endforelse
</div>

{{-- ===== PEDIDO TAB ===== --}}
<div x-show="tab === 'order'" class="flex-1 overflow-y-auto pb-28">

    {{-- TESTE: prévia do ticket de impressão --}}
    <div class="px-4 pt-4">
        <button type="button" @click="showTicket = true"
            class="w-full py-2 rounded-xl border border-dashed border-gray-300 text-xs text-gray-400 flex items-center justify-center gap-2 active:bg-gray-50 transition-colors">
            🖨️ Ver ticket (TESTE DE IMPRESSÃO)
        </button>
    </div>

    <p x-cloak x-show="items.length === 0" class="text-center text-gray-400 text-sm mt-12">Nenhum item no pedido.</p>

    <div x-cloak x-show="items.length > 0" class="p-4 space-y-3">
        <template x-for="item in items" :key="item.id">
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden"
                :class="item.status === 'pending' ? '' : (item.status === 'printing' ? 'opacity-70' : 'opacity-50')">

                {{-- Card body --}}
                <div class="px-4 pt-3 pb-3 flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 leading-tight"
                            x-text="item.quantity + '× ' + item.product.name"></p>

                        {{-- Options --}}
                        <template x-for="opt in item.options" :key="opt.id">
                            <p class="text-xs text-gray-500 mt-0.5">
                                <span x-text="opt.option.label + ': '"></span>
                                <span x-text="opt.choice ? opt.choice.label : (opt.text_value ?? 'Sim')"></span>
                                <span x-show="opt.choice && opt.price_delta > 0"
                                    x-text="'+R$ ' + opt.price_delta.toFixed(2).replace('.', ',')"
                                    class="text-green-600"></span>
                            </p>
                        </template>

                        <p x-show="item.notes" x-text="item.notes" class="text-xs text-gray-400 italic mt-0.5"></p>
                    </div>

                    <div class="flex flex-col items-end gap-2 shrink-0">
                        <span class="text-sm font-bold text-gray-800" x-text="fmt(itemTotal(item))"></span>

                        {{-- Status badge --}}
                        <template x-if="item.status === 'pending'">
                            <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">Pendente</span>
                        </template>
                        <template x-if="item.status === 'printing'">
                            <span class="text-xs px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-600">Imprimindo</span>
                        </template>
                        <template x-if="item.status === 'delivered'">
                            <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-600">Entregue</span>
                        </template>
                    </div>
                </div>

                {{-- Remove footer (only pending) --}}
                <div x-show="item.status === 'pending'" class="border-t border-red-100">
                    <button type="button" @click="removeOrderItem(item.id)"
                        class="w-full h-11 flex items-center justify-center gap-2 bg-red-50 text-red-500 text-sm font-semibold active:bg-red-100 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                            <path d="M10 11v6"/>
                            <path d="M14 11v6"/>
                            <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                        </svg>
                        Remover
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>

{{-- ===== STICKY FOOTER: Confirmar ===== --}}
<div x-cloak x-show="pendingCount > 0" class="fixed bottom-0 inset-x-0 p-4 bg-white border-t border-gray-100 z-10">
    <form method="POST" action="{{ route('waiter.orders.confirm', $order) }}">
        @csrf
        <button type="submit"
            class="w-full h-14 bg-orange-500 hover:bg-orange-600 active:bg-orange-700 text-white text-base font-semibold rounded-2xl transition-colors shadow-md"
            x-text="'Confirmar ' + pendingCount + (pendingCount === 1 ? ' item' : ' itens')">
        </button>
    </form>
</div>
<div x-cloak x-show="pendingCount === 0" class="fixed bottom-0 inset-x-0 p-4 bg-white border-t border-gray-100 z-10">
    <p class="text-center text-sm text-gray-400">Nenhum item pendente</p>
</div>

{{-- ===== MODAL TESTE: Prévia do ticket de impressão ===== --}}
@php
    $sep = '================================';
    $div = '--------------------------------';
    $mesa = 'Mesa: ' . $order->table->number;
    $garcom = 'Garçom: ' . $order->waiter->name;
    $pad = str_repeat(' ', max(1, 32 - strlen($mesa) - strlen($garcom)));
    $header = $mesa . $pad . $garcom;
    $ts = now()->format('d/m/Y  H:i');
    $num = '#PEDIDO ' . str_pad($order->id, 4, '0', STR_PAD_LEFT);

    $ticketLines = [$sep, '  ESPETARIA ROTA 445', $sep, $header, $div, $ts, $sep];
    $noteLines = [];

    foreach ($order->items as $ticketItem) {
        $ticketLines[] = str_pad($ticketItem->quantity . 'x', 3) . ' ' . $ticketItem->product->name;
        foreach ($ticketItem->options as $opt) {
            $val = $opt->choice?->label ?? $opt->text_value ?? 'Sim';
            $ticketLines[] = '    ' . $opt->option->label . ': ' . $val;
        }
        if ($ticketItem->notes) {
            $noteLines[] = $ticketItem->notes;
        }
    }

    $ticketLines[] = $div;
    foreach ($noteLines as $note) {
        $ticketLines[] = 'OBS: ' . $note;
    }
    $ticketLines[] = $sep;
    $ticketLines[] = '        ' . $num;
    $ticketLines[] = $sep;

    $ticket = implode("\n", $ticketLines);
@endphp

<div x-show="showTicket"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-end justify-center bg-black/40"
    @click.self="showTicket = false"
    style="display: none;">

    <div x-show="showTicket"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-y-full"
        x-transition:enter-end="translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-y-0"
        x-transition:leave-end="translate-y-full"
        class="bg-gray-100 rounded-t-2xl w-full max-w-lg max-h-[88vh] overflow-y-auto">

        <div class="p-5">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Prévia do Ticket (TESTE)</span>
                <button @click="showTicket = false"
                    class="w-8 h-8 flex items-center justify-center rounded-full bg-white text-gray-500 shrink-0">
                    ✕
                </button>
            </div>
            <pre class="font-mono text-xs leading-relaxed text-gray-800 bg-white rounded-xl p-4 overflow-x-auto shadow-sm whitespace-pre">{{ $ticket }}</pre>
        </div>
    </div>
</div>

{{-- ===== MODAL: Product options ===== --}}
<div x-show="showModal"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-end justify-center bg-black/40"
    @click.self="showModal = false"
    style="display: none;">

    <div x-show="showModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-y-full"
        x-transition:enter-end="translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-y-0"
        x-transition:leave-end="translate-y-full"
        class="bg-white rounded-t-2xl w-full max-w-lg max-h-[88vh] overflow-y-auto">

        <div class="p-5">

            {{-- Product header --}}
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 x-text="prod?.name" class="text-lg font-semibold text-gray-800 leading-tight"></h3>
                    <p x-text="fmt(prod?.price ?? 0)" class="text-orange-500 font-bold mt-0.5"></p>
                </div>
                <button @click="showModal = false"
                    class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-gray-500 shrink-0 ml-3">
                    ✕
                </button>
            </div>

            {{-- Dynamic options --}}
            <template x-for="opt in (prod?.options ?? [])" :key="opt.id">
                <div class="mb-5">
                    <p class="text-sm font-semibold text-gray-700 mb-2">
                        <span x-text="opt.label"></span>
                        <span x-show="opt.required" class="text-red-400 ml-0.5">*</span>
                    </p>

                    {{-- Toggle --}}
                    <template x-if="opt.type === 'toggle'">
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 cursor-pointer"
                            :class="optVals[opt.id] ? 'border-orange-400 bg-orange-50' : ''">
                            <input type="checkbox" x-model="optVals[opt.id]"
                                class="w-5 h-5 accent-orange-500 shrink-0">
                            <span class="text-sm text-gray-700" x-text="opt.label"></span>
                        </label>
                    </template>

                    {{-- Select (radio) --}}
                    <template x-if="opt.type === 'select'">
                        <div class="space-y-2">
                            <template x-for="choice in (opt.choices ?? [])" :key="choice.id">
                                <label class="flex items-center justify-between p-3 rounded-xl border cursor-pointer transition-colors"
                                    :class="optVals[opt.id] == choice.id ? 'border-orange-400 bg-orange-50' : 'border-gray-200'">
                                    <div class="flex items-center gap-3">
                                        <input type="radio"
                                            :name="'radio_' + opt.id"
                                            :value="choice.id"
                                            x-model="optVals[opt.id]"
                                            class="w-4 h-4 accent-orange-500 shrink-0">
                                        <span class="text-sm text-gray-700" x-text="choice.label"></span>
                                    </div>
                                    <span x-show="choice.price_add > 0"
                                        x-text="'+' + fmt(choice.price_add)"
                                        class="text-xs text-green-600 font-semibold ml-2"></span>
                                </label>
                            </template>
                        </div>
                    </template>

                    {{-- Extra (checkboxes, multiple) --}}
                    <template x-if="opt.type === 'extra'">
                        <div class="space-y-2">
                            <template x-for="choice in (opt.choices ?? [])" :key="choice.id">
                                <label class="flex items-center justify-between p-3 rounded-xl border cursor-pointer transition-colors"
                                    :class="isExtraOn(opt.id, choice.id) ? 'border-orange-400 bg-orange-50' : 'border-gray-200'">
                                    <div class="flex items-center gap-3">
                                        <input type="checkbox"
                                            :checked="isExtraOn(opt.id, choice.id)"
                                            @change="toggleExtra(opt.id, choice.id)"
                                            class="w-5 h-5 accent-orange-500 shrink-0">
                                        <span class="text-sm text-gray-700" x-text="choice.label"></span>
                                    </div>
                                    <span x-show="choice.price_add > 0"
                                        x-text="'+' + fmt(choice.price_add)"
                                        class="text-xs text-green-600 font-semibold ml-2"></span>
                                </label>
                            </template>
                        </div>
                    </template>

                    {{-- Text input --}}
                    <template x-if="opt.type === 'text'">
                        <textarea x-model="optVals[opt.id]"
                            :placeholder="opt.label"
                            rows="2"
                            class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-orange-400 focus:outline-none resize-none"></textarea>
                    </template>
                </div>
            </template>

            {{-- Notes --}}
            <div class="mb-5">
                <p class="text-sm font-semibold text-gray-700 mb-2">Observações</p>
                <textarea x-model="notes" rows="2"
                    placeholder="Ex: sem cebola, bem passado..."
                    class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-orange-400 focus:outline-none resize-none"></textarea>
            </div>

            {{-- Quantity --}}
            <div class="flex items-center justify-between mb-6">
                <span class="text-sm font-semibold text-gray-700">Quantidade</span>
                <div class="flex items-center gap-4">
                    <button type="button" @click="qty > 1 && qty--"
                        class="w-11 h-11 rounded-full bg-gray-100 text-gray-600 text-xl font-bold flex items-center justify-center active:bg-gray-200 transition-colors">
                        −
                    </button>
                    <span class="w-8 text-center text-xl font-bold text-gray-800" x-text="qty"></span>
                    <button type="button" @click="qty < 99 && qty++"
                        class="w-11 h-11 rounded-full bg-orange-500 text-white text-xl font-bold flex items-center justify-center active:bg-orange-600 transition-colors">
                        +
                    </button>
                </div>
            </div>

            {{-- Action buttons --}}
            <div class="flex gap-3">
                <button type="button" @click="showModal = false"
                    class="flex-1 h-13 py-3.5 rounded-2xl border border-gray-200 text-gray-600 text-sm font-semibold transition-colors active:bg-gray-50">
                    Cancelar
                </button>
                <button type="button" @click="addToOrder()"
                    class="flex-1 h-13 py-3.5 rounded-2xl bg-orange-500 text-white text-sm font-semibold transition-colors active:bg-orange-600">
                    Adicionar
                </button>
            </div>

        </div>
    </div>
</div>

<script>
window.__ORDER__ = {
    items: @json($itemsData),
    total: @json((float) $order->total),
    addUrl: @json(route('waiter.orders.item.add', $order)),
    removeUrl: @json(route('waiter.orders.item.remove', [$order, '__ID__'])),
    csrf: @json(csrf_token()),
};
</script>
</body>
</html>
