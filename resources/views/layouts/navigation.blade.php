{{-- Sidebar: fixed on desktop, off-canvas drawer below lg. `sidebar` state lives on <body> in layouts.dashboard. --}}
<div
    x-show="sidebar"
    x-cloak
    x-transition.opacity.duration.300ms
    class="fixed inset-0 z-40 bg-black/70 backdrop-blur-sm lg:hidden"
    @click="sidebar = false"
    aria-hidden="true"
></div>

<aside
    id="app-sidebar"
    class="fixed inset-y-0 start-0 z-50 flex w-72 max-w-[85vw] -translate-x-full flex-col border-e border-line bg-pitch-900 transition-[transform,visibility] duration-300 ease-smooth max-lg:invisible lg:w-64 lg:translate-x-0"
    :class="{ '!translate-x-0 !visible shadow-pop': sidebar }"
    aria-label="Portal navigation"
>
    <div class="flex h-16 shrink-0 items-center justify-between gap-3 border-b border-subtle px-4">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 rounded-lg">
            <x-application-logo class="h-10 w-10" />
            <span class="font-display text-lg font-extrabold uppercase leading-none text-white">BOUESTI <span class="text-gold-light">FC</span></span>
        </a>
        <button type="button" class="icon-btn h-10 w-10 lg:hidden" @click="sidebar = false" aria-label="Close navigation">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>

    <nav class="flex-1 space-y-6 overflow-y-auto overscroll-contain px-3 py-5" @click="if ($event.target.closest('a')) sidebar = false">
        <div class="space-y-1">
            <p class="px-3 pb-1 text-[0.6875rem] font-bold uppercase tracking-[0.15em] text-ink-faint">Menu</p>
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                <svg class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955a1.126 1.126 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                {{ __('Dashboard') }}
            </x-nav-link>
            <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')">
                <svg class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                {{ __('Profile') }}
            </x-nav-link>
        </div>

        <div class="space-y-1">
            <p class="px-3 pb-1 text-[0.6875rem] font-bold uppercase tracking-[0.15em] text-ink-faint">Club website</p>
            <x-nav-link :href="route('home')" :active="false">
                <svg class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
                {{ __('View website') }}
            </x-nav-link>
            <x-nav-link :href="route('fixtures')" :active="false">
                <svg class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                {{ __('Fixtures') }}
            </x-nav-link>
        </div>
    </nav>

    <div class="border-t border-subtle p-3">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="side-link w-full" data-loading-text="Logging out…">
                <svg class="h-5 w-5 text-gold" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" /></svg>
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</aside>
