<?php

namespace App\Http\Controllers;

use App\Models\Fixture;
use App\Models\GalleryItem;
use App\Models\NewsPost;
use App\Models\Player;
use App\Services\TeamStatisticsService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(TeamStatisticsService $stats): View
    {
        // Featured players first; fall back to the newest squad members so the section is never empty.
        $featuredPlayers = Player::active()->featured()->whereHas('team')->with('team')->latest('updated_at')->limit(5)->get();
        if ($featuredPlayers->isEmpty()) {
            $featuredPlayers = Player::active()->whereHas('team')->with('team')->latest()->limit(5)->get();
        }

        return view('pages.home', [
            'featuredPlayers' => $featuredPlayers,
            'latestNews' => NewsPost::published()->newest()->limit(3)->get(),
            // A live match takes priority, then featured, then the nearest upcoming one.
            'nextFixture' => Fixture::upcoming()->withTeams()->reorder()
                ->orderByRaw("CASE WHEN status = 'live' THEN 0 ELSE 1 END")
                ->orderBy('match_date')->orderByDesc('featured')->orderByRaw('kickoff_time IS NULL')->orderBy('kickoff_time')
                ->first(),
            'recentResults' => Fixture::completed()->withTeams()->latestFirst()->limit(3)->get(),
            'overview' => $stats->overview(),
            'gallery' => GalleryItem::orderByDesc('is_featured')->ordered()->limit(5)->get(),
        ]);
    }
}
