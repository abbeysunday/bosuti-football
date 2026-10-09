<?php

namespace App\Http\Controllers;

use App\Http\Requests\TrialApplicationRequest;
use App\Models\TrialApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TrialApplicationController extends Controller
{
    public function create(): View
    {
        return view('pages.apply');
    }

    public function store(TrialApplicationRequest $request): RedirectResponse
    {
        TrialApplication::create($request->applicationData());

        return redirect()->route('trials')
            // Shown inline above the form (not as a toast) so it stays visible.
            ->with('application_submitted', 'Thank you! Your trial application has been received. The football team will contact you about trial dates.');
    }
}
