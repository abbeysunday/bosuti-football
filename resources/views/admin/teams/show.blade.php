<x-layouts.admin :title="$team->name">
    <x-admin.page-header :title="$team->name" :back="route('admin.teams.index')" :description="$team->description ? Str::limit($team->description, 160) : null">
        <x-slot name="actions">
            <a href="{{ route('admin.players.create', ['team' => $team->id]) }}" class="btn btn-primary">Add player</a>
            <a href="{{ route('admin.teams.edit', $team) }}" class="btn btn-outline">Edit team</a>
            <a href="{{ route('teams.show', $team) }}" class="btn btn-ghost" target="_blank" rel="noopener">Public page</a>
        </x-slot>
    </x-admin.page-header>

    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 xl:grid-cols-7">
        @foreach (['played' => 'Played', 'won' => 'Won', 'drawn' => 'Drawn', 'lost' => 'Lost', 'goals_for' => 'Goals for', 'goals_against' => 'Goals against', 'clean_sheets' => 'Clean sheets'] as $key => $label)
            <div class="card p-4">
                <p class="font-display text-3xl font-extrabold leading-none text-white">{{ $stats[$key] }}</p>
                <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-ink-muted">{{ $label }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.4fr_1fr]">
        <x-admin.section :title="'Squad (' . $players->count() . ')'">
            @if ($players->isEmpty())
                <x-empty-state title="No players yet" description="Add {{ $team->name }} players so they can be named in line-ups and match events.">
                    <x-slot name="action"><a href="{{ route('admin.players.create', ['team' => $team->id]) }}" class="btn btn-primary">Add first player</a></x-slot>
                </x-empty-state>
            @else
                <ul class="divide-y divide-white/5">
                    @foreach ($players as $player)
                        <li class="flex min-h-[56px] items-center gap-3 py-2">
                            <span class="w-8 text-center font-display text-lg font-extrabold text-gold-light">{{ $player->jersey_number ?? '—' }}</span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate font-semibold text-white">{{ $player->full_name }}
                                    @if ($player->is_captain)<x-admin.badge color="gold" class="ms-1">C</x-admin.badge>@endif
                                    @unless ($player->is_active)<x-admin.badge class="ms-1">Inactive</x-admin.badge>@endunless
                                </span>
                                <span class="text-xs text-ink-muted">{{ $player->position_label }}</span>
                            </span>
                            <x-admin.row-actions :name="$player->full_name" :edit="route('admin.players.edit', $player)" :delete="route('admin.players.destroy', $player)" confirm="Their match history is kept." />
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-admin.section>

        <x-admin.section title="Recent & upcoming matches">
            <x-slot name="actions"><a href="{{ route('admin.fixtures.index', ['team' => $team->id]) }}" class="link text-sm">All</a></x-slot>
            @forelse ($fixtures as $fixture)
                @include('admin.fixtures.partials.row', ['fixture' => $fixture])
            @empty
                <x-empty-state title="No fixtures" description="{{ $team->name }} has not been drawn in any fixture yet." />
            @endforelse
        </x-admin.section>
    </div>
</x-layouts.admin>
