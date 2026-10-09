<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/** Editorial pages with no database content. Data-driven pages have their own controllers. */
class FrontendController extends Controller
{
    public function about(): View
    {
        return view('pages.about');
    }

    public function history(): View
    {
        return view('pages.history');
    }

    public function academy(): View
    {
        return view('pages.academy');
    }

    public function basketball(): View
    {
        return view('pages.basketball');
    }

    public function fanZone(): View
    {
        return view('pages.fanzone');
    }

    public function membership(): View
    {
        return view('pages.membership');
    }

    public function sponsors(): View
    {
        return view('pages.sponsors');
    }

    public function faq(): View
    {
        return view('pages.faq');
    }

    public function contact(): View
    {
        return view('pages.contact');
    }

    public function matchDay(): View
    {
        return view('pages.match-day');
    }

    public function privacy(): View
    {
        return view('pages.privacy');
    }

    public function terms(): View
    {
        return view('pages.terms');
    }
}
