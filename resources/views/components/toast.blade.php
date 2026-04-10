@php
    $message = session('success') ?? session('error') ?? ($errors->any() ? $errors->first() : null);
    $type = session('success') ? 'success' : 'error';
@endphp

@if ($message)
<div
    x-data="{
        show: true,
        progress: 100,
        duration: 4000,
        timer: null,
        interval: null,
        init() {
            this.startTimer();
        },
        startTimer() {
            const step = 50;
            const decrement = (step / this.duration) * 100;
            this.interval = setInterval(() => {
                this.progress -= decrement;
                if (this.progress <= 0) {
                    this.dismiss();
                }
            }, step);
        },
        dismiss() {
            clearInterval(this.interval);
            this.show = false;
        }
    }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-x-full"
    x-transition:enter-end="opacity-100 translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-x-0"
    x-transition:leave-end="opacity-0 translate-x-full"
    class="fixed top-4 right-4 z-[100] w-80 max-w-[calc(100vw-2rem)] shadow-lg rounded-2xl overflow-hidden
        {{ $type === 'success' ? 'bg-white border border-green-100' : 'bg-white border border-red-100' }}"
    role="alert"
>
    {{-- Top colored bar --}}
    <div class="h-1 {{ $type === 'success' ? 'bg-green-500' : 'bg-red-500' }}"></div>

    <div class="px-4 py-3 flex items-start gap-3">

        {{-- Icon --}}
        <div class="shrink-0 mt-0.5">
            @if ($type === 'success')
                <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
            @else
                <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
            @endif
        </div>

        {{-- Message --}}
        <div class="flex-1 min-w-0">
            <p class="text-xs font-semibold uppercase tracking-wider mb-0.5
                {{ $type === 'success' ? 'text-green-600' : 'text-red-600' }}">
                {{ $type === 'success' ? 'Sucesso' : 'Erro' }}
            </p>
            <p class="text-sm text-gray-700 leading-snug">{{ $message }}</p>
        </div>

        {{-- Close button --}}
        <button @click="dismiss()"
            class="shrink-0 w-6 h-6 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors mt-0.5">
            <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </button>
    </div>

    {{-- Progress bar --}}
    <div class="h-0.5 {{ $type === 'success' ? 'bg-green-100' : 'bg-red-100' }}">
        <div class="h-full transition-none {{ $type === 'success' ? 'bg-green-400' : 'bg-red-400' }}"
            :style="'width: ' + progress + '%'"></div>
    </div>
</div>
@endif
