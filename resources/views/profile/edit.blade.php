<x-app-layout>
    <x-slot name="header">
        <h1 class="truncate font-display text-2xl font-extrabold uppercase leading-none text-white">{{ __('Profile') }}</h1>
    </x-slot>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="card p-5 sm:p-8">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="card p-5 sm:p-8">
            @include('profile.partials.update-password-form')
        </div>

        <div class="card border-danger/25 p-5 sm:p-8 xl:col-span-2">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
