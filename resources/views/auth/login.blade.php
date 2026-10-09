<x-guest-layout>
    <x-slot name="title">Log in</x-slot>

    <div class="mb-6">
        <h1 class="page-title">Welcome back</h1>
        <p class="mt-2 text-sm text-ink-muted">Sign in to the BOUESTI Sports portal.</p>
    </div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')"/>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')"/>
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username"/>
            <x-input-error :messages="$errors->get('email')" class="mt-2"/>
        </div>

        <!-- Password -->
        <div class="mt-5">
            <x-input-label for="password" :value="__('Password')"/>

            <x-text-input id="password"
                            type="password"
                            name="password"
                            required autocomplete="current-password"/>

            <x-input-error :messages="$errors->get('password')" class="mt-2"/>
        </div>

        <!-- Remember Me -->
        <div class="mt-5 block">
            <label for="remember_me" class="inline-flex min-h-[44px] cursor-pointer items-center">
                <input id="remember_me" type="checkbox" class="form-checkbox" name="remember">
                <span class="ms-3 text-sm text-ink-2">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="mt-6 flex flex-col-reverse gap-4 sm:flex-row sm:items-center sm:justify-end">
            @if (Route::has('password.request'))
                <a class="link text-sm" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="w-full sm:w-auto" data-loading-text="Logging in…">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
