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
            <x-text-input id="password" class="block mt-2 w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-center mt-6">
            <x-primary-button class="w-full justify-center py-3 hover:scale-105 transition-all duration-200 shadow-md shadow-indigo-100">
                {{ __('Masuk') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
