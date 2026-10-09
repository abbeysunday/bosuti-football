<?php

namespace App\Services;

use App\Models\Fixture;
use App\Models\Player;
use App\Models\Season;
use App\Models\Team;
use Illuminate\Support\Facades\DB;

/** Team and portal-wide numbers, computed from completed fixtures with aggregate queries. */
class TeamStatisticsService
{
    /**
     * @return array{played:int, won:int, drawn:int, lost:int, goals_for:int, goals_against:int, clean_sheets:int}
     */
    public function forTeam(Team $team, ?Season $season = null): array
    {
        $id = (int) $team->id;

        $row = Fixture::query()
            ->completed()
            ->whereNotNull('home_score')->whereNotNull('away_score')
            ->involving($id)
            ->when($season, fn ($q) => $q->where('season_id', $season->id))
            ->selectRaw('COUNT(*) AS played')
            ->selectRaw('SUM(CASE WHEN home_team_id = ? THEN home_score ELSE away_score END) AS goals_for', [$id])
            ->selectRaw('SUM(CASE WHEN home_team_id = ? THEN away_score ELSE home_score END) AS goals_against', [$id])
            ->selectRaw('SUM(CASE WHEN (home_team_id = ? AND home_score > away_score) OR (away_team_id = ? AND away_score > home_score) THEN 1 ELSE 0 END) AS won', [$id, $id])
            ->selectRaw('SUM(CASE WHEN home_score = away_score THEN 1 ELSE 0 END) AS drawn')
            ->selectRaw('SUM(CASE WHEN (home_team_id = ? AND away_score = 0) OR (away_team_id = ? AND home_score = 0) THEN 1 ELSE 0 END) AS clean_sheets', [$id, $id])
            ->first();

        $played = (int) $row->played;
        $won = (int) $row->won;
        $drawn = (int) $row->drawn;

        return [
            'played' => $played,
            'won' => $won,
            'drawn' => $drawn,
            'lost' => $played - $won - $drawn,
            'goals_for' => (int) $row->goals_for,
            'goals_against' => (int) $row->goals_against,
            'clean_sheets' => (int) $row->clean_sheets,
        ];
    }

    /**
     * Portal-wide totals for the homepage.
     *
     * @return array{matches_played:int, goals:int, teams:int, players:int}
     */
    public function overview(): array
    {
        $matches = Fixture::completed()
            ->selectRaw('COUNT(*) AS played, COALESCE(SUM(home_score + away_score), 0) AS goals')
            ->first();

        return [
            'matches_played' => (int) $matches->played,
            'goals' => (int) $matches->goals,
            'teams' => Team::active()->count(),
            'players' => Player::active()->whereHas('team', fn ($q) => $q->whereNull('deleted_at'))->count(),
        ];
    }
}
