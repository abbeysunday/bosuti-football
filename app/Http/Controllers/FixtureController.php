<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\Fixture;
use App\Models\GalleryItem;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FixtureController extends Controller
{
    /** Upcoming fixtures and results with competition / team / month filters. */
    public function index(Request $request): View
    {
        $tab = $request->input('tab') === 'results' ? 'results' : 'upcoming';
        $competitions = Competition::active()->with('season')->orderByDesc('season_id')->orderBy('name')->get();
        $teams = Team::active()->orderBy('name')->get();
        $competition = $competitions->firstWhere('slug', $request->input('competition'));
        $team = $teams->firstWhere('slug', $request->input('team'));
        $month = preg_match('/^\d{4}-\d{2}$/', (string) $request->input('month')) ? $request->input('month') : null;

        $query = Fixture::withTeams()
            ->when($competition, fn ($q) => $q->where('competition_id', $competition->id))
            ->involving($team?->id)
            ->when($month, function ($q) use ($month) {
                $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
                $q->whereBetween('match_date', [$start->toDateString(), $start->copy()->endOfMonth()->toDateString()]);
            });

        $tab === 'results'
            ? $query->completed()->latestFirst()
            : $query->whereIn('status', ['scheduled', 'live', 'postponed'])->whereDate('match_date', '>=', today()->subDay())
                ->orderBy('match_date')->orderByRaw('kickoff_time IS NULL')->orderBy('kickoff_time');

        return view('pages.fixtures', [
            'fixtures' => $query->paginate(12)->withQueryString(),
            'tab' => $tab,
            'competitions' => $competitions,
            'teams' => $teams,
            'currentCompetition' => $competition,
            'currentTeam' => $team,
            'currentMonth' => $month,
            'months' => $this->months(),
        ]);
    }

    public function show(Fixture $fixture): View
    {
        $fixture->load([
            'homeTeam', 'awayTeam', 'competition.season',
            'matchEvents.player', 'matchEvents.relatedPlayer', 'matchEvents.team',
            'lineups.player', 'report',
        ]);

        $lineups = $fixture->lineups
            ->sortBy(fn ($l) => [$l->is_starting ? 0 : 1, $l->shirt_number ?? 99])
            ->groupBy('team_id');

        return view('pages.match-details', [
            'fixture' => $fixture,
            'lineups' => $lineups,
            'photos' => GalleryItem::where('fixture_id', $fixture->id)->ordered()->limit(8)->get(),
        ]);
    }

    /** "Match Centre" menu link: the live match, else the next one, else the latest result. */
    public function latest(): RedirectResponse
    {
        $fixture = Fixture::where('status', 'live')->latestFirst()->first()
            ?? Fixture::upcoming()->first()
            ?? Fixture::completed()->latestFirst()->first();

        return $fixture
            ? redirect()->route('matches.show', $fixture)
            : redirect()->route('fixtures');
    }

    /** Months that have fixtures, for the month filter. */
    private function months(): array
    {
        // Grouped in PHP so the query stays portable between SQLite and MySQL.
        return Fixture::query()->distinct()->orderByDesc('match_date')->pluck('match_date')
            ->map(fn ($date) => Carbon::parse($date))
            ->unique(fn (Carbon $date) => $date->format('Y-m'))
            ->mapWithKeys(fn (Carbon $date) => [$date->format('Y-m') => $date->format('F Y')])
            ->all();
    }
}
