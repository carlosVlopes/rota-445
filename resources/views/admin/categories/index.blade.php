@extends('admin.layouts.app')

@section('title', 'Categorias')
@section('page-title', 'Categorias')
@section('page-subtitle', $categories->total() . ' ' . ($categories->total() === 1 ? 'categoria cadastrada' : 'categorias cadastradas'))

@section('header-actions')
    <a href="{{ route('admin.categorias.create') }}"
       class="flex items-center gap-2 h-9 px-4 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-sm font-medium transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nova categoria
    </a>
@endsection

@section('content')

{{-- Filtros --}}
<form method="GET" action="{{ route('admin.categorias.index') }}"
      class="bg-white rounded-2xl border border-gray-100 p-4 mb-4 flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-48">
        <label class="block text-xs text-gray-500 mb-1">Buscar</label>
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Nome da categoria…"
               class="w-full h-9 px-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-orange-400">
    </div>
    <div class="min-w-36">
        <label class="block text-xs text-gray-500 mb-1">Status</label>
        <select name="status"
                class="w-full h-9 px-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-orange-400 bg-white">
            <option value="">Todos</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativas</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativas</option>
        </select>
    </div>
    <button type="submit"
            class="h-9 px-4 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-sm font-medium transition-colors">
        Filtrar
    </button>
    @if (request()->hasAny(['search', 'status']))
        <a href="{{ route('admin.categorias.index') }}"
           class="h-9 px-4 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-sm font-medium transition-colors flex items-center">
            Limpar
        </a>
    @endif
</form>

{{-- Tabela --}}
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
    @if ($categories->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-gray-400">
            <svg class="w-12 h-12 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            <p class="font-medium">Nenhuma categoria encontrada</p>
            <p class="text-sm mt-1">
                <a href="{{ route('admin.categorias.create') }}" class="text-orange-500 hover:underline">Crie a primeira categoria</a>.
            </p>
        </div>
    @else
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Nome</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Produtos</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Ordem</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($categories as $category)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-3.5 font-medium text-gray-800">{{ $category->name }}</td>
                        <td class="px-5 py-3.5 text-center text-gray-500">
                            <span class="inline-flex items-center gap-1">
                                <span class="font-semibold">{{ $category->products_count }}</span>
                                <span class="text-gray-400">{{ $category->products_count === 1 ? 'produto' : 'produtos' }}</span>
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-center text-gray-400">{{ $category->order }}</td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold
                                {{ $category->active ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $category->active ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                                {{ $category->active ? 'Ativa' : 'Inativa' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.categorias.edit', $category) }}"
                                   class="p-2 rounded-xl hover:bg-orange-50 text-gray-400 hover:text-orange-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('admin.categorias.destroy', $category) }}"
                                      onsubmit="return confirm('Remover {{ addslashes($category->name) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="p-2 rounded-xl hover:bg-red-50 text-gray-400 hover:text-red-500 transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if ($categories->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $categories->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
