@extends('layouts.app')

@section('title', 'Teams | BOUESTI Sports')
@section('meta_description', 'The student football teams competing in BOUESTI internal football competitions.')

@section('content')
    <x-site.page-hero
        title="Teams"
        eyebrow="BOUESTI Football"
        description="The student football clubs competing inside Bamidele Olumilua University of Education, Science and Technology."
        image="{{ asset('frontend/assets/images/facilities/football-pitch-wide.jpg') }}"
    />

    {{-- TEAMS --}}
    <section class="section">
        <div class="container">
            <div class="section-head">
                <div>
                    <div class="eyebrow">Internal Clubs</div>
                    <h2 class="section-title">Our <span class="gold">Teams</span></h2>
                </div>
                <a class="btn dark sm" href="{{ route('league.table') }}">League Table <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            </div>

            @if ($teams->isEmpty())
                <x-site.empty-state icon="fa-shield-halved" title="No teams yet" description="Teams will be listed here once they are registered." />
            @else
                <div class="team-grid">
                    @foreach ($teams as $team)
                        {{-- TEAM CARD --}}
                        <article class="team-card" @if ($team->primary_color) style="--team: {{ $team->primary_color }}" @endif>
                            <x-site.team-crest :team="$team" />
                            <div>
                                <h3><a href="{{ route('teams.show', $team) }}">{{ $team->name }}</a></h3>
                                <p>{{ $team->players_count }} {{ Str::plural('player', $team->players_count) }}{{ $team->coach_name ? ' • Coach: ' . $team->coach_name : '' }}</p>
                            </div>
                            <i class="fa-solid fa-arrow-right team-card-go" aria-hidden="true"></i>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
