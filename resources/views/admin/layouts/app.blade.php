<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — Espetaria Rota 445</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('head')
</head>
<body class="min-h-screen bg-gray-50 flex" x-data="{ sidebarOpen: true }">

{{-- Sidebar --}}
<aside
    :class="sidebarOpen ? 'w-64' : 'w-16'"
    class="bg-white border-r border-gray-100 flex flex-col shrink-0 transition-all duration-300 overflow-hidden h-screen sticky top-0">

    {{-- Brand --}}
    <div class="px-4 py-5 border-b border-gray-100 flex items-center gap-3 min-h-[72px]">
        <div class="w-9 h-9 rounded-xl bg-orange-500 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
        </div>
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <p class="text-sm font-bold text-gray-800 leading-tight whitespace-nowrap">Rota 445</p>
            <p class="text-xs text-gray-400">Painel Admin</p>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
        <p x-show="sidebarOpen" class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider px-3 mb-2">Visão Geral</p>

        {{-- Dashboard --}}
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
               {{ request()->routeIs('admin.dashboard') ? 'bg-orange-50 text-orange-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }}"
           :title="!sidebarOpen ? 'Dashboard' : ''">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Dashboard</span>
        </a>

        <div x-show="sidebarOpen" class="pt-3">
            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider px-3 mb-2">Cardápio</p>
        </div>
        <div x-show="!sidebarOpen" class="my-1 border-t border-gray-100"></div>

        {{-- Produtos --}}
        <a href="{{ route('admin.produtos.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
               {{ request()->routeIs('admin.produtos.*') ? 'bg-orange-50 text-orange-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }}"
           :title="!sidebarOpen ? 'Produtos' : ''">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Produtos</span>
        </a>

        {{-- Categorias --}}
        <a href="{{ route('admin.categorias.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
               {{ request()->routeIs('admin.categorias.*') ? 'bg-orange-50 text-orange-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }}"
           :title="!sidebarOpen ? 'Categorias' : ''">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Categorias</span>
        </a>

        <div x-show="sidebarOpen" class="pt-3">
            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider px-3 mb-2">Operações</p>
        </div>
        <div x-show="!sidebarOpen" class="my-1 border-t border-gray-100"></div>

        {{-- Mesas --}}
        <a href="{{ route('admin.mesas.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
               {{ request()->routeIs('admin.mesas.*') ? 'bg-orange-50 text-orange-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }}"
           :title="!sidebarOpen ? 'Mesas' : ''">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M10 10v4m4-4v4M5 6h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Mesas</span>
        </a>

        {{-- Pedidos Fechados --}}
        <a href="{{ route('admin.pedidos.closed') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
               {{ request()->routeIs('admin.pedidos.*') ? 'bg-orange-50 text-orange-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }}"
           :title="!sidebarOpen ? 'Pedidos Fechados' : ''">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Pedidos Fechados</span>
        </a>

        <div x-show="sidebarOpen" class="pt-3">
            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider px-3 mb-2">Configurações</p>
        </div>
        <div x-show="!sidebarOpen" class="my-1 border-t border-gray-100"></div>

        {{-- Usuários --}}
        <a href="{{ route('admin.usuarios.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
               {{ request()->routeIs('admin.usuarios.*') ? 'bg-orange-50 text-orange-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }}"
           :title="!sidebarOpen ? 'Usuários' : ''">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Usuários</span>
        </a>
    </nav>

    {{-- User + Logout --}}
    <div class="px-3 pb-4 pt-3 border-t border-gray-100">
        <div class="flex items-center gap-2.5 px-3 py-2.5">
            <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center shrink-0">
                <span class="text-orange-600 font-bold text-xs">
                    {{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}
                </span>
            </div>
            <div x-show="sidebarOpen" class="min-w-0">
                <p class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-400">Admin</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-full py-2 bg-gray-50 hover:bg-red-50 hover:text-red-500 text-gray-500 font-medium rounded-xl transition-colors text-sm flex items-center justify-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span x-show="sidebarOpen" x-transition>Sair</span>
            </button>
        </form>
    </div>
</aside>

{{-- Main --}}
<div class="flex-1 flex flex-col min-h-screen overflow-x-hidden">

    {{-- Top bar --}}
    <header class="bg-white border-b border-gray-100 px-6 py-4 flex items-center gap-4 sticky top-0 z-10">
        <button @click="sidebarOpen = !sidebarOpen"
                class="p-2 rounded-xl hover:bg-gray-100 text-gray-500 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <div class="flex-1">
            <h1 class="text-lg font-bold text-gray-800">@yield('page-title', 'Dashboard')</h1>
            @hasSection('page-subtitle')
                <p class="text-sm text-gray-400 mt-0.5">@yield('page-subtitle')</p>
            @endif
        </div>

        @yield('header-actions')
    </header>

    {{-- Flash messages --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
            <button @click="show = false" class="text-green-400 hover:text-green-600">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('error') }}
            </div>
            <button @click="show = false" class="text-red-400 hover:text-red-600">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    {{-- Content --}}
    <main class="flex-1 p-6">
        @yield('content')
    </main>
</div>

</body>
</html>
