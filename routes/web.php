<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FixtureController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeagueTableController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TrialApplicationController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public website
|--------------------------------------------------------------------------
*/

Route::get('/', HomeController::class)->name('home');

// Club
Route::controller(FrontendController::class)->group(function () {
    Route::get('/about', 'about')->name('about');
    Route::get('/history', 'history')->name('history');
    Route::get('/player-development', 'academy')->name('academy');
    Route::get('/basketball', 'basketball')->name('basketball');
    Route::get('/fan-zone', 'fanZone')->name('fan.zone');
    Route::get('/membership', 'membership')->name('membership');
    Route::get('/sponsors', 'sponsors')->name('sponsors');
    Route::get('/faq', 'faq')->name('faq');
    Route::get('/contact', 'contact')->name('contact');
    Route::get('/match-day', 'matchDay')->name('match.day');
    Route::get('/privacy', 'privacy')->name('privacy');
    Route::get('/terms', 'terms')->name('terms');
});

Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');

Route::get('/management', [StaffController::class, 'management'])->name('management');
Route::get('/coaching-staff', [StaffController::class, 'coaching'])->name('coaching.staff');

// Teams & players
Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
Route::get('/teams/{team:slug}', [TeamController::class, 'show'])->name('teams.show');
Route::get('/squad', [PlayerController::class, 'index'])->name('squad');
Route::get('/players/{player:slug}', [PlayerController::class, 'show'])->name('players.show');
Route::redirect('/player-profile', '/squad', 301)->name('player.profile'); // legacy static URL

// Trials
Route::get('/join-the-team', [TrialApplicationController::class, 'create'])->name('trials');
Route::post('/join-the-team', [TrialApplicationController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('trials.store');

// Fixtures, results & standings
Route::get('/fixtures', [FixtureController::class, 'index'])->name('fixtures');
Route::get('/matches/{fixture}', [FixtureController::class, 'show'])->whereNumber('fixture')->name('matches.show');
Route::get('/match-details', [FixtureController::class, 'latest'])->name('match.details'); // "Match Centre": latest/next match
Route::get('/league-table', LeagueTableController::class)->name('league.table');

// News & media
Route::get('/news', [NewsController::class, 'index'])->name('news');
Route::redirect('/news/article', '/news', 301)->name('news.article'); // legacy static URL
Route::get('/news/{newsPost:slug}', [NewsController::class, 'show'])->name('news.show');
Route::get('/gallery', GalleryController::class)->name('gallery');
Route::get('/videos', VideoController::class)->name('videos');

/*
|--------------------------------------------------------------------------
| Account area (Breeze)
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    // Administrators work from the admin panel.
    return auth()->user()->isAdmin() ? redirect()->route('admin.dashboard') : view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin panel — authenticated administrators only
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::redirect('/', '/admin/dashboard');
    Route::get('/dashboard', Admin\DashboardController::class)->name('dashboard');

    // Football
    Route::resource('seasons', Admin\SeasonController::class)->except('show');
    Route::resource('competitions', Admin\CompetitionController::class)->except('show');
    Route::resource('teams', Admin\TeamController::class);
    Route::resource('players', Admin\PlayerController::class)->except('show');

    // Matches
    Route::resource('fixtures', Admin\FixtureController::class);
    Route::put('fixtures/{fixture}/result', [Admin\FixtureController::class, 'updateResult'])->name('fixtures.result');
    Route::post('fixtures/{fixture}/events', [Admin\MatchEventController::class, 'store'])->name('fixtures.events.store');
    Route::delete('fixtures/{fixture}/events/{event}', [Admin\MatchEventController::class, 'destroy'])->name('fixtures.events.destroy');
    Route::put('fixtures/{fixture}/lineups/{team}', [Admin\LineupController::class, 'update'])->name('fixtures.lineups.update');
    Route::get('match-events', [Admin\MatchEventController::class, 'index'])->name('match-events.index');

    // Content
    Route::resource('news', Admin\NewsController::class)->parameters(['news' => 'newsPost'])->except('show');
    Route::resource('gallery', Admin\GalleryController::class)->parameters(['gallery' => 'galleryItem'])->except('show');
    Route::resource('videos', Admin\VideoController::class)->except('show');

    // People
    Route::resource('staff', Admin\StaffController::class)->parameters(['staff' => 'staff'])->except('show');
    Route::resource('trial-applications', Admin\TrialApplicationController::class)
        ->parameters(['trial-applications' => 'trialApplication'])
        ->only(['index', 'show', 'update', 'destroy']);
    Route::resource('contact-messages', Admin\ContactMessageController::class)
        ->parameters(['contact-messages' => 'contactMessage'])
        ->only(['index', 'show', 'update', 'destroy']);
});

require __DIR__.'/auth.php';
