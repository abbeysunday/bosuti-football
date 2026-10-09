<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrialApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TrialApplicationController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('q'));

        return view('admin.trial-applications.index', [
            'applications' => TrialApplication::status($request->input('status'))
                ->when($search !== '', fn ($q) => $q->where(fn ($w) => $w
                    ->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('matric_number', 'like', "%{$search}%")))
                ->latest()
                ->paginate(20)
                ->withQueryString(),
            'counts' => TrialApplication::selectRaw('status, COUNT(*) AS total')->groupBy('status')->pluck('total', 'status'),
        ]);
    }

    public function show(TrialApplication $trialApplication): View
    {
        // Opening a new application moves it into review.
        if ($trialApplication->status === 'pending') {
            $trialApplication->forceFill(['status' => 'reviewing'])->save();
        }

        return view('admin.trial-applications.show', ['application' => $trialApplication]);
    }

    public function update(Request $request, TrialApplication $trialApplication): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(TrialApplication::STATUSES))],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $trialApplication->forceFill($data)->save();

        return redirect()->route('admin.trial-applications.show', $trialApplication)
            ->with('success', "Application marked as {$trialApplication->status_label}.");
    }

    public function destroy(TrialApplication $trialApplication): RedirectResponse
    {
        $trialApplication->delete();

        return redirect()->route('admin.trial-applications.index')->with('success', 'Application deleted.');
    }
}
