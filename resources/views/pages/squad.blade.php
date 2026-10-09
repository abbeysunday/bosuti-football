@extends('layouts.app')

@section('title', ($currentTeam ? $currentTeam->name . ' Squad' : 'Squad') . ' | BOUESTI Sports')
@section('meta_description', 'Players of the BOUESTI internal football teams — filter by team and position.')

@section('content')
    <x-site.page-hero
        title="Squad"
        eyebrow="The Players"
        description="Student footballers representing the internal teams of BOUESTI. Filter by team or position."
        image="{{ asset('frontend/assets/images/facilities/football-pitch-field.jpg') }}"
    />

    {{-- PLAYER DIRECTORY --}}
    <section class="section">
        <div class="container">
            <div class="section-head">
                <div>
                    <div class="eyebrow">{{ $currentTeam?->name ?? 'All Teams' }}</div>
                    <h2 class="section-title">Player <span class="gold">Profiles</span></h2>
                </div>
            </div>

            <x-site.filter-links label="Filter players by team" param="team" :current="$currentTeam?->slug" all="All Teams"
                :options="$teams->pluck('name', 'slug')" />
            <x-site.filter-links label="Filter players by position" param="position" :current="$currentPosition" all="All Positions"
                :options="['goalkeeper' => 'Goalkeepers', 'defender' => 'Defenders', 'midfielder' => 'Midfielders', 'forward' => 'Forwards']" />

            <p class="filter-status" aria-live="polite">{{ $players->total() }} {{ Str::plural('player', $players->total()) }}</p>

            @if ($players->isEmpty())
                <x-site.empty-state icon="fa-people-group" title="No players found" description="{{ $currentTeam ? 'No ' . ($currentPosition ? strtolower(\App\Models\Player::POSITIONS[$currentPosition]) . 's have' : 'players have') . ' been added to ' . $currentTeam->name . ' yet.' : 'No players match this filter yet.' }}">
                    <a class="btn dark sm" href="{{ route('squad') }}">Show all players</a>
                </x-site.empty-state>
            @else
                <div class="squad-grid">
                    @foreach ($players as $player)
                        <x-site.player-card :player="$player" squad />
                    @endforeach
                </div>
                {{ $players->links('vendor.pagination.bouesti') }}
            @endif
        </div>
    </section>
@endsection