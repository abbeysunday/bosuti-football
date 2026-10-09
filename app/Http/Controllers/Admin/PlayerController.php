<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ManagesUploads;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PlayerRequest;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlayerController extends Controller
{
    use ManagesUploads;

    public function index(Request $request): View
    {
        $search = trim((string) $request->input('q'));

        return view('admin.players.index', [
            'players' => Player::with('team')
                ->when($search !== '', fn ($q) => $q->where(fn ($w) => $w
                    ->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('matric_number', 'like', "%{$search}%")))
                ->when($request->filled('team'), fn ($q) => $q->where('team_id', $request->integer('team')))
                ->position($request->input('position'))
                ->orderBy('team_id')->squadOrder()
                ->paginate(20)
                ->withQueryString(),
            'teams' => $this->teamOptions(),
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.players.form', [
            'player' => new Player(['is_active' => true, 'team_id' => $request->integer('team') ?: null]),
            'teams' => $this->teamOptions(),
        ]);
    }

    public function store(PlayerRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['photo', 'remove_photo']);
        $data['photo'] = $this->handleUpload($request, 'photo', 'players');
        $player = Player::create($data);

        $next = $request->boolean('add_another')
            ? redirect()->route('admin.players.create', ['team' => $player->team_id])
            : redirect()->route('admin.teams.show', $player->team);

        return $next->with('success', "{$player->full_name} added to {$player->team->name}.");
    }

    public function edit(Player $player): View
    {
        return view('admin.players.form', ['player' => $player, 'teams' => $this->teamOptions()]);
    }

    public function update(PlayerRequest $request, Player $player): RedirectResponse
    {
        $data = $request->safe()->except(['photo', 'remove_photo']);
        $data['photo'] = $this->handleUpload($request, 'photo', 'players', $player->photo);
        $player->update($data);

        return redirect()->route('admin.players.index', ['team' => $player->team_id])->with('success', "{$player->full_name} updated.");
    }

    /** Soft delete: goals, cards and line-ups in past matches stay attributed to the player. */
    public function destroy(Player $player): RedirectResponse
    {
        $player->delete();

        return back()->with('success', "{$player->full_name} removed from the squad. Their match history is kept.");
    }

    private function teamOptions()
    {
        return Team::orderBy('name')->pluck('name', 'id');
    }
}
