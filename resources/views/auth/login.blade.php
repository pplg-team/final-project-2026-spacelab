<x-guest-layout :title="$title" :description="$description">
    <div class="min-h-screen flex flex-col">
        <div class="flex-1 h-dvh flex flex-col items-center justify-center
        bg-gray-50 dark:bg-gray-950 px-4">
            <!-- Logo -->
            <div class="flex flex-col items-center space-y-2 mb-8">
                <a href="/" class="flex items-center justify-center">
                    <x-application-logo class="w-16 h-16 text-gray-700 dark:text-gray-300" />
                </a>
                <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 tracking-tight">
                    Selamat Datang Kembali
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Masuk untuk melanjutkan ke akun anda
                </p>
            </div>

            <!-- Form Card -->
            <div
                class="w-full max-w-md bg-white dark:bg-gray-900 shadow-lg rounded-2xl p-8 border border-gray-200 dark:border-gray-800">
                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email -->
                    <div class="space-y-1">
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-950 text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring focus:ring-indigo-400 focus:ring-opacity-30 transition"
                            type="email" name="email" :value="old('email')" required autofocus />
                        <x-input-error :messages="$errors->get('email')" class="text-red-500 text-sm mt-1" />
                    </div>

                    <!-- Password -->
                    <div x-data="{ show: false }" class="relative">
                        <x-text-input ::type="show ? 'text' : 'password'" name="password" required
                            class="w-full pr-10 rounded-xl border-gray-300 dark:border-gray-700 
                                dark:bg-gray-950 text-gray-900 dark:text-gray-100" />

                        <button type="button" @click="show = !show"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 
                                text-gray-400 hover:text-gray-600">
                            <x-heroicon-o-eye class="h-5 w-5" x-show="!show" />
                            <x-heroicon-o-eye-slash class="h-5 w-5" x-show="show" />
                        </button>
                        </div>                    
                        <!-- Password Error -->
                    <x-input-error :messages="$errors->get('password')" class="text-red-500 text-sm mt-1" />
                                        

                    <!-- Remember + Forgot -->
                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="flex items-center space-x-2">
                            <input id="remember_me" type="checkbox"
                                class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 focus:ring-indigo-500"
                                name="remember">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Ingat saya</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                                class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 transition">
                                Lupa Kata Sandi?
                            </a>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <x-primary-button class="w-full">Masuk</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>

