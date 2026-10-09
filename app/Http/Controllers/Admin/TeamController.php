<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ManagesUploads;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TeamRequest;
use App\Models\Team;
use App\Services\TeamStatisticsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeamController extends Controller
{
    use ManagesUploads;

    public function index(Request $request): View
    {
        return view('admin.teams.index', [
            'teams' => Team::withCount('players')
                ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%' . $request->input('q') . '%'))
                ->orderBy('name')
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.teams.form', ['team' => new Team(['is_active' => true])]);
    }

    public function store(TeamRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['logo', 'remove_logo']);
        $data['logo'] = $this->handleUpload($request, 'logo', 'teams/logos');
        $team = Team::create($data);

        return redirect()->route('admin.teams.show', $team)->with('success', "{$team->name} created.");
    }

    public function show(Team $team, TeamStatisticsService $stats): View
    {
        return view('admin.teams.show', [
            'team' => $team,
            'players' => $team->players()->squadOrder()->get(),
            'stats' => $stats->forTeam($team),
            'fixtures' => $team->fixtures()->withTeams()->latestFirst()->limit(8)->get(),
        ]);
    }

    public function edit(Team $team): View
    {
        return view('admin.teams.form', compact('team'));
    }

    public function update(TeamRequest $request, Team $team): RedirectResponse
    {
        $data = $request->safe()->except(['logo', 'remove_logo']);
        $data['logo'] = $this->handleUpload($request, 'logo', 'teams/logos', $team->logo);
        $team->update($data);

        return redirect()->route('admin.teams.show', $team)->with('success', "{$team->name} updated.");
    }

    /**
     * Teams are soft-deleted: historical fixtures, results and standings keep the team's name.
     * Its players are archived with it so they leave public squad lists.
     */
    public function destroy(Team $team): RedirectResponse
    {
        $team->players()->delete();
        $team->delete();

        $note = $team->hasFixtures() ? ' Its past fixtures and results are kept.' : '';

        return redirect()->route('admin.teams.index')->with('success', "{$team->name} deleted.{$note}");
    }
}
