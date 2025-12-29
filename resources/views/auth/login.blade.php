<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6 animate-slideUp">
        @csrf
        
        <div class="mb-6 text-center animate-fadeIn">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Masuk SINDAPUR</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Kelola bahan baku dengan mudah</p>
        </div>

        <div>
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input id="username" class="block mt-2 w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" type="text" name="username" :value="old('username')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Kata Sandi')" />
            <div class="relative mt-2">
                <x-text-input id="password" class="block w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 pr-12"
                                type="password"
                                name="password"
                                required autocomplete="current-password" />
                <button type="button" id="toggle-password" class="absolute inset-y-0 right-3 flex items-center text-gray-500 hover:text-gray-700" aria-label="Tampilkan sandi"></button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const input = document.getElementById('password');
                const toggle = document.getElementById('toggle-password');
                if (!input || !toggle) return;
                const eyeOn = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>';
                const eyeOff = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/><path stroke-linecap="round" stroke-linejoin="round" d="M10.477 10.485A3 3 0 0113.5 13.5m3.35 3.356A9.96 9.96 0 0112 19c-4.477 0-8.268-2.943-9.542-7a10.05 10.05 0 012.563-4.263M9.88 4.24A9.956 9.956 0 0112 4c4.477 0 8.268 2.943 9.542 7a9.99 9.99 0 01-3.32 4.568"/></svg>';
                const setState = (hidden) => {
                    input.type = hidden ? 'password' : 'text';
                    toggle.innerHTML = hidden ? eyeOn : eyeOff;
                    toggle.setAttribute('aria-label', hidden ? 'Tampilkan sandi' : 'Sembunyikan sandi');
                };
                setState(true);
                toggle.addEventListener('click', () => {
                    const willShow = input.type === 'password';
                    setState(!willShow);
                });
            });
        </script>

        <div class="flex items-center justify-center mt-6">
            <x-primary-button class="w-full justify-center py-3 hover:scale-105 transition-all duration-200 shadow-md shadow-indigo-100">
                {{ __('Masuk') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
