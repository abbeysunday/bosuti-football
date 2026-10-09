<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ManagesUploads;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CompetitionRequest;
use App\Models\Competition;
use App\Models\Season;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompetitionController extends Controller
{
    use ManagesUploads;

    public function index(Request $request): View
    {
        return view('admin.competitions.index', [
            'competitions' => Competition::with('season')
                ->withCount('fixtures')
                ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%' . $request->input('q') . '%'))
                ->when($request->filled('season'), fn ($q) => $q->where('season_id', $request->integer('season')))
                ->latest('id')
                ->paginate(15)
                ->withQueryString(),
            'seasons' => Season::orderByDesc('id')->pluck('name', 'id'),
        ]);
    }

    public function create(): View
    {
        return view('admin.competitions.form', [
            'competition' => new Competition(['type' => 'league', 'is_active' => true, 'season_id' => Season::currentOrLatest()?->id]),
            'seasons' => Season::orderByDesc('id')->pluck('name', 'id'),
        ]);
    }

    public function store(CompetitionRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['logo', 'remove_logo']);
        $data['logo'] = $this->handleUpload($request, 'logo', 'competitions');
        $competition = Competition::create($data);

        return redirect()->route('admin.competitions.index')->with('success', "{$competition->name} created.");
    }

    public function edit(Competition $competition): View
    {
        return view('admin.competitions.form', [
            'competition' => $competition,
            'seasons' => Season::orderByDesc('id')->pluck('name', 'id'),
        ]);
    }

    public function update(CompetitionRequest $request, Competition $competition): RedirectResponse
    {
        $data = $request->safe()->except(['logo', 'remove_logo']);
        $data['logo'] = $this->handleUpload($request, 'logo', 'competitions', $competition->logo);
        $competition->update($data);

        return redirect()->route('admin.competitions.index')->with('success', "{$competition->name} updated.");
    }

    public function destroy(Competition $competition): RedirectResponse
    {
        if ($competition->fixtures()->exists()) {
            return back()->with('error', "{$competition->name} has fixtures, so it can't be deleted. Mark it inactive to hide it instead.");
        }

        $this->deleteUpload($competition->logo);
        $competition->delete();

        return redirect()->route('admin.competitions.index')->with('success', "{$competition->name} deleted.");
    }
}
