<x-guest-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-50  px-4">
        <div class="flex flex-col items-center space-y-2 mb-6">
            <a href="/">
                <x-application-logo class="w-16 h-16 text-gray-700 " />
            </a>
            <h1 class="text-xl font-semibold text-gray-800 ">Forgot your password?</h1>
            <p class="text-sm text-gray-500  text-center max-w-sm">
                No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
            </p>
        </div>

        <div class="w-full max-w-md bg-white  shadow-lg 2xl p-8 border border-gray-200 ">
            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf

                <div class="space-y-1">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="w-full xl border-gray-300   text-gray-900  focus:border-indigo-500 focus:ring focus:ring-indigo-400 focus:ring-opacity-30 transition" type="email" name="email" :value="old('email')" required autofocus />
                    <x-input-error :messages="$errors->get('email')" class="text-red-500 text-sm mt-1" />
                </div>

                <div class="flex justify-end">
                    <x-primary-button class="w-full">Email Password Reset Link</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
