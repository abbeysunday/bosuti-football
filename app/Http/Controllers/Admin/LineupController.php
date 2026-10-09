<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LineupRequest;
use App\Models\Fixture;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class LineupController extends Controller
{
    /** Replace one team's match-day squad for a fixture. */
    public function update(LineupRequest $request, Fixture $fixture, Team $team): RedirectResponse
    {
        abort_unless($fixture->involves($team->id), 404);

        $selected = collect($request->validated('players', []))->map(fn ($id) => (int) $id);
        $starting = collect($request->validated('starting', []))->map(fn ($id) => (int) $id);
        $captain = $request->validated('captain_id') ? (int) $request->validated('captain_id') : null;

        DB::transaction(function () use ($fixture, $team, $selected, $starting, $captain) {
            $fixture->lineups()->where('team_id', $team->id)->whereNotIn('player_id', $selected)->delete();

            Player::whereIn('id', $selected)->get()->each(function (Player $player) use ($fixture, $team, $starting, $captain) {
                $fixture->lineups()->updateOrCreate(
                    ['player_id' => $player->id],
                    [
                        'team_id' => $team->id,
                        'is_starting' => $starting->contains($player->id),
                        'position' => $player->position,
                        'shirt_number' => $player->jersey_number,
                        'is_captain' => $captain === $player->id,
                    ],
                );
            });
        });

        return redirect()->to(route('admin.fixtures.show', $fixture) . '#lineups')
            ->with('success', "{$team->name} line-up saved ({$starting->count()} starting, " . ($selected->count() - $starting->count()) . ' substitutes).');
    }
}
