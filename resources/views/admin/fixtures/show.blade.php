@php
    $teams = collect([$fixture->homeTeam, $fixture->awayTeam]);
    $lineups = $fixture->lineups->groupBy('team_id');
@endphp

<x-layouts.admin :title="$fixture->title">
    <x-admin.page-header :title="$fixture->title" :back="route('admin.fixtures.index')"
        :description="collect([$fixture->competition?->name, $fixture->matchday ? 'Matchday ' . $fixture->matchday : null, $fixture->match_date->format('l d F Y'), $fixture->kickoff_label, $fixture->venue])->filter()->implode(' · ')">
        <x-slot name="actions">
            <a href="{{ route('admin.fixtures.edit', $fixture) }}" class="btn btn-outline">Edit fixture</a>
            <a href="{{ route('matches.show', $fixture) }}" class="btn btn-ghost" target="_blank" rel="noopener">Public page</a>
        </x-slot>
    </x-admin.page-header>

    {{-- Scoreboard --}}
    <section class="card mb-6 grid grid-cols-[1fr_auto_1fr] items-center gap-3 p-5 sm:p-8" aria-label="Scoreboard">
        <div class="flex flex-col items-center gap-2 text-center">
            <x-admin.team :team="$fixture->homeTeam" size="h-14 w-14" class="flex-col !gap-2 text-center [&>span:last-child]:whitespace-normal [&>span:last-child]:text-base sm:[&>span:last-child]:text-lg" />
            <span class="text-xs uppercase tracking-wide text-ink-faint">Home</span>
        </div>
        <div class="text-center">
            <p class="font-display text-5xl font-black leading-none sm:text-6xl {{ $fixture->hasScore() ? 'text-gold-light' : 'text-ink-muted' }}">{{ $fixture->scoreline }}</p>
            <div class="mt-2"><x-admin.status-badge :status="$fixture->status" /></div>
        </div>
        <div class="flex flex-col items-center gap-2 text-center">
            <x-admin.team :team="$fixture->awayTeam" size="h-14 w-14" class="flex-col !gap-2 text-center [&>span:last-child]:whitespace-normal [&>span:last-child]:text-base sm:[&>span:last-child]:text-lg" />
            <span class="text-xs uppercase tracking-wide text-ink-faint">Away</span>
        </div>
    </section>

    <div class="grid gap-6 xl:grid-cols-[1fr_1.15fr]">
        {{-- Result entry --}}
        <x-admin.section title="Result" description="Saving a completed result updates standings, team and player statistics immediately.">
            <form method="POST" action="{{ route('admin.fixtures.result', $fixture) }}" x-data="{ status: @js(old('status', $fixture->status === 'scheduled' ? 'completed' : $fixture->status)) }">
                @csrf
                @method('PUT')
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.field name="status" label="Status" type="select" :options="\App\Models\Fixture::STATUSES" :value="$fixture->status === 'scheduled' ? 'completed' : $fixture->status" x-model="status" class="sm:col-span-2" />
                    <div x-show="['live', 'completed'].includes(status)" class="contents">
                        <x-admin.field name="home_score" :label="$fixture->homeTeam->name" type="number" :value="$fixture->home_score" min="0" max="99" inputmode="numeric" class="[&_input]:text-center [&_input]:font-display [&_input]:text-2xl [&_input]:font-extrabold" />
                        <x-admin.field name="away_score" :label="$fixture->awayTeam->name" type="number" :value="$fixture->away_score" min="0" max="99" inputmode="numeric" class="[&_input]:text-center [&_input]:font-display [&_input]:text-2xl [&_input]:font-extrabold" />
                    </div>
                    <x-admin.field name="referee" label="Referee" :value="$fixture->referee" />
                    <x-admin.field name="attendance" label="Attendance" type="number" :value="$fixture->attendance" min="0" inputmode="numeric" />
                    <x-admin.field name="report" label="Short match summary" type="textarea" rows="3" :value="$fixture->report" help="Shown on the public match page. Write a full match report as a news article." class="sm:col-span-2" />
                </div>
                <div class="mt-5 flex flex-col gap-3 sm:flex-row">
                    <button type="submit" class="btn btn-primary" data-loading-text="Saving result…">Save result</button>
                    <a href="{{ route('admin.news.create', ['fixture' => $fixture->id]) }}" class="btn btn-outline">Write match report</a>
                    <a href="{{ route('admin.gallery.create', ['fixture' => $fixture->id]) }}" class="btn btn-ghost">Add photos</a>
                </div>
            </form>
        </x-admin.section>

        {{-- Match events --}}
        <x-admin.section title="Match events" id="events" class="scroll-mt-24" description="Goals, assists, cards and substitutions feed player statistics.">
            @if ($fixture->matchEvents->isEmpty())
                <p class="mb-5 rounded-xl border border-dashed border-line px-4 py-6 text-center text-sm text-ink-muted">No events recorded yet.</p>
            @else
                <ol class="mb-6 divide-y divide-white/5">
                    @foreach ($fixture->matchEvents as $event)
                        <li class="flex min-h-[52px] items-center gap-3 py-2">
                            <span class="w-12 shrink-0 font-display text-lg font-extrabold text-gold-light">{{ $event->minute_label }}</span>
                            <span class="min-w-0 flex-1 text-sm">
                                <span class="block font-semibold text-white">
                                    {{ $event->type_label }} — {{ $event->player?->full_name ?? 'Unknown player' }}
                                    @if ($event->relatedPlayer)<span class="font-normal text-ink-muted">({{ $event->related_label }}: {{ $event->relatedPlayer->full_name }})</span>@endif
                                </span>
                                <span class="text-xs text-ink-muted">{{ $event->team?->name }}{{ $event->description ? ' · ' . $event->description : '' }}</span>
                            </span>
                            <x-admin.row-actions :name="$event->type_label . ' (' . $event->minute_label . ')'" :delete="route('admin.fixtures.events.destroy', [$fixture, $event])" confirm="Player statistics will be recalculated." />
                        </li>
                    @endforeach
                </ol>
            @endif

            @if ($squads->flatten()->isEmpty())
                <x-alert type="warning" title="No players to choose from">
                    Add players to {{ $fixture->homeTeam->name }} and {{ $fixture->awayTeam->name }} before recording events.
                </x-alert>
            @else
                {{-- Player lists are filtered by the chosen team without extra requests: only the visible select is enabled and submitted. --}}
                <form method="POST" action="{{ route('admin.fixtures.events.store', $fixture) }}" class="rounded-xl border border-subtle bg-pitch-900/60 p-4"
                      x-data="{ type: @js(old('type', 'goal')), team: @js((string) old('team_id', $fixture->home_team_id)) }">
                    @csrf
                    <h3 class="mb-4 text-sm font-bold uppercase tracking-wide text-ink-2">Add event</h3>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-admin.field name="type" label="Event" type="select" :options="$eventTypes" :value="old('type', 'goal')" x-model="type" />
                        <x-admin.field name="team_id" label="Team" type="select" :options="$teams->pluck('name', 'id')" :value="$fixture->home_team_id" x-model="team" />

                        @foreach ($teams as $team)
                            <div x-show="team === '{{ $team->id }}'" class="contents">
                                <div class="min-w-0">
                                    <label for="player-{{ $team->id }}" class="form-label" x-text="type === 'substitution' ? 'Player coming off' : 'Player'">Player</label>
                                    <select id="player-{{ $team->id }}" name="player_id" class="form-input" :disabled="team !== '{{ $team->id }}'" required>
                                        <option value="">Choose a {{ $team->name }} player</option>
                                        @foreach ($squads[$team->id] as $player)
                                            <option value="{{ $player->id }}" @selected(old('player_id') == $player->id)>{{ $player->jersey_number ? $player->jersey_number . '. ' : '' }}{{ $player->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="min-w-0" x-show="['goal', 'penalty_scored', 'substitution'].includes(type)">
                                    <label for="related-{{ $team->id }}" class="form-label" x-text="type === 'substitution' ? 'Player coming on' : 'Assisted by (optional)'">Assisted by (optional)</label>
                                    <select id="related-{{ $team->id }}" name="related_player_id" class="form-input" :disabled="team !== '{{ $team->id }}' || ! ['goal', 'penalty_scored', 'substitution'].includes(type)">
                                        <option value="">None</option>
                                        @foreach ($squads[$team->id] as $player)
                                            <option value="{{ $player->id }}" @selected(old('related_player_id') == $player->id)>{{ $player->jersey_number ? $player->jersey_number . '. ' : '' }}{{ $player->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endforeach

                        <x-admin.field name="minute" label="Minute" type="number" min="0" max="130" inputmode="numeric" required />
                        <x-admin.field name="additional_minute" label="Added time" type="number" min="1" max="30" inputmode="numeric" placeholder="e.g. 2 for 45+2" />
                        <x-admin.field name="description" label="Note (optional)" placeholder="e.g. Header from a corner" class="sm:col-span-2" />
                    </div>
                    <p class="mt-3 text-xs text-ink-muted" x-show="type === 'own_goal'">Own goal: choose the team and player who put the ball into their own net.</p>
                    <button type="submit" class="btn btn-secondary mt-4 w-full sm:w-auto" data-loading-text="Adding…">Add event</button>
                </form>
            @endif
        </x-admin.section>
    </div>

    {{-- Line-ups --}}
    <section id="lineups" class="mt-6 scroll-mt-24">
        <h2 class="card-title mb-4">Line-ups</h2>
        <div class="grid gap-6 lg:grid-cols-2">
            @foreach ($teams as $team)
                @php $current = $lineups->get($team->id, collect())->keyBy('player_id'); @endphp
                <x-admin.section :title="$team->name" description="Tick the match-day squad, then mark up to 11 starters.">
                    @if ($squads[$team->id]->isEmpty())
                        <x-empty-state title="No players" description="{{ $team->name }} has no active players.">
                            <x-slot name="action"><a href="{{ route('admin.players.create', ['team' => $team->id]) }}" class="btn btn-outline">Add players</a></x-slot>
                        </x-empty-state>
                    @else
                        <form method="POST" action="{{ route('admin.fixtures.lineups.update', [$fixture, $team]) }}"
                              x-data="{ starters: {{ $current->where('is_starting', true)->count() }} }"
                              @change="starters = $el.querySelectorAll('[name=\'starting[]\']:checked').length">
                            @csrf
                            @method('PUT')
                            <div class="relative overflow-x-auto">
                                <table class="w-full min-w-[420px] text-sm">
                                    <thead class="text-[0.6875rem] font-bold uppercase tracking-wide text-ink-muted">
                                        <tr>
                                            <th scope="col" class="py-2 text-start">Player</th>
                                            <th scope="col" class="w-16 py-2 text-center">Squad</th>
                                            <th scope="col" class="w-16 py-2 text-center">Starts</th>
                                            <th scope="col" class="w-16 py-2 text-center">Capt.</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-white/5">
                                        @foreach ($squads[$team->id] as $player)
                                            @php $entry = $current->get($player->id); @endphp
                                            <tr>
                                                <td class="py-1.5">
                                                    <span class="font-semibold text-white">{{ $player->jersey_number ? $player->jersey_number . '. ' : '' }}{{ $player->full_name }}</span>
                                                    <span class="ms-1 text-xs text-ink-faint">{{ $player->position_short }}</span>
                                                </td>
                                                <td class="text-center"><label class="inline-grid h-11 w-11 cursor-pointer place-items-center"><span class="sr-only">{{ $player->full_name }} in squad</span><input type="checkbox" name="players[]" value="{{ $player->id }}" class="form-checkbox" @checked($entry)></label></td>
                                                <td class="text-center"><label class="inline-grid h-11 w-11 cursor-pointer place-items-center"><span class="sr-only">{{ $player->full_name }} starts</span><input type="checkbox" name="starting[]" value="{{ $player->id }}" class="form-checkbox" @checked($entry?->is_starting)></label></td>
                                                <td class="text-center"><label class="inline-grid h-11 w-11 cursor-pointer place-items-center"><span class="sr-only">{{ $player->full_name }} captain</span><input type="radio" name="captain_id" value="{{ $player->id }}" class="h-5 w-5 border-white/20 bg-[#050c08] text-gold focus:ring-gold/40" @checked($entry?->is_captain)></label></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                                <p class="text-sm" :class="starters > 11 ? 'text-danger' : 'text-ink-muted'"><span x-text="starters"></span>/11 starters</p>
                                <button type="submit" class="btn btn-secondary" data-loading-text="Saving…">Save {{ $team->name }} line-up</button>
                            </div>
                        </form>
                    @endif
                </x-admin.section>
            @endforeach
        </div>
    </section>

    <div class="mt-10 flex justify-end">
        <form method="POST" action="{{ route('admin.fixtures.destroy', $fixture) }}" data-confirm="Delete {{ $fixture->title }}? Its result, match events and line-ups are deleted too.">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-ghost text-danger hover:bg-danger/10 hover:text-danger">Delete fixture</button>
        </form>
    </div>
</x-layouts.admin>
