<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comandas Fechadas — Espetaria Rota 445</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-screen flex bg-gray-50 overflow-hidden">

{{-- Sidebar --}}
<aside class="w-56 bg-white border-r border-gray-100 flex flex-col shrink-0">

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

    <nav class="flex-1 px-3 py-4 space-y-1">
        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider px-3 mb-2">Menu</p>
        <a href="{{ route('cashier.index') }}"
           class="flex items-center gap-2.5 px-3 py-2.5 text-gray-600 hover:bg-gray-50 hover:text-gray-800 rounded-xl font-medium text-sm transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Comandas
        </a>
        <a href="{{ route('cashier.closed') }}"
           class="flex items-center gap-2.5 px-3 py-2.5 bg-orange-50 text-orange-600 rounded-xl font-semibold text-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Fechadas
        </a>
    </nav>

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
            <h1 class="text-xl font-bold text-gray-800">Comandas Fechadas</h1>
            <p class="text-sm text-gray-400 mt-0.5">{{ now()->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</p>
        </div>
        <div class="text-right">
            <p class="text-2xl font-black text-gray-800">{{ $orders->total() }}</p>
            <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">
                {{ $orders->total() === 1 ? 'Comanda' : 'Comandas' }}
            </p>
        </div>
    </header>

    <x-toast />

    {{-- Content --}}
    <div class="flex-1 overflow-y-auto p-8">

        {{-- Filtros --}}
        <form method="GET" action="{{ route('cashier.closed') }}"
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
                <a href="{{ route('cashier.closed') }}"
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-semibold rounded-xl transition-colors">
                    Limpar
                </a>
            @endif
        </form>

        {{-- Tabela --}}
        @if ($orders->isEmpty())
            <div class="flex flex-col items-center justify-center py-24 text-center">
                <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mb-5">
                    <svg class="w-10 h-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                @if (request('mesa') || request('garcom'))
                    <p class="text-gray-500 font-semibold text-lg">Nenhuma comanda encontrada</p>
                    <p class="text-gray-400 text-sm mt-1">Tente ajustar os filtros.</p>
                    <a href="{{ route('cashier.closed') }}" class="mt-4 text-sm text-orange-500 hover:text-orange-600 font-medium">
                        Limpar filtros
                    </a>
                @else
                    <p class="text-gray-500 font-semibold text-lg">Nenhuma comanda fechada ainda</p>
                    <p class="text-gray-400 text-sm mt-1">As comandas fechadas aparecerão aqui.</p>
                @endif
            </div>
        @else
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/60">
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Mesa</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Garçom</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pagamento</th>
                            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Fechada em</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($orders as $order)
                            @php
                                $paymentLabels = [
                                    'dinheiro' => 'Dinheiro',
                                    'debito'   => 'Débito',
                                    'credito'  => 'Crédito',
                                    'pix'      => 'Pix',
                                ];
                            @endphp
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-5 py-3.5">
                                    <span class="font-bold text-gray-800 text-base">{{ $order->table->number }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-gray-600">{{ $order->waiter->name }}</td>
                                <td class="px-5 py-3.5">
                                    @if ($order->payment_method)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            {{ $paymentLabels[$order->payment_method] ?? $order->payment_method }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">—</span>
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
                                    <a href="{{ route('cashier.closed.show', $order) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 hover:bg-orange-50 hover:text-orange-600 text-gray-600 text-xs font-semibold rounded-lg transition-colors">
                                        Ver
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginação --}}
            @if ($orders->hasPages())
                <div class="mt-5 flex items-center justify-between text-sm text-gray-500">
                    <p>
                        Mostrando {{ $orders->firstItem() }}–{{ $orders->lastItem() }}
                        de {{ $orders->total() }} comandas
                    </p>
                    <div class="flex items-center gap-1">
                        @if ($orders->onFirstPage())
                            <span class="px-3 py-1.5 rounded-lg text-gray-300 cursor-not-allowed">Anterior</span>
                        @else
                            <a href="{{ $orders->previousPageUrl() }}"
                               class="px-3 py-1.5 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors font-medium text-gray-600">
                                Anterior
                            </a>
                        @endif

                        @if ($orders->hasMorePages())
                            <a href="{{ $orders->nextPageUrl() }}"
                               class="px-3 py-1.5 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors font-medium text-gray-600">
                                Próxima
                            </a>
                        @else
                            <span class="px-3 py-1.5 rounded-lg text-gray-300 cursor-not-allowed">Próxima</span>
                        @endif
                    </div>
                </div>
            @endif
        @endif

    </div>
</div>

</body>
</html>
