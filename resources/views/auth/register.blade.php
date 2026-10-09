<x-guest-layout>
    <x-slot name="title">Create account</x-slot>

    <div class="mb-6">
        <h1 class="page-title">Create your account</h1>
        <p class="mt-2 text-sm text-ink-muted">Join the BOUESTI Sports portal.</p>
    </div>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')"/>
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name"/>
            <x-input-error :messages="$errors->get('name')" class="mt-2"/>
        </div>

        <!-- Email Address -->
        <div class="mt-5">
            <x-input-label for="email" :value="__('Email')"/>
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username"/>
            <x-input-error :messages="$errors->get('email')" class="mt-2"/>
        </div>

        <!-- Password -->
        <div class="mt-5">
            <x-input-label for="password" :value="__('Password')"/>

            <x-text-input id="password"
                            type="password"
                            name="password"
                            required autocomplete="new-password"/>

            <x-input-error :messages="$errors->get('password')" class="mt-2"/>
        </div>

        <!-- Confirm Password -->
        <div class="mt-5">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')"/>

            <x-text-input id="password_confirmation"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password"/>

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2"/>
        </div>

        <div class="mt-6 flex flex-col-reverse gap-4 sm:flex-row sm:items-center sm:justify-end">
            <a class="link text-sm" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="w-full sm:w-auto" data-loading-text="Creating account…">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
