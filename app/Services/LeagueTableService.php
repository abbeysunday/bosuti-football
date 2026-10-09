<?php

namespace App\Services;

use App\Models\Competition;
use App\Models\Fixture;
use App\Models\Season;
use App\Models\Team;
use Illuminate\Support\Collection;

/**
 * Builds standings from COMPLETED fixtures — points are never stored.
 * Win 3, draw 1, loss 0. Sorted by points, goal difference, goals for, then name.
 */
class LeagueTableService
{
    public const WIN = 3;
    public const DRAW = 1;

    /**
     * @return Collection<int, object{position:int, team:Team, played:int, won:int, drawn:int, lost:int,
     *                                goals_for:int, goals_against:int, goal_difference:int, points:int, form:array}>
     */
    public function generate(Competition $competition, ?Season $season = null): Collection
    {
        $fixtures = Fixture::query()
            ->where('competition_id', $competition->id)
            ->when($season, fn ($q) => $q->where('season_id', $season->id))
            ->get(['id', 'home_team_id', 'away_team_id', 'status', 'home_score', 'away_score', 'match_date', 'kickoff_time']);

        // Every team drawn into the competition appears, even before its first result.
        $teamIds = $fixtures->pluck('home_team_id')->merge($fixtures->pluck('away_team_id'))->unique();
        if ($teamIds->isEmpty()) {
            return collect();
        }

        $rows = Team::withTrashed()->whereIn('id', $teamIds)->get()->mapWithKeys(fn (Team $team) => [
            $team->id => (object) [
                'position' => 0, 'team' => $team, 'played' => 0, 'won' => 0, 'drawn' => 0, 'lost' => 0,
                'goals_for' => 0, 'goals_against' => 0, 'goal_difference' => 0, 'points' => 0, 'form' => [],
            ],
        ]);

        $completed = $fixtures
            ->filter(fn (Fixture $f) => $f->status === 'completed' && $f->home_score !== null && $f->away_score !== null)
            ->sortBy(fn (Fixture $f) => $f->match_date->format('Y-m-d') . ' ' . $f->kickoff_time . sprintf('%08d', $f->id));

        foreach ($completed as $fixture) {
            $this->record($rows[$fixture->home_team_id], $fixture->home_score, $fixture->away_score);
            $this->record($rows[$fixture->away_team_id], $fixture->away_score, $fixture->home_score);
        }

        return $rows->values()
            ->each(function ($row) {
                $row->goal_difference = $row->goals_for - $row->goals_against;
                $row->form = array_slice($row->form, -5);
            })
            ->sort(fn ($a, $b) => [$b->points, $b->goal_difference, $b->goals_for, $a->team->name]
                <=> [$a->points, $a->goal_difference, $a->goals_for, $b->team->name])
            ->values()
            ->each(fn ($row, $i) => $row->position = $i + 1);
    }

    /** A team's current position in a competition, or null if it is not in the table. */
    public function positionOf(Team $team, Competition $competition, ?Season $season = null): ?object
    {
        return $this->generate($competition, $season)->first(fn ($row) => $row->team->id === $team->id);
    }

    private function record(object $row, int $for, int $against): void
    {
        $row->played++;
        $row->goals_for += $for;
        $row->goals_against += $against;

        if ($for > $against) {
            $row->won++;
            $row->points += self::WIN;
            $row->form[] = 'W';
        } elseif ($for === $against) {
            $row->drawn++;
            $row->points += self::DRAW;
            $row->form[] = 'D';
        } else {
            $row->lost++;
            $row->form[] = 'L';
        }
    }
}
