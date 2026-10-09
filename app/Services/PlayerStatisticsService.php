<?php

namespace App\Services;

use App\Models\Fixture;
use App\Models\FixturePlayer;
use App\Models\MatchEvent;
use App\Models\Player;
use App\Models\Season;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Player numbers are derived from match data rather than typed in:
 *  - appearances: named in a lineup, or involved in an event, in a live/completed match
 *  - goals / assists / cards: match events (an assist is an `assist` event or the assister on a goal)
 *  - clean sheets: goalkeeper starts where the opponent did not score
 */
class PlayerStatisticsService
{
    /** Match statuses whose events count towards statistics. */
    private const COUNTED = ['live', 'completed'];

    /**
     * @return array{appearances:int, starts:int, goals:int, assists:int, yellow_cards:int, red_cards:int, clean_sheets:int}
     */
    public function forPlayer(Player $player, ?Season $season = null): array
    {
        $fixtures = $this->countedFixtures($season);

        $events = MatchEvent::query()
            ->whereIn('fixture_id', $fixtures)
            ->where(fn (Builder $q) => $q->where('player_id', $player->id)->orWhere('related_player_id', $player->id))
            ->get(['fixture_id', 'player_id', 'related_player_id', 'type']);

        $mine = $events->where('player_id', $player->id);
        $related = $events->where('related_player_id', $player->id);

        $lineups = FixturePlayer::query()
            ->where('player_id', $player->id)
            ->whereIn('fixture_id', $fixtures)
            ->get(['fixture_id', 'is_starting']);

        // A substitute coming on (related player on a substitution) has also appeared.
        $appeared = $lineups->pluck('fixture_id')
            ->merge($mine->pluck('fixture_id'))
            ->merge($related->where('type', 'substitution')->pluck('fixture_id'))
            ->unique();

        return [
            'appearances' => $appeared->count(),
            'starts' => $lineups->where('is_starting', true)->count(),
            'goals' => $mine->whereIn('type', MatchEvent::SCORING)->count(),
            'assists' => $mine->where('type', 'assist')->count() + $related->whereIn('type', MatchEvent::SCORING)->count(),
            'yellow_cards' => $mine->where('type', 'yellow_card')->count(),
            'red_cards' => $mine->where('type', 'red_card')->count(),
            'clean_sheets' => $player->position === 'goalkeeper' ? $this->cleanSheets($player, $season) : 0,
        ];
    }

    /**
     * Top scorers across counted matches.
     *
     * @return Collection<int, object{player:Player, goals:int}>
     */
    public function topScorers(?Season $season = null, ?int $competitionId = null, int $limit = 5): Collection
    {
        $rows = MatchEvent::query()
            ->whereIn('fixture_id', $this->countedFixtures($season, $competitionId))
            ->whereIn('type', MatchEvent::SCORING)
            ->whereNotNull('player_id')
            ->select('player_id', DB::raw('COUNT(*) AS goals'))
            ->groupBy('player_id')
            ->orderByDesc('goals')
            ->limit($limit)
            ->get();

        $players = Player::withTrashed()->with('team')->whereIn('id', $rows->pluck('player_id'))->get()->keyBy('id');

        return $rows
            ->filter(fn ($row) => $players->has($row->player_id))
            ->map(fn ($row) => (object) ['player' => $players[$row->player_id], 'goals' => (int) $row->goals])
            ->values();
    }

    private function cleanSheets(Player $player, ?Season $season): int
    {
        return FixturePlayer::query()
            ->where('fixture_players.player_id', $player->id)
            ->where('fixture_players.is_starting', true)
            ->join('fixtures', 'fixtures.id', '=', 'fixture_players.fixture_id')
            ->where('fixtures.status', 'completed')
            ->when($season, fn ($q) => $q->where('fixtures.season_id', $season->id))
            ->where(fn ($q) => $q
                ->where(fn ($h) => $h->whereColumn('fixture_players.team_id', 'fixtures.home_team_id')->where('fixtures.away_score', 0))
                ->orWhere(fn ($a) => $a->whereColumn('fixture_players.team_id', 'fixtures.away_team_id')->where('fixtures.home_score', 0)))
            ->count();
    }

    private function countedFixtures(?Season $season = null, ?int $competitionId = null): Builder
    {
        return Fixture::query()
            ->select('id')
            ->whereIn('status', self::COUNTED)
            ->when($season, fn ($q) => $q->where('season_id', $season->id))
            ->when($competitionId, fn ($q) => $q->where('competition_id', $competitionId));
    }
}
