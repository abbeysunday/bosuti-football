{{-- Account dropdown used in the top bar of the user dashboard and the admin panel. --}}
<x-dropdown align="right" width="48">
    <x-slot name="trigger">
        <button type="button" class="flex min-h-[44px] items-center gap-2 rounded-full border border-line bg-pitch-850 py-1 pe-3 ps-1 text-sm font-semibold text-ink transition hover:border-line-strong" aria-haspopup="menu" aria-expanded="false">
            <span class="grid h-9 w-9 place-items-center rounded-full bg-brand font-display text-base font-extrabold uppercase text-white" aria-hidden="true">{{ mb_substr(Auth::user()->name, 0, 1) }}</span>
            <span class="hidden max-w-[10rem] truncate sm:block">{{ Auth::user()->name }}</span>
            <svg class="h-4 w-4 text-ink-muted" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" /></svg>
        </button>
    </x-slot>

    <x-slot name="content">
        <div class="border-b border-subtle px-3 pb-2.5 pt-2">
            <p class="truncate text-sm font-semibold text-white">{{ Auth::user()->name }}</p>
            <p class="truncate text-xs text-ink-muted">{{ Auth::user()->email }}</p>
        </div>
        <div class="pt-1.5">
            @if (Auth::user()->isAdmin())
                <x-dropdown-link :href="route('admin.dashboard')">{{ __('Admin panel') }}</x-dropdown-link>
            @endif
            <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
            <x-dropdown-link :href="route('home')">{{ __('View website') }}</x-dropdown-link>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-dropdown-link :href="route('logout')"
                        onclick="event.preventDefault();
                                    this.closest('form').submit();">
                    {{ __('Log Out') }}
                </x-dropdown-link>
            </form>
        </div>
    </x-slot>
</x-dropdown>
