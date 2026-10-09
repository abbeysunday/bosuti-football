<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\Season;
use App\Services\LeagueTableService;
use App\Services\PlayerStatisticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeagueTableController extends Controller
{
    public function __invoke(Request $request, LeagueTableService $table, PlayerStatisticsService $players): View
    {
        $competitions = Competition::active()->withTable()->with('season')
            ->orderByDesc('season_id')->orderByRaw("CASE type WHEN 'league' THEN 0 ELSE 1 END")->orderBy('name')
            ->get();

        // Default: the current season's league.
        $currentSeasonId = Season::currentOrLatest()?->id;
        $competition = $competitions->firstWhere('slug', $request->input('competition'))
            ?? $competitions->firstWhere('season_id', $currentSeasonId)
            ?? $competitions->first();

        return view('pages.league-table', [
            'competitions' => $competitions,
            'competition' => $competition,
            'standings' => $competition ? $table->generate($competition) : collect(),
            'topScorers' => $competition ? $players->topScorers(competitionId: $competition->id) : collect(),
        ]);
    }
}
