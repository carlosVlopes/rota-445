@extends('admin.layouts.app')

@section('title', 'Editar Usuário')
@section('page-title', 'Editar Usuário')
@section('page-subtitle', $user->name)

@section('header-actions')
    <a href="{{ route('admin.usuarios.index') }}"
       class="flex items-center gap-2 h-9 px-4 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-sm font-medium transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Voltar
    </a>
@endsection

@section('content')
<div class="max-w-lg" x-data="{ role: '{{ old('role', $user->role) }}' }">
    <form method="POST" action="{{ route('admin.usuarios.update', $user) }}"
          class="bg-white rounded-2xl border border-gray-100 p-6 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nome completo <span class="text-red-400">*</span></label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                   class="w-full h-10 px-3 rounded-xl border {{ $errors->has('name') ? 'border-red-300' : 'border-gray-200' }} text-sm focus:outline-none focus:border-orange-400">
            @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Perfil <span class="text-red-400">*</span></label>
            <select name="role" x-model="role" required
                    class="w-full h-10 px-3 rounded-xl border {{ $errors->has('role') ? 'border-red-300' : 'border-gray-200' }} text-sm focus:outline-none focus:border-orange-400 bg-white">
                <option value="waiter" {{ old('role', $user->role) === 'waiter' ? 'selected' : '' }}>Garçom</option>
                <option value="cashier" {{ old('role', $user->role) === 'cashier' ? 'selected' : '' }}>Caixa</option>
                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
            @error('role') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div x-show="role !== 'admin'">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">PIN (4 dígitos)</label>
            <input type="text" name="pin" value="{{ old('pin', $user->pin) }}" maxlength="4" pattern="\d{4}"
                   placeholder="Ex.: 1234"
                   class="w-full h-10 px-3 rounded-xl border {{ $errors->has('pin') ? 'border-red-300' : 'border-gray-200' }} text-sm focus:outline-none focus:border-orange-400 font-mono tracking-widest">
            @error('pin') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div x-show="role !== 'waiter'">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">E-mail</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                   class="w-full h-10 px-3 rounded-xl border {{ $errors->has('email') ? 'border-red-300' : 'border-gray-200' }} text-sm focus:outline-none focus:border-orange-400">
            @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nova senha <span class="text-xs text-gray-400 font-normal">(deixe vazio para manter)</span></label>
            <input type="password" name="password"
                   class="w-full h-10 px-3 rounded-xl border {{ $errors->has('password') ? 'border-red-300' : 'border-gray-200' }} text-sm focus:outline-none focus:border-orange-400">
            @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="flex items-center gap-2.5 cursor-pointer">
                <div x-data="{ checked: {{ old('active', $user->active) ? 'true' : 'false' }} }"
                     @click="checked = !checked"
                     :class="checked ? 'bg-orange-500' : 'bg-gray-200'"
                     class="relative w-11 h-6 rounded-full transition-colors shrink-0">
                    <div :class="checked ? 'translate-x-5' : 'translate-x-1'"
                         class="absolute top-1 w-4 h-4 bg-white rounded-full shadow transition-transform"></div>
                    <input type="hidden" name="active" :value="checked ? '1' : '0'">
                </div>
                <span class="text-sm font-medium text-gray-700">Usuário ativo</span>
            </label>
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
            @if ($user->id !== auth()->id())
                <form method="POST" action="{{ route('admin.usuarios.destroy', $user) }}"
                      onsubmit="return confirm('Remover este usuário permanentemente?')">
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
                <a href="{{ route('admin.usuarios.index') }}"
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
