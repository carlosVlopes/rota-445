@extends('admin.layouts.app')

@section('title', 'Nova Mesa')
@section('page-title', 'Nova Mesa')

@section('header-actions')
    <a href="{{ route('admin.mesas.index') }}"
       class="flex items-center gap-2 h-9 px-4 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-sm font-medium transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Voltar
    </a>
@endsection

@section('content')
<div class="max-w-sm">
    <form method="POST" action="{{ route('admin.mesas.store') }}"
          class="bg-white rounded-2xl border border-gray-100 p-6 space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Número / identificação <span class="text-red-400">*</span></label>
            <input type="text" name="number" value="{{ old('number') }}" required maxlength="10"
                   placeholder="Ex.: 1, 2, VIP…"
                   class="w-full h-10 px-3 rounded-xl border {{ $errors->has('number') ? 'border-red-300' : 'border-gray-200' }} text-sm focus:outline-none focus:border-orange-400">
            @error('number') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            <p class="text-xs text-gray-400 mt-1">A mesa sempre começa com status <strong>Livre</strong>.</p>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
            <a href="{{ route('admin.mesas.index') }}"
               class="h-10 px-5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-sm font-medium transition-colors flex items-center">
                Cancelar
            </a>
            <button type="submit"
                    class="h-10 px-5 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-sm font-medium transition-colors">
                Criar mesa
            </button>
        </div>
    </form>
</div>
@endsection
