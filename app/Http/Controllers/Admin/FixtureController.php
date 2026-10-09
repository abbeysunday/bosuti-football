<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FixtureRequest;
use App\Http\Requests\Admin\FixtureResultRequest;
use App\Models\Competition;
use App\Models\Fixture;
use App\Models\MatchEvent;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FixtureController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->input('status');

        return view('admin.fixtures.index', [
            'fixtures' => Fixture::withTeams()
                ->withCount('matchEvents')
                ->when($request->filled('competition'), fn ($q) => $q->where('competition_id', $request->integer('competition')))
                ->involving($request->integer('team') ?: null)
                ->when(array_key_exists((string) $status, Fixture::STATUSES), fn ($q) => $q->where('status', $status))
                // Results newest first; everything else in calendar order.
                ->when($status === 'completed', fn ($q) => $q->latestFirst(), fn ($q) => $q->orderBy('match_date')->orderBy('kickoff_time'))
                ->paginate(20)
                ->withQueryString(),
            'competitions' => $this->competitionOptions(),
            'teams' => Team::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.fixtures.form', [
            'fixture' => new Fixture([
                'status' => 'scheduled',
                'competition_id' => Competition::active()->latest('id')->value('id'),
                'match_date' => today(),
            ]),
            'competitions' => $this->competitionOptions(),
            'teams' => Team::active()->orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function store(FixtureRequest $request): RedirectResponse
    {
        $fixture = Fixture::create($request->validated());

        return redirect()->route('admin.fixtures.show', $fixture)->with('success', "{$fixture->title} scheduled.");
    }

    /** Match management hub: result, events, line-ups and match report. */
    public function show(Fixture $fixture): View
    {
        $fixture->load(['homeTeam', 'awayTeam', 'competition.season', 'matchEvents.player', 'matchEvents.relatedPlayer', 'matchEvents.team', 'lineups']);

        $squads = collect([$fixture->homeTeam, $fixture->awayTeam])->mapWithKeys(fn (Team $team) => [
            $team->id => $team->players()->active()->squadOrder()->get(),
        ]);

        return view('admin.fixtures.show', [
            'fixture' => $fixture,
            'squads' => $squads,
            'eventTypes' => MatchEvent::TYPES,
        ]);
    }

    public function edit(Fixture $fixture): View
    {
        return view('admin.fixtures.form', [
            'fixture' => $fixture,
            'competitions' => $this->competitionOptions(),
            // Keep the current teams selectable even if one was later deactivated.
            'teams' => Team::where(fn ($q) => $q->where('is_active', true)->orWhereIn('id', [$fixture->home_team_id, $fixture->away_team_id]))
                ->orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function update(FixtureRequest $request, Fixture $fixture): RedirectResponse
    {
        $fixture->update($request->validated());

        return redirect()->route('admin.fixtures.show', $fixture)->with('success', 'Fixture updated.');
    }

    /** Quick result entry. Standings are calculated from results, so nothing else needs updating. */
    public function updateResult(FixtureResultRequest $request, Fixture $fixture): RedirectResponse
    {
        $fixture->update($request->validated());

        $message = $fixture->isCompleted()
            ? "Result saved: {$fixture->homeTeam->name} {$fixture->home_score}–{$fixture->away_score} {$fixture->awayTeam->name}. The league table is up to date."
            : "Match status set to {$fixture->status_label}.";

        return redirect()->route('admin.fixtures.show', $fixture)->with('success', $message);
    }

    /** Deleting a fixture also removes its events and line-ups (cascade). */
    public function destroy(Fixture $fixture): RedirectResponse
    {
        $title = $fixture->title;
        $fixture->delete();

        return redirect()->route('admin.fixtures.index')->with('success', "{$title} deleted.");
    }

    private function competitionOptions()
    {
        return Competition::with('season')->orderByDesc('season_id')->orderBy('name')->get()
            ->mapWithKeys(fn (Competition $c) => [$c->id => "{$c->name} ({$c->season?->name})"]);
    }
}
