<x-guest-layout>
    <x-slot name="title">Confirm password</x-slot>

    <div class="mb-6">
        <h1 class="page-title">Confirm your password</h1>
    </div>
    <div class="mb-5 text-sm leading-relaxed text-ink-2">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')"/>

            <x-text-input id="password"
                            type="password"
                            name="password"
                            required autocomplete="current-password"/>

            <x-input-error :messages="$errors->get('password')" class="mt-2"/>
        </div>

        <div class="mt-6 flex sm:justify-end">
            <x-primary-button class="w-full sm:w-auto" data-loading-text="Confirming…">
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
