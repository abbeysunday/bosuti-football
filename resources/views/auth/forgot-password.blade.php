<x-guest-layout>
    <x-slot name="title">Forgot password</x-slot>

    <div class="mb-6">
        <h1 class="page-title">Reset your password</h1>
    </div>
    <div class="mb-5 text-sm leading-relaxed text-ink-2">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')"/>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')"/>
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus/>
            <x-input-error :messages="$errors->get('email')" class="mt-2"/>
        </div>

        <div class="mt-6 flex flex-col-reverse gap-4 sm:flex-row sm:items-center sm:justify-end">
            <x-primary-button class="w-full sm:w-auto" data-loading-text="Sending…">
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
