@extends('layouts.app')

@section('title', $team->name . ' | BOUESTI Sports')
@section('meta_description', $team->description ? Str::limit($team->description, 150) : $team->name . ' squad, fixtures, results and statistics — BOUESTI internal football.')

@section('content')
    {{-- TEAM HERO --}}
    <section class="page-hero team-hero" @if ($team->primary_color) style="--team: {{ $team->primary_color }}" @endif>
        <div class="container">
            <div class="team-hero-inner">
                <x-site.team-crest :team="$team" class="team-hero-crest" />
                <div>
                    <div class="eyebrow">BOUESTI Internal Football</div>
                    <h1>{{ $team->name }}</h1>
                    @if ($team->description)<p>{{ $team->description }}</p>@endif
                </div>
            </div>
            <nav class="crumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Home</a><i class="fa-solid fa-chevron-right" aria-hidden="true"></i><a href="{{ route('teams.index') }}">Teams</a><i class="fa-solid fa-chevron-right" aria-hidden="true"></i><span aria-current="page">{{ $team->name }}</span>
            </nav>
        </div>
    </section>

    {{-- TEAM STATISTICS --}}
    <section class="section">
        <div class="container">
            <div class="impact team-stats">
                @if ($standing)
                    <div class="impact-item"><i class="fa-solid fa-ranking-star" aria-hidden="true"></i><div><b>#{{ $standing->position }}</b><span>{{ $league->display_name }} • {{ $standing->points }} pts</span></div></div>
                @endif
                <div class="impact-item"><i class="fa-solid fa-trophy" aria-hidden="true"></i><div><b>{{ $stats['played'] }}</b><span>Played</span></div></div>
                <div class="impact-item"><i class="fa-solid fa-check" aria-hidden="true"></i><div><b>{{ $stats['won'] }}–{{ $stats['drawn'] }}–{{ $stats['lost'] }}</b><span>Won • Drawn • Lost</span></div></div>
                <div class="impact-item"><i class="fa-regular fa-futbol" aria-hidden="true"></i><div><b>{{ $stats['goals_for'] }}:{{ $stats['goals_against'] }}</b><span>Goals For : Against</span></div></div>
                <div class="impact-item"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i><div><b>{{ $stats['clean_sheets'] }}</b><span>Clean Sheets</span></div></div>
            </div>
            @if ($team->coach_name || $coaches->isNotEmpty())
                <p class="notice">Coach: {{ $coaches->pluck('name')->prepend($team->coach_name)->filter()->unique()->implode(', ') }}</p>
            @endif
        </div>
    </section>

    {{-- SQUAD --}}
    <section class="section alt">
        <div class="container">
            <div class="section-head">
                <div>
                    <div class="eyebrow">{{ $team->name }}</div>
                    <h2 class="section-title">The <span class="gold">Squad</span></h2>
                </div>
                <a class="btn dark sm" href="{{ route('squad', ['team' => $team->slug]) }}">All Players <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            </div>
            @if ($players->isEmpty())
                <x-site.empty-state icon="fa-people-group" title="No players added yet" description="{{ $team->name }}’s squad will appear here once players are registered." />
            @else
                @foreach (\App\Models\Player::POSITIONS as $key => $label)
                    @continue(! $players->has($key))
                    <h3 class="subsection-title">{{ Str::plural($label) }}</h3>
                    <div class="squad-grid">
                        @foreach ($players[$key] as $player)
                            <x-site.player-card :player="$player" squad />
                        @endforeach
                    </div>
                @endforeach
            @endif
        </div>
    </section>

    {{-- MATCHES --}}
    <section class="section">
        <div class="container two-col top even">
            <div>
                <div class="eyebrow">Next Up</div>
                <h2 class="subsection-title">Upcoming <span class="gold">Fixtures</span></h2>
                @forelse ($upcoming as $fixture)
                    <x-site.fixture-card :fixture="$fixture" />
                @empty
                    <x-site.empty-state icon="fa-calendar-days" title="No upcoming fixtures" description="{{ $team->name }}’s next match has not been scheduled yet." />
                @endforelse
            </div>
            <div>
                <div class="eyebrow">Form</div>
                <h2 class="subsection-title">Recent <span class="gold">Results</span></h2>
                @forelse ($recentResults as $fixture)
                    <x-site.fixture-card :fixture="$fixture" />
                @empty
                    <x-site.empty-state icon="fa-futbol" title="No results yet" description="Results appear here after {{ $team->name }}’s first completed match." />
                @endforelse
            </div>
        </div>
    </section>
@endsection
