<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\ContactMessage;
use App\Models\Fixture;
use App\Models\NewsPost;
use App\Models\Player;
use App\Models\Team;
use App\Models\TrialApplication;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        // Every figure is a single COUNT query.
        $stats = [
            'teams' => Team::count(),
            'players' => Player::count(),
            'upcoming' => Fixture::whereIn('status', ['scheduled', 'live'])->whereDate('match_date', '>=', today())->count(),
            'completed' => Fixture::completed()->count(),
            'competitions' => Competition::active()->count(),
            'news' => NewsPost::published()->count(),
        ];

        return view('admin.dashboard', [
            'stats' => $stats,
            'pendingApplications' => TrialApplication::status('pending')->count(),
            'unreadMessages' => ContactMessage::status('new')->count(),
            'upcomingFixtures' => Fixture::upcoming()->withTeams()->limit(5)->get(),
            'recentResults' => Fixture::completed()->withTeams()->latestFirst()->limit(5)->get(),
            'recentApplications' => TrialApplication::latest()->limit(5)->get(),
        ]);
    }
}
