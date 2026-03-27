<x-guest-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-50  px-4">
        <!-- Logo -->
        <div class="flex flex-col items-center space-y-2 mb-6">
            <a href="/">
                <x-application-logo class="w-16 h-16 text-gray-700 " />
            </a>
            <h1 class="text-xl font-semibold text-gray-800 ">Confirm Password</h1>
            <p class="text-sm text-gray-500  text-center">
                This is a secure area of the application. Please confirm your password before continuing.
            </p>
        </div>

        <div class="w-full max-w-md bg-white  shadow-lg 2xl p-8 border border-gray-200 ">
            <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
                @csrf

                <div class="space-y-1">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="w-full xl border-gray-300   text-gray-900  focus:border-indigo-500 focus:ring focus:ring-indigo-400 focus:ring-opacity-30 transition" type="password" name="password" required />
                    <x-input-error :messages="$errors->get('password')" class="text-red-500 text-sm mt-1" />
                </div>

                <div class="flex justify-end">
                    <x-primary-button class="w-full">
                        Confirm
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
