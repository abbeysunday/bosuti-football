@extends('layouts.app')

@section('title', $player->full_name . ' | BOUESTI Sports')
@section('meta_description', $player->full_name . ' — ' . $player->position_label . ' for ' . $player->team->name . ', BOUESTI internal football.')

@section('content')
    {{-- PLAYER HERO --}}
    <section class="profile-hero">
        <div class="profile-photo">
            @if ($player->photo_url)
                <img src="{{ $player->photo_url }}" alt="{{ $player->full_name }}">
            @else
                <div class="player-fallback" aria-hidden="true" @if ($player->team->primary_color) style="--team: {{ $player->team->primary_color }}" @endif><span>{{ mb_substr($player->first_name, 0, 1) }}{{ mb_substr($player->last_name, 0, 1) }}</span></div>
            @endif
        </div>
        <div class="profile-details">
            <div class="eyebrow"><a href="{{ route('teams.show', $player->team) }}">{{ $player->team->name }}</a></div>
            <div class="profile-number">{{ $player->shirt }}</div>
            <h1 class="profile-name">{{ $player->full_name }}</h1>
            <div class="profile-position">{{ $player->position_label }}@if ($player->is_captain) • Captain @endif</div>

            {{-- Public details only: matric number and state of origin stay private. --}}
            <div class="stat-grid">
                <div class="mini-stat"><b>{{ $player->shirt }}</b><span>Squad Number</span></div>
                <div class="mini-stat"><b>{{ $player->position_short }}</b><span>Position</span></div>
                <div class="mini-stat"><b>{{ $player->dominant_foot ? ucfirst($player->dominant_foot) : '—' }}</b><span>Foot</span></div>
                <div class="mini-stat"><b class="small">{{ $player->department ?? '—' }}</b><span>Department</span></div>
                <div class="mini-stat"><b>{{ $player->level ? str_replace(' Level', 'L', $player->level) : '—' }}</b><span>Level</span></div>
                <div class="mini-stat"><b>{{ $player->height ?? '—' }}</b><span>Height</span></div>
            </div>
        </div>
    </section>

    {{-- SEASON STATISTICS --}}
    <section class="section">
        <div class="container">
            <div class="section-head">
                <div>
                    <div class="eyebrow">All Competitions</div>
                    <h2 class="section-title">Player <span class="gold">Statistics</span></h2>
                </div>
            </div>
            <div class="impact player-stats">
                <div class="impact-item"><i class="fa-solid fa-shirt" aria-hidden="true"></i><div><b>{{ $stats['appearances'] }}</b><span>Appearances</span></div></div>
                <div class="impact-item"><i class="fa-regular fa-futbol" aria-hidden="true"></i><div><b>{{ $stats['goals'] }}</b><span>Goals</span></div></div>
                <div class="impact-item"><i class="fa-solid fa-shoe-prints" aria-hidden="true"></i><div><b>{{ $stats['assists'] }}</b><span>Assists</span></div></div>
                <div class="impact-item"><i class="fa-solid fa-square card-yellow" aria-hidden="true"></i><div><b>{{ $stats['yellow_cards'] }}</b><span>Yellow Cards</span></div></div>
                <div class="impact-item"><i class="fa-solid fa-square card-red" aria-hidden="true"></i><div><b>{{ $stats['red_cards'] }}</b><span>Red Cards</span></div></div>
                @if ($player->position === 'goalkeeper')
                    <div class="impact-item"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i><div><b>{{ $stats['clean_sheets'] }}</b><span>Clean Sheets</span></div></div>
                @endif
            </div>
            <p class="notice">Statistics are calculated from recorded match line-ups and events.</p>
        </div>
    </section>

    {{-- BIOGRAPHY & RECENT MATCHES --}}
    <section class="section alt">
        <div class="container two-col top">
            <div class="prose">
                <div class="eyebrow">Biography</div>
                <h2>Player Story</h2>
                @forelse (preg_split("/\R{2,}/", trim((string) $player->bio), -1, PREG_SPLIT_NO_EMPTY) as $paragraph)
                    <p>{{ $paragraph }}</p>
                @empty
                    <p>{{ $player->first_name }} plays as a {{ strtolower($player->position_label) }} for {{ $player->team->name }}. A full biography will be added soon.</p>
                @endforelse
            </div>
            <div>
                <div class="eyebrow">{{ $player->team->name }}</div>
                <h3 class="subsection-title">Recent <span class="gold">Matches</span></h3>
                @if ($recentMatches->isEmpty())
                    <x-site.empty-state icon="fa-calendar-days" title="No matches yet" description="{{ $player->team->name }} has not completed a match yet." />
                @else
                    <div class="fixture-list compact">
                        @foreach ($recentMatches as $fixture)
                            <x-site.fixture-card :fixture="$fixture" />
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>

    @if ($teammates->isNotEmpty())
        {{-- TEAMMATES --}}
        <section class="section">
            <div class="container">
                <div class="section-head">
                    <div>
                        <div class="eyebrow">{{ $player->team->name }}</div>
                        <h2 class="section-title">Team<span class="gold">mates</span></h2>
                    </div>
                    <a class="btn dark sm" href="{{ route('teams.show', $player->team) }}">Full Squad <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                </div>
                <div class="player-grid four">
                    @foreach ($teammates as $mate)
                        <x-site.player-card :player="$mate" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection