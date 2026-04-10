@extends('admin.layouts.app')

@section('title', 'Produtos')
@section('page-title', 'Produtos')
@section('page-subtitle', $products->total() . ' ' . ($products->total() === 1 ? 'produto cadastrado' : 'produtos cadastrados'))

@section('header-actions')
    <a href="{{ route('admin.produtos.create') }}"
       class="flex items-center gap-2 h-9 px-4 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-sm font-medium transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Novo produto
    </a>
@endsection

@section('content')

{{-- Filtros --}}
<form method="GET" action="{{ route('admin.produtos.index') }}"
      class="bg-white rounded-2xl border border-gray-100 p-4 mb-4 flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-48">
        <label class="block text-xs text-gray-500 mb-1">Buscar</label>
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Nome ou descrição…"
               class="w-full h-9 px-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-orange-400">
    </div>
    <div class="min-w-40">
        <label class="block text-xs text-gray-500 mb-1">Categoria</label>
        <select name="category"
                class="w-full h-9 px-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-orange-400 bg-white">
            <option value="">Todas</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
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
    @if (request()->hasAny(['search', 'category', 'status']))
        <a href="{{ route('admin.produtos.index') }}"
           class="h-9 px-4 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-sm font-medium transition-colors flex items-center">
            Limpar
        </a>
    @endif
</form>

{{-- Tabela --}}
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
    @if ($products->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-gray-400">
            <svg class="w-12 h-12 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <p class="font-medium">Nenhum produto encontrado</p>
            <p class="text-sm mt-1">Tente ajustar os filtros ou <a href="{{ route('admin.produtos.create') }}" class="text-orange-500 hover:underline">crie um novo produto</a>.</p>
        </div>
    @else
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Produto</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Categoria</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Preço</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Ordem</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($products as $product)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                @if ($product->image)
                                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                                         class="w-10 h-10 rounded-xl object-cover shrink-0">
                                @else
                                    <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-medium text-gray-800">{{ $product->name }}</p>
                                    @if ($product->description)
                                        <p class="text-xs text-gray-400 truncate max-w-xs">{{ $product->description }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-gray-500">{{ $product->category->name }}</td>
                        <td class="px-5 py-3.5 text-right font-semibold text-gray-700">
                            R$ {{ number_format($product->price, 2, ',', '.') }}
                        </td>
                        <td class="px-5 py-3.5 text-center text-gray-400">{{ $product->order }}</td>
                        <td class="px-5 py-3.5 text-center">
                            <form method="POST" action="{{ route('admin.produtos.toggle', $product) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold transition-colors
                                            {{ $product->active
                                                ? 'bg-green-100 text-green-600 hover:bg-red-100 hover:text-red-500'
                                                : 'bg-gray-100 text-gray-400 hover:bg-green-100 hover:text-green-600' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $product->active ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                                    {{ $product->active ? 'Ativo' : 'Inativo' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.produtos.edit', $product) }}"
                                   class="p-2 rounded-xl hover:bg-orange-50 text-gray-400 hover:text-orange-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('admin.produtos.destroy', $product) }}"
                                      onsubmit="return confirm('Remover {{ addslashes($product->name) }}?')">
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

        @if ($products->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $products->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
