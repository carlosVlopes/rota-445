@extends('admin.layouts.app')

@section('title', 'Usuários')
@section('page-title', 'Usuários')
@section('page-subtitle', $users->total() . ' ' . ($users->total() === 1 ? 'usuário cadastrado' : 'usuários cadastrados'))

@php
    $roleLabels = ['admin' => 'Admin', 'waiter' => 'Garçom', 'cashier' => 'Caixa'];
    $roleColors = [
        'admin'   => 'bg-purple-100 text-purple-600',
        'waiter'  => 'bg-blue-100 text-blue-600',
        'cashier' => 'bg-orange-100 text-orange-600',
    ];
@endphp

@section('header-actions')
    <a href="{{ route('admin.usuarios.create') }}"
       class="flex items-center gap-2 h-9 px-4 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-sm font-medium transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Novo usuário
    </a>
@endsection

@section('content')

{{-- Filtros --}}
<form method="GET" action="{{ route('admin.usuarios.index') }}"
      class="bg-white rounded-2xl border border-gray-100 p-4 mb-4 flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-48">
        <label class="block text-xs text-gray-500 mb-1">Buscar</label>
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Nome ou e-mail…"
               class="w-full h-9 px-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-orange-400">
    </div>
    <div class="min-w-36">
        <label class="block text-xs text-gray-500 mb-1">Perfil</label>
        <select name="role"
                class="w-full h-9 px-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-orange-400 bg-white">
            <option value="">Todos</option>
            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="waiter" {{ request('role') === 'waiter' ? 'selected' : '' }}>Garçom</option>
            <option value="cashier" {{ request('role') === 'cashier' ? 'selected' : '' }}>Caixa</option>
        </select>
    </div>
    <div class="min-w-36">
        <label class="block text-xs text-gray-500 mb-1">Status</label>
        <select name="status"
                class="w-full h-9 px-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-orange-400 bg-white">
            <option value="">Todos</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativos</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativos</option>
        </select>
    </div>
    <button type="submit"
            class="h-9 px-4 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-sm font-medium transition-colors">
        Filtrar
    </button>
    @if (request()->hasAny(['search', 'role', 'status']))
        <a href="{{ route('admin.usuarios.index') }}"
           class="h-9 px-4 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-sm font-medium transition-colors flex items-center">
            Limpar
        </a>
    @endif
</form>

{{-- Tabela --}}
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
    @if ($users->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-gray-400">
            <svg class="w-12 h-12 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <p class="font-medium">Nenhum usuário encontrado</p>
        </div>
    @else
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Usuário</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">E-mail</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">PIN</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Perfil</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($users as $user)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-orange-100 flex items-center justify-center shrink-0">
                                    <span class="text-orange-600 font-bold text-sm">
                                        {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $user->name }}</p>
                                    @if ($user->id === auth()->id())
                                        <p class="text-xs text-orange-400">Você</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-gray-500">{{ $user->email ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-center">
                            @if ($user->pin)
                                <span class="font-mono text-gray-600 bg-gray-100 px-2 py-0.5 rounded-lg text-xs tracking-widest">
                                    {{ $user->pin }}
                                </span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-500' }}">
                                {{ $roleLabels[$user->role] ?? $user->role }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold
                                {{ $user->active ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $user->active ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                                {{ $user->active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.usuarios.edit', $user) }}"
                                   class="p-2 rounded-xl hover:bg-orange-50 text-gray-400 hover:text-orange-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                @if ($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.usuarios.destroy', $user) }}"
                                          onsubmit="return confirm('Remover {{ addslashes($user->name) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-2 rounded-xl hover:bg-red-50 text-gray-400 hover:text-red-500 transition-colors">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if ($users->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
