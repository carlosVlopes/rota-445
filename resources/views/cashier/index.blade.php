<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caixa — Espetaria Rota 445</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-screen flex bg-gray-50 overflow-hidden">

{{-- Sidebar --}}
<aside class="w-56 bg-white border-r border-gray-100 flex flex-col shrink-0">

    {{-- Brand --}}
    <div class="px-5 py-5 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-orange-500 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-800 leading-tight">Rota 445</p>
                <p class="text-xs text-gray-400">Espetaria</p>
            </div>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 px-3 py-4 space-y-1">
        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider px-3 mb-2">Menu</p>
        <a href="{{ route('cashier.index') }}"
           class="flex items-center gap-2.5 px-3 py-2.5 bg-orange-50 text-orange-600 rounded-xl font-semibold text-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Comandas
        </a>
        <a href="{{ route('cashier.closed') }}"
           class="flex items-center gap-2.5 px-3 py-2.5 text-gray-600 hover:bg-gray-50 hover:text-gray-800 rounded-xl font-medium text-sm transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Fechadas
        </a>
    </nav>

    {{-- User + Logout --}}
    <div class="px-3 pb-4 pt-3 border-t border-gray-100 space-y-1">
        <div class="flex items-center gap-2.5 px-3 py-2.5">
            <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center shrink-0">
                <span class="text-orange-600 font-bold text-xs">
                    {{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}
                </span>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-400">Caixa</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-full py-2 bg-gray-50 hover:bg-red-50 hover:text-red-500 text-gray-500 font-medium rounded-xl transition-colors text-sm flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Sair
            </button>
        </form>
    </div>
</aside>

{{-- Main --}}
<div class="flex-1 flex flex-col overflow-hidden">

    {{-- Top bar --}}
    <header class="bg-white border-b border-gray-100 px-8 py-4 flex items-center justify-between shrink-0">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Comandas Abertas</h1>
            <p class="text-sm text-gray-400 mt-0.5">{{ now()->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</p>
        </div>

        @if ($tables->isNotEmpty())
            @php
                $totalOpen = $tables->sum(fn ($t) => $t->openOrder?->total ?? 0);
                $waitingCount = $tables->where('status', \App\Enums\TableStatus::WaitingPayment)->count();
            @endphp
            <div class="flex items-center gap-8">
                <div class="text-right">
                    <p class="text-2xl font-black text-gray-800">{{ $tables->count() }}</p>
                    <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">
                        {{ $tables->count() === 1 ? 'Mesa aberta' : 'Mesas abertas' }}
                    </p>
                </div>
                @if ($waitingCount > 0)
                    <div class="text-right">
                        <p class="text-2xl font-black text-yellow-500">{{ $waitingCount }}</p>
                        <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">Aguardando</p>
                    </div>
                @endif
                <div class="w-px h-10 bg-gray-100"></div>
                <div class="text-right">
                    <p class="text-2xl font-black text-orange-500">R$ {{ number_format($totalOpen, 2, ',', '.') }}</p>
                    <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">Total em aberto</p>
                </div>
            </div>
        @endif
    </header>

    <x-toast />

    {{-- Content --}}
    <div class="flex-1 overflow-y-auto p-8">

        {{-- Filtros --}}
        <form method="GET" action="{{ route('cashier.index') }}"
              class="flex items-center gap-3 mb-6">
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <input type="number"
                       name="mesa"
                       value="{{ request('mesa') }}"
                       placeholder="Nº da mesa"
                       min="1"
                       class="pl-9 pr-3 py-2 text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-orange-400 w-36 transition">
            </div>

            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <select name="garcom"
                        onchange="this.form.submit()"
                        class="pl-9 pr-8 py-2 text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-orange-400 appearance-none w-48 transition cursor-pointer">
                    <option value="">Todos os garçons</option>
                    @foreach ($waiters as $waiter)
                        <option value="{{ $waiter->id }}" {{ request('garcom') == $waiter->id ? 'selected' : '' }}>
                            {{ $waiter->name }}
                        </option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>

            <button type="submit"
                    class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-xl transition-colors">
                Filtrar
            </button>

            @if (request('mesa') || request('garcom'))
                <a href="{{ route('cashier.index') }}"
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-semibold rounded-xl transition-colors">
                    Limpar
                </a>
            @endif
        </form>

        @if ($tables->isEmpty())
            <div class="flex flex-col items-center justify-center py-24 text-center">
                <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mb-5">
                    <svg class="w-10 h-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                @if (request('mesa') || request('garcom'))
                    <p class="text-gray-500 font-semibold text-lg">Nenhuma comanda encontrada</p>
                    <p class="text-gray-400 text-sm mt-1">Tente ajustar os filtros.</p>
                    <a href="{{ route('cashier.index') }}" class="mt-4 text-sm text-orange-500 hover:text-orange-600 font-medium">
                        Limpar filtros
                    </a>
                @else
                    <p class="text-gray-500 font-semibold text-lg">Nenhuma mesa com comanda aberta</p>
                    <p class="text-gray-400 text-sm mt-1">As comandas aparecerão aqui quando houver mesas ocupadas.</p>
                @endif
            </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                @foreach ($tables as $table)
                    @php
                        $order = $table->openOrder;
                        $isWaiting = $table->status === \App\Enums\TableStatus::WaitingPayment;
                    @endphp

                    <a href="{{ route('cashier.show', $table) }}"
                       class="group flex flex-col rounded-2xl p-4 transition-all
                           {{ $isWaiting
                               ? 'bg-yellow-50 border-2 border-yellow-200 hover:border-yellow-400 hover:shadow-lg hover:shadow-yellow-100/60'
                               : 'bg-white border-2 border-gray-100 hover:border-orange-300 hover:shadow-lg hover:shadow-orange-50' }}">

                        {{-- Mesa + badge --}}
                        <div class="flex items-start justify-between mb-3">
                            <span class="text-4xl font-black leading-none
                                {{ $isWaiting ? 'text-yellow-600' : 'text-gray-800' }}">
                                {{ $table->number }}
                            </span>
                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full
                                {{ $isWaiting ? 'bg-yellow-200 text-yellow-700' : 'bg-orange-100 text-orange-600' }}">
                                {{ $isWaiting ? 'Aguardando' : 'Ocupada' }}
                            </span>
                        </div>

                        @if ($order)
                            {{-- Garçom --}}
                            <div class="flex items-center gap-1.5 text-xs text-gray-400 mb-3">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span class="truncate">{{ $order->waiter->name }}</span>
                            </div>

                            <div class="border-t {{ $isWaiting ? 'border-yellow-100' : 'border-gray-100' }} mb-3"></div>

                            {{-- Total + tempo --}}
                            <div class="flex items-end justify-between">
                                <p class="text-lg font-black {{ $isWaiting ? 'text-yellow-700' : 'text-gray-800' }}">
                                    R$ {{ number_format($order->total, 2, ',', '.') }}
                                </p>
                                <p class="text-[11px] text-gray-400">{{ $order->opened_at->format('H:i') }}</p>
                            </div>
                        @endif

                        {{-- CTA --}}
                        <div class="mt-3 pt-0">
                            <div class="w-full py-2 text-center text-sm font-semibold rounded-xl transition-colors
                                {{ $isWaiting
                                    ? 'bg-yellow-400 group-hover:bg-yellow-500 text-yellow-900'
                                    : 'bg-orange-500 group-hover:bg-orange-600 text-white' }}">
                                Ver comanda
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

    </div>
</div>

</body>
</html>
