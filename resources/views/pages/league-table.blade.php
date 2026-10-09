@extends('layouts.app')

@section('title', 'League Table | BOUESTI Sports')
@section('meta_description', 'Live standings of BOUESTI internal football competitions, calculated from completed matches.')

@section('content')
    <x-site.page-hero
        title="League Table"
        eyebrow="Standings"
        description="Standings are calculated automatically from completed matches: 3 points for a win, 1 for a draw."
        image="{{ asset('frontend/assets/images/facilities/football-pitch-view.jpg') }}"
    />

    {{-- STANDINGS --}}
    <section class="section">
        <div class="container">
            <div class="section-head">
                <div>
                    <div class="eyebrow">{{ $competition?->season?->name ?? 'Standings' }}</div>
                    <h2 class="section-title">{{ $competition?->name ?? 'League' }} <span class="gold">Table</span></h2>
                </div>
            </div>

            @if ($competitions->count() > 1)
                <form class="filter-form" method="GET" action="{{ route('league.table') }}">
                    <label class="field">
                        <span class="sr-only">Competition</span>
                        <select name="competition" aria-label="Competition" onchange="this.form.requestSubmit()">
                            @foreach ($competitions as $option)
                                <option value="{{ $option->slug }}" @selected($option->id === $competition?->id)>{{ $option->name }} ({{ $option->season?->name }})</option>
                            @endforeach
                        </select>
                    </label>
                    <noscript><button class="btn gold sm" type="submit">Show</button></noscript>
                </form>
            @endif

            @if ($standings->isEmpty())
                <x-site.empty-state icon="fa-ranking-star" title="No league results available yet"
                    description="The table fills in automatically once fixtures are scheduled and results are recorded.">
                    <a class="btn dark sm" href="{{ route('fixtures') }}">View fixtures</a>
                </x-site.empty-state>
            @else
                <p class="table-hint"><i class="fa-solid fa-left-right" aria-hidden="true"></i>Swipe the table to see all statistics</p>
                <div class="table-wrap" tabindex="0" role="region" aria-label="League standings">
                    <table class="league-table">
                        <caption class="sr-only">{{ $competition->name }} standings: position, team, played, won, drawn, lost, goals for, goals against, goal difference, points and form</caption>
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Team</th>
                                <th scope="col"><abbr title="Played">P</abbr></th>
                                <th scope="col"><abbr title="Won">W</abbr></th>
                                <th scope="col"><abbr title="Drawn">D</abbr></th>
                                <th scope="col"><abbr title="Lost">L</abbr></th>
                                <th scope="col"><abbr title="Goals for">GF</abbr></th>
                                <th scope="col"><abbr title="Goals against">GA</abbr></th>
                                <th scope="col"><abbr title="Goal difference">GD</abbr></th>
                                <th scope="col" class="col-form">Form</th>
                                <th scope="col"><abbr title="Points">PTS</abbr></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($standings as $row)
                                {{-- TABLE ROW --}}
                                <tr @class(['highlight' => $row->position === 1 && $row->played > 0])>
                                    <td>{{ $row->position }}</td>
                                    <td>
                                        <a class="club-cell" href="{{ route('teams.show', $row->team) }}">
                                            <x-site.team-crest :team="$row->team" small />
                                            {{ $row->team->name }}
                                        </a>
                                    </td>
                                    <td>{{ $row->played }}</td>
                                    <td>{{ $row->won }}</td>
                                    <td>{{ $row->drawn }}</td>
                                    <td>{{ $row->lost }}</td>
                                    <td>{{ $row->goals_for }}</td>
                                    <td>{{ $row->goals_against }}</td>
                                    <td>{{ $row->goal_difference > 0 ? '+' : '' }}{{ $row->goal_difference }}</td>
                                    <td class="col-form">
                                        <span class="form-guide">
                                            @forelse ($row->form as $result)
                                                <span class="form-{{ strtolower($result) }}" title="{{ ['W' => 'Win', 'D' => 'Draw', 'L' => 'Loss'][$result] }}">{{ $result }}</span>
                                            @empty
                                                <span class="form-none">—</span>
                                            @endforelse
                                        </span>
                                    </td>
                                    <td>{{ $row->points }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="notice">Sorted by points, then goal difference, then goals scored. Updated automatically after every result.</p>
            @endif
        </div>
    </section>

    @if ($topScorers->isNotEmpty())
        {{-- TOP SCORERS --}}
        <section class="section alt">
            <div class="container">
                <div class="section-head">
                    <div>
                        <div class="eyebrow">{{ $competition->name }}</div>
                        <h2 class="section-title">Top <span class="gold">Scorers</span></h2>
                    </div>
                </div>
                <ol class="scorer-list">
                    @foreach ($topScorers as $row)
                        <li>
                            <span class="scorer-rank">{{ $loop->iteration }}</span>
                            <span class="scorer-name"><a href="{{ route('players.show', $row->player) }}">{{ $row->player->full_name }}</a><small>{{ $row->player->team?->name }}</small></span>
                            <span class="scorer-goals"><b>{{ $row->goals }}</b> {{ Str::plural('goal', $row->goals) }}</span>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>
    @endif
@endsection
