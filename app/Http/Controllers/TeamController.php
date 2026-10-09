<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\Fixture;
use App\Models\Season;
use App\Models\Team;
use App\Services\LeagueTableService;
use App\Services\TeamStatisticsService;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(): View
    {
        return view('pages.teams.index', [
            'teams' => Team::active()->withCount(['players' => fn ($q) => $q->active()])->orderBy('name')->get(),
        ]);
    }

    public function show(Team $team, TeamStatisticsService $stats, LeagueTableService $table): View
    {
        abort_unless($team->is_active, 404);

        // League position in the current season's main league (if the team plays in it).
        $season = Season::currentOrLatest();
        $league = $season
            ? Competition::active()->where('season_id', $season->id)->where('type', 'league')
                ->whereHas('fixtures', fn ($q) => $q->involving($team->id))->first()
            : null;

        return view('pages.teams.show', [
            'team' => $team,
            'players' => $team->players()->active()->squadOrder()->get()->groupBy('position'),
            'coaches' => $team->staff()->active()->ofType('coaching')->ordered()->get(),
            'stats' => $stats->forTeam($team),
            'recentResults' => $team->fixtures()->completed()->withTeams()->latestFirst()->limit(5)->get(),
            'upcoming' => Fixture::upcoming()->involving($team->id)->withTeams()->limit(5)->get(),
            'league' => $league,
            'standing' => $league ? $table->positionOf($team, $league) : null,
        ]);
    }
}
