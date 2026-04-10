@extends('admin.layouts.app')

@section('title', 'Editar Mesa')
@section('page-title', 'Editar Mesa')
@section('page-subtitle', 'Mesa ' . $mesa->number)

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
    <form method="POST" action="{{ route('admin.mesas.update', $mesa) }}"
          class="bg-white rounded-2xl border border-gray-100 p-6 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Número / identificação <span class="text-red-400">*</span></label>
            <input type="text" name="number" value="{{ old('number', $mesa->number) }}" required maxlength="10"
                   class="w-full h-10 px-3 rounded-xl border {{ $errors->has('number') ? 'border-red-300' : 'border-gray-200' }} text-sm focus:outline-none focus:border-orange-400">
            @error('number') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="bg-gray-50 rounded-xl px-4 py-3 flex items-center justify-between">
            <span class="text-sm text-gray-600">Status atual</span>
            @php
                $statusLabels = ['free' => 'Livre', 'occupied' => 'Ocupada', 'waiting_payment' => 'Aguardando'];
                $statusColors = ['free' => 'bg-green-100 text-green-600', 'occupied' => 'bg-orange-100 text-orange-600', 'waiting_payment' => 'bg-yellow-100 text-yellow-600'];
            @endphp
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusColors[$mesa->status->value] }}">
                {{ $statusLabels[$mesa->status->value] }}
            </span>
        </div>

        <div class="bg-gray-50 rounded-xl px-4 py-3 flex items-center justify-between">
            <span class="text-sm text-gray-600">Total de pedidos</span>
            <span class="font-semibold text-gray-700">{{ $mesa->orders_count }}</span>
        </div>

        @if ($mesa->status->value !== 'free')
            <div class="bg-orange-50 border border-orange-100 rounded-xl px-4 py-3 text-sm text-orange-700">
                Esta mesa está ocupada no momento. Apenas o número pode ser editado.
            </div>
        @endif

        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
            @if ($mesa->status->value === 'free')
                <form method="POST" action="{{ route('admin.mesas.destroy', $mesa) }}"
                      onsubmit="return confirm('Remover esta mesa permanentemente?')">
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
            @else
                <div></div>
            @endif

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.mesas.index') }}"
                   class="h-10 px-5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-sm font-medium transition-colors flex items-center">
                    Cancelar
                </a>
                <button type="submit"
                        class="h-10 px-5 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-sm font-medium transition-colors">
                    Salvar
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
