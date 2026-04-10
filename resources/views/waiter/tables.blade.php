<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesas — Espetaria</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50" x-data="tablesPage()">

{{-- Forms ocultos para fechar mesas ocupadas --}}
@foreach ($tables as $table)
    @if ($table->status === \App\Enums\TableStatus::Occupied)
        <form method="POST"
              action="{{ route('waiter.tables.close', $table) }}"
              x-ref="closeForm{{ $table->id }}"
              class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endif
@endforeach

{{-- Overlay do drawer --}}
<div x-show="drawer"
     x-transition:enter="transition-opacity duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black/50 z-40"
     @click="drawer = false"
     style="display:none">
</div>

{{-- Menu lateral --}}
<aside x-show="drawer"
       x-transition:enter="transition-transform duration-300"
       x-transition:enter-start="-translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition-transform duration-200"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="-translate-x-full"
       class="fixed top-0 left-0 h-full w-72 bg-white z-50 shadow-2xl flex flex-col"
       style="display:none">

    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
        <span class="font-semibold text-gray-700">Menu</span>
        <button @click="drawer = false"
                class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <div class="flex-1 px-5 py-6">
        <div class="flex items-center gap-3 p-3 bg-orange-50 rounded-2xl">
            <div class="w-10 h-10 rounded-full bg-orange-200 flex items-center justify-center shrink-0">
                <span class="text-orange-700 font-bold text-sm">{{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}</span>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-orange-500 font-medium">Garçom</p>
            </div>
        </div>
    </div>

    <div class="px-5 pb-8">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-full py-3 bg-red-50 hover:bg-red-100 active:bg-red-200 text-red-500 font-semibold rounded-2xl transition-colors text-sm flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Sair
            </button>
        </form>
    </div>
</aside>

{{-- Modal de confirmação para fechar mesa --}}
<div x-show="closeModal"
     x-transition:enter="transition-opacity duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black/50 z-50 flex items-end sm:items-center justify-center p-4"
     style="display:none">

    <div class="bg-white rounded-2xl w-full sm:max-w-sm p-6 shadow-2xl"
         @click.stop>

        <div class="flex items-start gap-3 mb-5">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800">Fechar Mesa <span x-text="tableNumber"></span>?</h3>
                <p class="text-xs text-gray-500 mt-0.5">Itens pendentes serão cancelados e a mesa voltará a ficar livre.</p>
            </div>
        </div>

        <div class="flex gap-3">
            <button @click="closeModal = false"
                    class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 active:bg-gray-300 text-gray-700 font-semibold rounded-xl transition-colors text-sm">
                Cancelar
            </button>
            <button @click="confirmClose()"
                    class="flex-1 py-3 bg-red-500 hover:bg-red-600 active:bg-red-700 text-white font-semibold rounded-xl transition-colors text-sm">
                Fechar Mesa
            </button>
        </div>
    </div>
</div>

{{-- Header --}}
<header class="bg-white border-b border-gray-100 px-4 py-3 flex items-center justify-between sticky top-0 z-30">
    <button @click="drawer = true"
            class="p-2 -ml-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-colors">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>
    <h1 class="text-lg font-semibold text-gray-800">Mesas</h1>
    <div class="w-9"></div>
</header>

<x-toast />
<div class="p-4 flex justify-center gap-5 text-xs text-gray-400">
    <span class="flex items-center gap-1.5">
        <span class="w-2.5 h-2.5 rounded-full bg-green-400 inline-block"></span>Livre
    </span>
    <span class="flex items-center gap-1.5">
        <span class="w-2.5 h-2.5 rounded-full bg-red-400 inline-block"></span>Ocupada
    </span>
</div>
<main class="p-4">
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
        @foreach ($tables as $table)
            @php
                $isFree    = $table->status === \App\Enums\TableStatus::Free;
                $isOcc     = $table->status === \App\Enums\TableStatus::Occupied;
                $isWaiting = $table->status === \App\Enums\TableStatus::WaitingPayment;
            @endphp

            <div class="rounded-2xl p-3 flex flex-col items-center gap-2 min-h-[130px]
                {{ $isFree ? 'bg-green-50 border-2 border-green-200' : ($isOcc ? 'bg-red-50 border-2 border-red-200' : 'bg-yellow-50 border-2 border-yellow-200') }}">

                <span class="text-3xl font-bold
                    {{ $isFree ? 'text-green-700' : ($isOcc ? 'text-red-700' : 'text-yellow-700') }}">
                    {{ $table->number }}
                </span>

                <span class="text-xs font-medium
                    {{ $isFree ? 'text-green-600' : ($isOcc ? 'text-red-600' : 'text-yellow-600') }}">
                    {{ $isFree ? 'Livre' : ($isOcc ? 'Ocupada' : 'Aguardando') }}
                </span>

                <div class="w-full mt-auto flex flex-col gap-1.5">
                    @if ($isFree)
                        <form method="POST" action="{{ route('waiter.tables.open', $table) }}">
                            @csrf
                            <button class="w-full py-2 bg-green-500 hover:bg-green-600 active:bg-green-700 text-white text-sm font-semibold rounded-xl transition-colors">
                                Abrir
                            </button>
                        </form>
                    @elseif ($isOcc && $table->openOrder)
                        <a href="{{ route('waiter.orders.show', $table->openOrder) }}"
                           class="block w-full py-1.5 bg-red-500 hover:bg-red-600 active:bg-red-700 text-white text-xs font-semibold rounded-xl text-center transition-colors">
                            Ver pedido
                        </a>
                        <button @click="openCloseModal({{ $table->id }}, {{ $table->number }})"
                                class="w-full py-1.5 bg-white hover:bg-red-50 active:bg-red-100 text-red-500 border border-red-200 text-xs font-semibold rounded-xl text-center transition-colors">
                            Fechar
                        </button>
                    @else
                        <p class="text-center text-xs text-yellow-600 font-medium py-1">No caixa</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</main>

<script>
    function tablesPage() {
        return {
            drawer: false,
            closeModal: false,
            tableIdToClose: null,
            tableNumber: null,

            openCloseModal(tableId, tableNumber) {
                this.tableIdToClose = tableId;
                this.tableNumber = tableNumber;
                this.closeModal = true;
            },

            confirmClose() {
                this.$refs['closeForm' + this.tableIdToClose].submit();
            },
        };
    }
</script>

</body>
</html>
