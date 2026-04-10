<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Espetaria Rota 445</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center p-4">

<div class="w-full max-w-sm">

    {{-- Logo / Nome --}}
    <div class="text-center mb-8">
        <img src="{{ asset('images/logo.png') }}" alt="Rota 445" class="w-24 h-24 mx-auto mb-3 object-contain">
        <h1 class="text-2xl font-semibold text-gray-800">Espetaria Rota 445</h1>
        <p class="text-gray-500 text-sm mt-1">Sistema de pedidos</p>
    </div>

    <x-toast />

    {{-- Aba PIN / Admin --}}
    <div x-data="{ tab: 'pin' }" class="bg-white rounded-2xl shadow-sm overflow-hidden">

        {{-- Tabs --}}
        <div class="flex border-b border-gray-100">
            <button type="button" @click="tab = 'pin'"
                :class="tab === 'pin' ? 'border-b-2 border-orange-500 text-orange-600' : 'text-gray-400'"
                class="flex-1 py-3 text-sm font-medium transition-colors">
                Entrar com PIN
            </button>
            <button type="button" @click="tab = 'admin'"
                :class="tab === 'admin' ? 'border-b-2 border-orange-500 text-orange-600' : 'text-gray-400'"
                class="flex-1 py-3 text-sm font-medium transition-colors">
                Admin
            </button>
        </div>

        {{-- Login por PIN --}}
        <div x-show="tab === 'pin'" class="p-6">
            <form method="POST" action="{{ route('login.post') }}" x-data="pinPad()">
                @csrf

                {{-- Display do PIN --}}
                <div class="flex justify-center gap-3 mb-6">
                    <template x-for="i in 4">
                        <div class="w-12 h-12 rounded-xl border-2 flex items-center justify-center text-xl font-bold transition-all"
                            :class="pin.length >= i ? 'border-orange-500 bg-orange-50 text-orange-600' : 'border-gray-200 text-gray-300'">
                            <span x-show="pin.length >= i">•</span>
                            <span x-show="pin.length < i" class="text-gray-200">—</span>
                        </div>
                    </template>
                </div>

                <input type="hidden" name="pin" :value="pin">

                {{-- Teclado numérico --}}
                <div class="grid grid-cols-3 gap-3">
                    <template x-for="n in [1,2,3,4,5,6,7,8,9]">
                        <button type="button"
                            @click="addDigit(n)"
                            :disabled="pin.length >= 4"
                            class="h-14 rounded-xl bg-gray-50 hover:bg-orange-50 hover:text-orange-600 text-gray-700 text-xl font-medium transition-colors disabled:opacity-40">
                            <span x-text="n"></span>
                        </button>
                    </template>
                    {{-- Limpar --}}
                    <button type="button" @click="clear()"
                        class="h-14 rounded-xl bg-gray-50 hover:bg-red-50 hover:text-red-500 text-gray-400 text-sm font-medium transition-colors">
                        Limpar
                    </button>
                    {{-- 0 --}}
                    <button type="button" @click="addDigit(0)" :disabled="pin.length >= 4"
                        class="h-14 rounded-xl bg-gray-50 hover:bg-orange-50 hover:text-orange-600 text-gray-700 text-xl font-medium transition-colors disabled:opacity-40">
                        0
                    </button>
                    {{-- Apagar --}}
                    <button type="button" @click="removeDigit()"
                        class="h-14 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-400 text-lg transition-colors">
                        ⌫
                    </button>
                </div>

                {{-- Botão entrar (aparece quando PIN completo) --}}
                <button type="submit" x-show="pin.length === 4"
                    class="w-full mt-4 h-12 bg-orange-500 hover:bg-orange-600 text-white rounded-xl font-medium transition-colors">
                    Entrar
                </button>
            </form>
        </div>

        {{-- Login Admin --}}
        <div x-show="tab === 'admin'" class="p-6">
            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm text-gray-600 mb-1">E-mail</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full h-11 px-3 rounded-xl border border-gray-200 focus:outline-none focus:border-orange-400 text-sm">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Senha</label>
                    <input type="password" name="password" required
                        class="w-full h-11 px-3 rounded-xl border border-gray-200 focus:outline-none focus:border-orange-400 text-sm">
                </div>
                <button type="submit"
                    class="w-full h-12 bg-orange-500 hover:bg-orange-600 text-white rounded-xl font-medium transition-colors">
                    Entrar como Admin
                </button>
            </form>
        </div>

    </div>{{-- fim card --}}
</div>

<script>
function pinPad() {
    return {
        pin: '',
        addDigit(n) {
            if (this.pin.length < 4) this.pin += String(n);
        },
        removeDigit() {
            this.pin = this.pin.slice(0, -1);
        },
        clear() {
            this.pin = '';
        },
    }
}
</script>

</body>
</html>
