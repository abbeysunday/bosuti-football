<x-app-layout>
    <x-slot name="header">
        <h1 class="truncate font-display text-2xl font-extrabold uppercase leading-none text-white">{{ __('Dashboard') }}</h1>
    </x-slot>

    <div class="space-y-6 sm:space-y-8">
        {{-- Welcome --}}
        <section class="card relative overflow-hidden p-6 sm:p-8">
            <div class="pointer-events-none absolute -end-16 -top-16 h-56 w-56 rounded-full bg-brand/20 blur-3xl" aria-hidden="true"></div>
            <p class="eyebrow">Welcome back</p>
            <h2 class="page-title mt-3">{{ Auth::user()->name }}</h2>
            <p class="mt-3 max-w-xl text-ink-2">{{ __("You're logged in to the BOUESTI Sports portal.") }}</p>
            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('profile.edit') }}" class="btn btn-primary">Manage profile</a>
                <a href="{{ route('home') }}" class="btn btn-outline">View website</a>
            </div>
        </section>

        {{-- Quick links: 1 column on phones, 2 on tablets, 4 on desktop --}}
        <section aria-labelledby="quick-links-title">
            <h2 id="quick-links-title" class="card-title mb-4">Quick links</h2>
            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ([
                    ['Fixtures', 'Upcoming matches and results', route('fixtures'), 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5'],
                    ['Squad', 'Players and positions', route('squad'), 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z'],
                    ['News', 'Latest club stories', route('news'), 'M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z'],
                    ['League table', 'Current standings', route('league.table'), 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z'],
                ] as [$label, $description, $href, $icon])
                    <a href="{{ $href }}" class="card group flex items-center gap-4 p-4 transition duration-200 hover:border-line-strong hover:bg-pitch-800">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-lg bg-brand/15 text-gold-light">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" /></svg>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block font-semibold text-white">{{ $label }}</span>
                            <span class="block text-xs text-ink-muted">{{ $description }}</span>
                        </span>
                        <svg class="h-4 w-4 shrink-0 text-ink-faint transition group-hover:translate-x-0.5 group-hover:text-gold-light" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" /></svg>
                    </a>
                @endforeach
            </div>
        </section>

        {{-- Activity: empty until the club backend records activity --}}
        <section aria-labelledby="activity-title">
            <h2 id="activity-title" class="card-title mb-4">Recent activity</h2>
            <x-empty-state title="No activity yet" description="Applications, fixtures and news you manage will appear here once the club admin tools are connected.">
                <x-slot name="action">
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline">Complete your profile</a>
                </x-slot>
            </x-empty-state>
        </section>
    </div>
</x-app-layout>
