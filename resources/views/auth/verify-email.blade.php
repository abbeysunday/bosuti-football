<x-guest-layout>
    <x-slot name="title">Verify email</x-slot>

    <div class="mb-6">
        <h1 class="page-title">Verify your email</h1>
    </div>
    <div class="mb-5 text-sm leading-relaxed text-ink-2">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-5 rounded-xl border border-brand-light/35 bg-brand/10 px-4 py-3 text-sm font-medium text-[#2fd488]">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button class="w-full sm:w-auto" data-loading-text="Sending…">
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="link text-sm">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
