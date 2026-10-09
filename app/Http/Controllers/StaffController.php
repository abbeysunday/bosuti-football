<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function management(): View
    {
        return view('pages.management', ['staff' => Staff::active()->ofType('management')->with('team')->ordered()->get()]);
    }

    public function coaching(): View
    {
        return view('pages.coaching-staff', ['staff' => Staff::active()->ofType('coaching')->with('team')->ordered()->get()]);
    }
}
