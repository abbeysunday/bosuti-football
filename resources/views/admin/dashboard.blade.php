@php
    $icon = [
        'shield' => 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z',
        'users' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
        'calendar' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5',
        'check' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'trophy' => 'M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0',
        'news' => 'M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z',
    ];
@endphp

<x-layouts.admin title="Dashboard">
    <x-admin.page-header title="Dashboard" description="Overview of BOUESTI internal football: teams, players, matches and content.">
        <x-slot name="actions">
            <a href="{{ route('admin.fixtures.create') }}" class="btn btn-primary">New fixture</a>
            <a href="{{ route('admin.players.create') }}" class="btn btn-outline">Add player</a>
        </x-slot>
    </x-admin.page-header>

    {{-- 1 column on phones, 2 on tablets, 3 on desktop --}}
    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
        <x-admin.stat-card label="Total teams" :value="$stats['teams']" :href="route('admin.teams.index')" :icon="$icon['shield']" />
        <x-admin.stat-card label="Total players" :value="$stats['players']" :href="route('admin.players.index')" :icon="$icon['users']" />
        <x-admin.stat-card label="Upcoming matches" :value="$stats['upcoming']" :href="route('admin.fixtures.index', ['status' => 'scheduled'])" :icon="$icon['calendar']" />
        <x-admin.stat-card label="Completed matches" :value="$stats['completed']" :href="route('admin.fixtures.index', ['status' => 'completed'])" :icon="$icon['check']" />
        <x-admin.stat-card label="Active competitions" :value="$stats['competitions']" :href="route('admin.competitions.index')" :icon="$icon['trophy']" />
        <x-admin.stat-card label="Published news" :value="$stats['news']" :href="route('admin.news.index', ['state' => 'published'])" :icon="$icon['news']" />
    </div>

    @if ($pendingApplications)
        <x-alert type="info" class="mt-6" title="{{ $pendingApplications }} new trial {{ Str::plural('application', $pendingApplications) }}">
            <a href="{{ route('admin.trial-applications.index', ['status' => 'pending']) }}" class="link">Review applications</a>
        </x-alert>
    @endif

    @if ($unreadMessages)
        <x-alert type="info" class="mt-3" title="{{ $unreadMessages }} unread {{ Str::plural('message', $unreadMessages) }}">
            <a href="{{ route('admin.contact-messages.index', ['status' => 'new']) }}" class="link">Open messages</a>
        </x-alert>
    @endif

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <x-admin.section title="Upcoming fixtures">
            <x-slot name="actions"><a href="{{ route('admin.fixtures.index') }}" class="link text-sm">All fixtures</a></x-slot>
            @forelse ($upcomingFixtures as $fixture)
                @include('admin.fixtures.partials.row', ['fixture' => $fixture])
            @empty
                <x-empty-state title="No upcoming fixtures" description="Schedule the next internal match to see it here.">
                    <x-slot name="action"><a href="{{ route('admin.fixtures.create') }}" class="btn btn-primary">Create fixture</a></x-slot>
                </x-empty-state>
            @endforelse
        </x-admin.section>

        <x-admin.section title="Recent results">
            <x-slot name="actions"><a href="{{ route('admin.fixtures.index', ['status' => 'completed']) }}" class="link text-sm">All results</a></x-slot>
            @forelse ($recentResults as $fixture)
                @include('admin.fixtures.partials.row', ['fixture' => $fixture])
            @empty
                <x-empty-state title="No results yet" description="Open a fixture and enter the final score to record a result." />
            @endforelse
        </x-admin.section>
    </div>

    <x-admin.section title="Recent trial applications" class="mt-6">
        <x-slot name="actions"><a href="{{ route('admin.trial-applications.index') }}" class="link text-sm">All applications</a></x-slot>
        @forelse ($recentApplications as $application)
            <a href="{{ route('admin.trial-applications.show', $application) }}" class="-mx-2 flex min-h-[56px] items-center gap-3 rounded-lg px-2 py-2 transition hover:bg-white/[0.03]">
                <span class="min-w-0 flex-1">
                    <span class="block truncate font-semibold text-white">{{ $application->full_name }}</span>
                    <span class="block truncate text-xs text-ink-muted">{{ $application->position_label ?? 'Any position' }} · {{ $application->created_at->diffForHumans() }}</span>
                </span>
                <x-admin.status-badge :status="$application->status" />
            </a>
        @empty
            <x-empty-state title="No applications yet" description="Applications submitted on the Join the Team page appear here." />
        @endforelse
    </x-admin.section>
</x-layouts.admin>
