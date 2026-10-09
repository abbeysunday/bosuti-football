@extends('layouts.app')

@section('title', $fixture->homeTeam->name . ' vs ' . $fixture->awayTeam->name . ' | BOUESTI Sports')
@section('meta_description', $fixture->title . ' — ' . ($fixture->competition?->name ?? 'BOUESTI football') . ', ' . $fixture->match_date->format('d F Y') . '.')

@section('content')
    <x-site.page-hero
        :title="$fixture->homeTeam->name . ' vs ' . $fixture->awayTeam->name"
        :eyebrow="collect([$fixture->competition?->name, $fixture->matchday ? 'Matchday ' . $fixture->matchday : null])->filter()->implode(' • ')"
        :description="collect([$fixture->match_date->format('l, d F Y'), $fixture->kickoff_label, $fixture->venue])->filter()->implode(' • ')"
        image="{{ asset('frontend/assets/images/facilities/football-stadium.jpg') }}"
        breadcrumb="Match Centre"
    />

    {{-- SCOREBOARD --}}
    <section class="section">
        <div class="container">
            <div class="fixture-shell single">
                <div class="fixture-match lg">
                    <div class="team-badge"><a href="{{ route('teams.show', $fixture->homeTeam) }}"><x-site.team-crest :team="$fixture->homeTeam" /><b>{{ $fixture->homeTeam->name }}</b></a></div>
                    <div class="vs">{{ $fixture->scoreline }}<small><span @class(['status-pill', $fixture->status])>{{ $fixture->status_label }}</span></small></div>
                    <div class="team-badge"><a href="{{ route('teams.show', $fixture->awayTeam) }}"><x-site.team-crest :team="$fixture->awayTeam" /><b>{{ $fixture->awayTeam->name }}</b></a></div>
                </div>
            </div>

            @if ($fixture->referee || $fixture->attendance)
                <p class="notice text-center">
                    @if ($fixture->referee)Referee: {{ $fixture->referee }}@endif
                    @if ($fixture->referee && $fixture->attendance) • @endif
                    @if ($fixture->attendance)Attendance: {{ number_format($fixture->attendance) }}@endif
                </p>
            @endif

            {{-- EVENTS TIMELINE --}}
            <div class="section-head mt-10">
                <div>
                    <div class="eyebrow">Minute by Minute</div>
                    <h2 class="section-title">Match <span class="gold">Events</span></h2>
                </div>
            </div>
            @if ($fixture->matchEvents->isEmpty())
                <x-site.empty-state icon="fa-stopwatch" title="No events recorded" description="{{ $fixture->isCompleted() ? 'Goals, cards and substitutions for this match were not recorded.' : 'Goals, cards and substitutions will appear here during and after the match.' }}" />
            @else
                <ol class="event-list">
                    @foreach ($fixture->matchEvents as $event)
                        {{-- MATCH EVENT --}}
                        <li @class(['event', 'away' => (int) $event->team_id === (int) $fixture->away_team_id, 'type-' . $event->type])>
                            <span class="event-minute">{{ $event->minute_label }}</span>
                            <span class="event-body">
                                <i class="fa-solid {{ $event->icon }} event-icon" aria-hidden="true"></i>
                                <span>
                                    <b>{{ $event->player?->full_name ?? 'Unknown player' }}</b>
                                    <small>
                                        {{ $event->type_label }}
                                        @if ($event->relatedPlayer) • {{ $event->related_label }}: {{ $event->relatedPlayer->full_name }}@endif
                                        @if ($event->description) • {{ $event->description }}@endif
                                    </small>
                                </span>
                            </span>
                        </li>
                    @endforeach
                </ol>
            @endif
        </div>
    </section>

    {{-- LINE-UPS --}}
    <section class="section alt">
        <div class="container">
            <div class="section-head">
                <div>
                    <div class="eyebrow">Team Sheets</div>
                    <h2 class="section-title">Line-<span class="gold">ups</span></h2>
                </div>
            </div>
            @if ($lineups->isEmpty())
                <x-site.empty-state icon="fa-clipboard-list" title="Line-ups not available" description="Team sheets are published by the match officials before kick-off." />
            @else
                <div class="lineup-grid">
                    @foreach ([$fixture->homeTeam, $fixture->awayTeam] as $team)
                        @php $sheet = $lineups->get($team->id, collect()); @endphp
                        <div class="lineup">
                            <h3><x-site.team-crest :team="$team" small /> {{ $team->name }}</h3>
                            @if ($sheet->isEmpty())
                                <p class="notice">No line-up submitted.</p>
                            @else
                                @foreach (['Starting XI' => $sheet->where('is_starting', true), 'Substitutes' => $sheet->where('is_starting', false)] as $heading => $group)
                                    @continue($group->isEmpty())
                                    <h4>{{ $heading }}</h4>
                                    <ul>
                                        @foreach ($group as $entry)
                                            <li>
                                                <span class="lineup-no">{{ $entry->shirt_number ?? '—' }}</span>
                                                <a href="{{ route('players.show', $entry->player) }}">{{ $entry->player->full_name }}</a>
                                                @if ($entry->is_captain)<span class="tag">C</span>@endif
                                                <small>{{ \App\Models\Player::POSITION_SHORT[$entry->position] ?? '' }}</small>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endforeach
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    @if ($fixture->report || $fixture->getRelation('report'))
        {{-- MATCH REPORT --}}
        <section class="section">
            <div class="container article-wrap">
                <article class="article-content">
                    <div class="eyebrow">Match Report</div>
                    @if ($fixture->report)
                        @foreach (preg_split("/\R{2,}/", trim($fixture->report), -1, PREG_SPLIT_NO_EMPTY) as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    @endif
                    @if ($article = $fixture->getRelation('report'))
                        <p><a class="btn gold sm" href="{{ route('news.show', $article) }}">Read the full report <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></p>
                    @endif
                </article>
            </div>
        </section>
    @endif

    @if ($photos->isNotEmpty())
        {{-- MATCH PHOTOS --}}
        <section class="section alt">
            <div class="container">
                <div class="section-head">
                    <div>
                        <div class="eyebrow">Photos from {{ $fixture->title }}</div>
                        <h2 class="section-title">Match <span class="gold">Gallery</span></h2>
                    </div>
                </div>
                <div class="gallery-grid">
                    @foreach ($photos as $item)
                        <button class="gallery-item" type="button"><img src="{{ $item->image_url }}" alt="{{ $item->alt_text }}" loading="lazy" decoding="async"></button>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
