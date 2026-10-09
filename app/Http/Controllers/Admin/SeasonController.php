<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SeasonRequest;
use App\Models\Season;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SeasonController extends Controller
{
    public function index(): View
    {
        return view('admin.seasons.index', [
            'seasons' => Season::withCount(['competitions', 'fixtures'])
                ->orderByDesc('is_current')->orderByDesc('start_date')->orderByDesc('id')
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.seasons.form', ['season' => new Season(['is_active' => true])]);
    }

    public function store(SeasonRequest $request): RedirectResponse
    {
        $season = Season::create($request->validated());

        return redirect()->route('admin.seasons.index')->with('success', "Season {$season->name} created.");
    }

    public function edit(Season $season): View
    {
        return view('admin.seasons.form', compact('season'));
    }

    public function update(SeasonRequest $request, Season $season): RedirectResponse
    {
        $season->update($request->validated());

        return redirect()->route('admin.seasons.index')->with('success', "Season {$season->name} updated.");
    }

    public function destroy(Season $season): RedirectResponse
    {
        if ($season->competitions()->exists() || $season->fixtures()->exists()) {
            return back()->with('error', "{$season->name} has competitions or fixtures, so it can't be deleted. Mark it inactive instead.");
        }

        $season->delete();

        return redirect()->route('admin.seasons.index')->with('success', "Season {$season->name} deleted.");
    }
}
