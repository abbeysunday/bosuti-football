<?php

namespace App\Http\Controllers;

use App\Models\Fixture;
use App\Models\Player;
use App\Models\Team;
use App\Services\PlayerStatisticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlayerController extends Controller
{
    /** Squad directory filtered by team and position. */
    public function index(Request $request): View
    {
        $teams = Team::active()->orderBy('name')->get();
        $team = $teams->firstWhere('slug', $request->input('team'));
        $position = array_key_exists((string) $request->input('position'), Player::POSITIONS) ? $request->input('position') : null;

        return view('pages.squad', [
            'players' => Player::active()
                ->with('team')
                ->whereHas('team', fn ($q) => $q->where('is_active', true))
                ->when($team, fn ($q) => $q->where('team_id', $team->id))
                ->position($position)
                ->when(! $team, fn ($q) => $q->orderBy('team_id'))
                ->squadOrder()
                ->paginate(12)
                ->withQueryString(),
            'teams' => $teams,
            'currentTeam' => $team,
            'currentPosition' => $position,
        ]);
    }

    public function show(Player $player, PlayerStatisticsService $stats): View
    {
        abort_unless($player->is_active || $player->matchEvents()->exists(), 404);

        $player->load('team');

        return view('pages.player-profile', [
            'player' => $player,
            'stats' => $stats->forPlayer($player),
            'recentMatches' => Fixture::completed()->involving($player->team_id)->withTeams()->latestFirst()->limit(5)->get(),
            'teammates' => $player->team->players()->active()->whereKeyNot($player->id)->with('team')->inRandomOrder()->limit(4)->get(),
        ]);
    }
}
