@extends('admin.layouts.app')

@section('title', 'Editar Produto')
@section('page-title', 'Editar Produto')
@section('page-subtitle', $product->name)

@section('header-actions')
    <a href="{{ route('admin.produtos.index') }}"
       class="flex items-center gap-2 h-9 px-4 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-sm font-medium transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Voltar
    </a>
@endsection

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.produtos.update', $product) }}" enctype="multipart/form-data"
          class="bg-white rounded-2xl border border-gray-100 p-6 space-y-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-2 gap-5">
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nome <span class="text-red-400">*</span></label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                       class="w-full h-10 px-3 rounded-xl border {{ $errors->has('name') ? 'border-red-300' : 'border-gray-200' }} text-sm focus:outline-none focus:border-orange-400">
                @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Categoria <span class="text-red-400">*</span></label>
                <select name="category_id" required
                        class="w-full h-10 px-3 rounded-xl border {{ $errors->has('category_id') ? 'border-red-300' : 'border-gray-200' }} text-sm focus:outline-none focus:border-orange-400 bg-white">
                    <option value="">Selecione…</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Preço (R$) <span class="text-red-400">*</span></label>
                <input type="number" name="price" value="{{ old('price', $product->price) }}" min="0" step="0.01" required
                       class="w-full h-10 px-3 rounded-xl border {{ $errors->has('price') ? 'border-red-300' : 'border-gray-200' }} text-sm focus:outline-none focus:border-orange-400">
                @error('price') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Descrição</label>
                <textarea name="description" rows="3"
                          class="w-full px-3 py-2 rounded-xl border {{ $errors->has('description') ? 'border-red-300' : 'border-gray-200' }} text-sm focus:outline-none focus:border-orange-400 resize-none">{{ old('description', $product->description) }}</textarea>
                @error('description') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Ordem de exibição</label>
                <input type="number" name="order" value="{{ old('order', $product->order) }}" min="0"
                       class="w-full h-10 px-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-orange-400">
            </div>

            <div class="flex items-end pb-0.5">
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <div x-data="{ checked: {{ old('active', $product->active) ? 'true' : 'false' }} }"
                         @click="checked = !checked"
                         :class="checked ? 'bg-orange-500' : 'bg-gray-200'"
                         class="relative w-11 h-6 rounded-full transition-colors shrink-0">
                        <div :class="checked ? 'translate-x-5' : 'translate-x-1'"
                             class="absolute top-1 w-4 h-4 bg-white rounded-full shadow transition-transform"></div>
                        <input type="hidden" name="active" :value="checked ? '1' : '0'">
                    </div>
                    <span class="text-sm font-medium text-gray-700">Produto ativo</span>
                </label>
            </div>

            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Imagem</label>
                @if ($product->image)
                    <div class="flex items-center gap-3 mb-2">
                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                             class="w-16 h-16 rounded-xl object-cover border border-gray-100">
                        <p class="text-xs text-gray-400">Imagem atual. Envie uma nova para substituir.</p>
                    </div>
                @endif
                <input type="file" name="image" accept="image/*"
                       class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-orange-50 file:text-orange-600 file:font-medium hover:file:bg-orange-100">
                @error('image') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
            <form method="POST" action="{{ route('admin.produtos.destroy', $product) }}"
                  onsubmit="return confirm('Remover este produto permanentemente?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="h-10 px-4 bg-red-50 hover:bg-red-100 text-red-500 rounded-xl text-sm font-medium transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Remover
                </button>
            </form>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.produtos.index') }}"
                   class="h-10 px-5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-sm font-medium transition-colors flex items-center">
                    Cancelar
                </a>
                <button type="submit"
                        class="h-10 px-5 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-sm font-medium transition-colors">
                    Salvar alterações
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
