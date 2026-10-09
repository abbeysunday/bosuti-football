<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MatchEventRequest;
use App\Models\Fixture;
use App\Models\MatchEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MatchEventController extends Controller
{
    /** Recent events across all matches, linking back to each match. */
    public function index(Request $request): View
    {
        return view('admin.match-events.index', [
            'events' => MatchEvent::with(['fixture.homeTeam', 'fixture.awayTeam', 'team', 'player', 'relatedPlayer'])
                ->when(array_key_exists((string) $request->input('type'), MatchEvent::TYPES), fn ($q) => $q->where('type', $request->input('type')))
                ->latest('id')
                ->paginate(25)
                ->withQueryString(),
        ]);
    }

    public function store(MatchEventRequest $request, Fixture $fixture): RedirectResponse
    {
        $event = $fixture->matchEvents()->create($request->validated());
        $event->load('player');

        return redirect()->to(route('admin.fixtures.show', $fixture) . '#events')
            ->with('success', "{$event->type_label} added for {$event->player?->full_name} ({$event->minute_label}).");
    }

    public function destroy(Fixture $fixture, MatchEvent $event): RedirectResponse
    {
        // The event must belong to the fixture in the URL.
        abort_unless((int) $event->fixture_id === (int) $fixture->id, 404);

        $event->delete();

        return redirect()->to(route('admin.fixtures.show', $fixture) . '#events')->with('success', 'Match event removed.');
    }
}
