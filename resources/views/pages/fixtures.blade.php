@extends('layouts.app')

@section('title', 'Fixtures & Results | BOUESTI Sports')
@section('meta_description', 'Upcoming fixtures and results of BOUESTI internal football competitions.')

@section('content')
    <x-site.page-hero
        title="Fixtures & Results"
        eyebrow="Match Centre"
        description="Every match between BOUESTI teams: dates, kick-off times, venues and final scores."
        image="{{ asset('frontend/assets/images/facilities/football-stadium.jpg') }}"
    />

    {{-- UPCOMING & RESULTS --}}
    <section class="section">
        <div class="container">
            <div class="section-head">
                <div>
                    <div class="eyebrow">{{ $currentCompetition?->name ?? 'All Competitions' }}</div>
                    <h2 class="section-title">{!! $tab === 'results' ? 'Latest <span class="gold">Results</span>' : 'Upcoming <span class="gold">Fixtures</span>' !!}</h2>
                </div>
            </div>

            {{-- Tabs --}}
            <div class="filter-bar tabs" role="tablist" aria-label="Fixtures or results">
                @foreach (['upcoming' => 'Upcoming', 'results' => 'Results'] as $key => $label)
                    <a href="{{ route('fixtures', array_filter(['tab' => $key === 'upcoming' ? null : $key, 'competition' => $currentCompetition?->slug, 'team' => $currentTeam?->slug])) }}"
                       role="tab" aria-selected="{{ $tab === $key ? 'true' : 'false' }}" @class(['filter-btn', 'active' => $tab === $key])>{{ $label }}</a>
                @endforeach
            </div>

            {{-- Filters --}}
            <form class="filter-form" method="GET" action="{{ route('fixtures') }}">
                @if ($tab === 'results')<input type="hidden" name="tab" value="results">@endif
                <label class="field">
                    <span class="sr-only">Competition</span>
                    <select name="competition" aria-label="Competition">
                        <option value="">All competitions</option>
                        @foreach ($competitions as $competition)
                            <option value="{{ $competition->slug }}" @selected($currentCompetition?->id === $competition->id)>{{ $competition->name }} ({{ $competition->season?->name }})</option>
                        @endforeach
                    </select>
                </label>
                <label class="field">
                    <span class="sr-only">Team</span>
                    <select name="team" aria-label="Team">
                        <option value="">All teams</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->slug }}" @selected($currentTeam?->id === $team->id)>{{ $team->name }}</option>
                        @endforeach
                    </select>
                </label>
                @if ($months)
                    <label class="field">
                        <span class="sr-only">Month</span>
                        <select name="month" aria-label="Month">
                            <option value="">Any month</option>
                            @foreach ($months as $value => $text)
                                <option value="{{ $value }}" @selected($currentMonth === $value)>{{ $text }}</option>
                            @endforeach
                        </select>
                    </label>
                @endif
                <button class="btn gold sm" type="submit">Apply</button>
                @if ($currentCompetition || $currentTeam || $currentMonth)
                    <a class="btn ghost sm" href="{{ route('fixtures', array_filter(['tab' => $tab === 'results' ? 'results' : null])) }}">Clear filters</a>
                @endif
            </form>

            @if ($fixtures->isEmpty())
                <x-site.empty-state icon="fa-calendar-days"
                    title="{{ $tab === 'results' ? 'No results yet' : 'No upcoming fixtures' }}"
                    description="{{ $tab === 'results' ? 'Completed matches will appear here with their final scores.' : 'New fixtures between BOUESTI teams will appear here as soon as they are scheduled.' }}">
                    <a class="btn dark sm" href="{{ route('fixtures', ['tab' => $tab === 'results' ? null : 'results']) }}">{{ $tab === 'results' ? 'View upcoming fixtures' : 'View results' }}</a>
                </x-site.empty-state>
            @else
                <div class="fixture-list">
                    @foreach ($fixtures as $fixture)
                        <x-site.fixture-card :fixture="$fixture" />
                    @endforeach
                </div>
                {{ $fixtures->links('vendor.pagination.bouesti') }}
            @endif
        </div>
    </section>
@endsection
