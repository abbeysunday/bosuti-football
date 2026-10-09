<?php

use App\Models\Competition;
use App\Models\Fixture;
use App\Models\NewsPost;
use App\Models\Player;
use App\Models\Season;
use App\Models\Team;
use App\Models\TrialApplication;
use App\Models\User;
use App\Services\LeagueTableService;
use Database\Seeders\CompetitionSeeder;
use Database\Seeders\SeasonSeeder;
use Database\Seeders\TeamSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed([SeasonSeeder::class, CompetitionSeeder::class, TeamSeeder::class]);

    $this->admin = User::factory()->create();
    $this->admin->forceFill(['role' => User::ROLE_ADMIN])->save();

    $this->amapro = Team::where('slug', 'amapro-fc')->firstOrFail();
    $this->elite = Team::where('slug', 'elite-fc')->firstOrFail();
    $this->league = Competition::firstOrFail();
});

function makePlayer(Team $team, array $attributes = []): Player
{
    static $n = 0;
    $n++;

    return Player::create($attributes + [
        'team_id' => $team->id,
        'first_name' => 'Sample',
        'last_name' => "Player{$n}",
        'position' => 'forward',
        'jersey_number' => $n,
        'is_active' => true,
    ]);
}

/* Seeders ------------------------------------------------------------- */

test('the seven internal BOUESTI teams are seeded with the expected slugs', function () {
    expect(Team::orderBy('id')->pluck('slug')->all())->toBe([
        'amapro-fc', 'elite-fc', 'young-boys-fc', 'csc-elites', 'engines-boys', 'sovereignty-fc', 'amcoms',
    ]);
    expect(Season::current()->count())->toBe(1);
});

/* Access control ------------------------------------------------------ */

test('guests are sent to login and regular users are refused the admin panel', function () {
    $this->get('/admin/dashboard')->assertRedirect('/login');

    $this->actingAs(User::factory()->create())->get('/admin/dashboard')->assertForbidden();
    $this->actingAs(User::factory()->create())->post('/admin/teams', ['name' => 'Hack FC'])->assertForbidden();
    expect(Team::where('name', 'Hack FC')->exists())->toBeFalse();
});

test('admins are redirected from the user dashboard to the admin panel', function () {
    $this->actingAs($this->admin)->get('/dashboard')->assertRedirect(route('admin.dashboard'));
});

/* The required end-to-end workflow ------------------------------------ */

test('admin workflow: players, fixture, result, event and match report reach the public site', function () {
    $this->actingAs($this->admin);

    // Admin edits a team.
    $this->put(route('admin.teams.update', $this->amapro), [
        'name' => 'Amapro FC', 'short_name' => 'AMA', 'primary_color' => '#0a7a3d', 'is_active' => 1,
    ])->assertRedirect(route('admin.teams.show', $this->amapro))->assertSessionHasNoErrors();

    // Admin adds players to Amapro FC and Elite FC.
    $this->post(route('admin.players.store'), [
        'team_id' => $this->amapro->id, 'first_name' => 'Tunde', 'last_name' => 'Adeyemi',
        'position' => 'forward', 'jersey_number' => 9, 'matric_number' => 'BOU/2024/0001', 'is_active' => 1,
    ])->assertSessionHasNoErrors();
    $this->post(route('admin.players.store'), [
        'team_id' => $this->amapro->id, 'first_name' => 'Kola', 'last_name' => 'Bello', 'position' => 'midfielder', 'jersey_number' => 8, 'is_active' => 1,
    ])->assertSessionHasNoErrors();
    $this->post(route('admin.players.store'), [
        'team_id' => $this->elite->id, 'first_name' => 'Emeka', 'last_name' => 'Okafor', 'position' => 'goalkeeper', 'jersey_number' => 1, 'is_active' => 1,
    ])->assertSessionHasNoErrors();

    $scorer = Player::where('last_name', 'Adeyemi')->firstOrFail();
    $assister = Player::where('last_name', 'Bello')->firstOrFail();
    expect($scorer->slug)->toBe('tunde-adeyemi');

    // Admin creates Amapro FC vs Elite FC; it appears on the public fixtures page.
    $this->post(route('admin.fixtures.store'), [
        'competition_id' => $this->league->id,
        'home_team_id' => $this->amapro->id,
        'away_team_id' => $this->elite->id,
        'match_date' => today()->addDays(3)->toDateString(),
        'kickoff_time' => '16:00',
        'venue' => 'BOUESTI Sports Complex',
        'status' => 'scheduled',
    ])->assertSessionHasNoErrors();

    $fixture = Fixture::firstOrFail();
    expect($fixture->season_id)->toBe($this->league->season_id);

    $this->get(route('fixtures'))->assertOk()->assertSeeInOrder(['Amapro FC', 'VS', 'Elite FC']);
    $this->get(route('home'))->assertOk()->assertSee('Upcoming Fixture')->assertSee('data-countdown', false);

    // Admin enters the completed result: Amapro FC 2–1 Elite FC.
    $this->put(route('admin.fixtures.result', $fixture), ['status' => 'completed', 'home_score' => 2, 'away_score' => 1])
        ->assertRedirect(route('admin.fixtures.show', $fixture))
        ->assertSessionHasNoErrors();

    // Standings update automatically.
    $standings = app(LeagueTableService::class)->generate($this->league);
    expect($standings->first()->team->name)->toBe('Amapro FC')
        ->and($standings->first()->points)->toBe(3)
        ->and($standings->first()->goal_difference)->toBe(1)
        ->and($standings->last()->team->name)->toBe('Elite FC')
        ->and($standings->last()->lost)->toBe(1);

    $this->get(route('league.table'))->assertOk()->assertSeeInOrder(['Amapro FC', 'Elite FC']);

    // Admin records a goal (with assist) for an Amapro player; profile statistics update.
    $this->post(route('admin.fixtures.events.store', $fixture), [
        'type' => 'goal', 'team_id' => $this->amapro->id, 'player_id' => $scorer->id,
        'related_player_id' => $assister->id, 'minute' => 62,
    ])->assertSessionHasNoErrors();

    $profile = $this->get(route('players.show', $scorer))->assertOk();
    expect($profile->viewData('stats'))->toMatchArray(['goals' => 1, 'appearances' => 1]);
    $profile->assertDontSee('BOU/2024/0001'); // matric number stays private

    $assistStats = $this->get(route('players.show', $assister))->viewData('stats');
    expect($assistStats['assists'])->toBe(1);

    // The result appears on the homepage and the match centre shows the event.
    $this->get(route('home'))->assertOk()->assertSee('Recent')->assertSee('2–1');
    $this->get(route('matches.show', $fixture))->assertOk()->assertSee("62'")->assertSee('Tunde Adeyemi');

    // Admin publishes a match report; it appears publicly.
    $this->post(route('admin.news.store'), [
        'title' => 'Amapro FC edge Elite FC in campus derby',
        'content' => "Amapro FC beat Elite FC 2–1.\n\nTunde Adeyemi scored the winner.",
        'category' => 'Match Report',
        'fixture_id' => $fixture->id,
        'is_published' => 1,
    ])->assertSessionHasNoErrors();

    $post = NewsPost::firstOrFail();
    expect($post->author_id)->toBe($this->admin->id);

    $this->get(route('news'))->assertOk()->assertSee('Amapro FC edge Elite FC in campus derby');
    $this->get(route('news.show', $post))->assertOk()->assertSee('Tunde Adeyemi scored the winner.');
    $this->get(route('matches.show', $fixture))->assertSee('Read the full report');
});

/* Validation & integrity ---------------------------------------------- */

test('a team cannot play itself', function () {
    $this->actingAs($this->admin)->post(route('admin.fixtures.store'), [
        'competition_id' => $this->league->id, 'home_team_id' => $this->amapro->id, 'away_team_id' => $this->amapro->id,
        'match_date' => today()->toDateString(), 'status' => 'scheduled',
    ])->assertSessionHasErrors('away_team_id');

    expect(Fixture::count())->toBe(0);
});

test('a team cannot be scheduled for two active fixtures on the same day', function () {
    $this->actingAs($this->admin);
    $young = Team::where('slug', 'young-boys-fc')->firstOrFail();
    $existing = Fixture::create([
        'competition_id' => $this->league->id, 'home_team_id' => $this->amapro->id, 'away_team_id' => $this->elite->id,
        'match_date' => '2026-11-07', 'status' => 'scheduled',
    ]);
    $clash = [
        'competition_id' => $this->league->id, 'home_team_id' => $young->id, 'away_team_id' => $this->elite->id,
        'match_date' => '2026-11-07', 'status' => 'scheduled',
    ];

    $this->post(route('admin.fixtures.store'), $clash)->assertSessionHasErrors('match_date');
    $this->post(route('admin.fixtures.store'), ['match_date' => '2026-11-08'] + $clash)->assertSessionHasNoErrors();

    // Editing a fixture does not clash with itself, and a postponed fixture frees the day.
    $this->put(route('admin.fixtures.update', $existing), $existing->only('competition_id', 'home_team_id', 'away_team_id', 'status') + ['match_date' => '2026-11-07'])
        ->assertSessionHasNoErrors();
    $existing->update(['status' => 'postponed']);
    $this->post(route('admin.fixtures.store'), $clash)->assertSessionHasNoErrors();

    expect(Fixture::count())->toBe(3);
});

test('admins can create a team, which is then available for fixtures and listed publicly', function () {
    $this->actingAs($this->admin)->post(route('admin.teams.store'), [
        'name' => 'Faculty of Science FC', 'short_name' => 'FOS', 'primary_color' => '#009A56', 'is_active' => 1,
    ])->assertSessionHasNoErrors();

    $team = Team::where('name', 'Faculty of Science FC')->firstOrFail();
    expect($team->slug)->toBe('faculty-of-science-fc');
    $this->get(route('admin.fixtures.create'))->assertSee('Faculty of Science FC');
    $this->get(route('teams.index'))->assertSee('Faculty of Science FC');
});

test('editing a fixture is reflected on the public schedule', function () {
    $fixture = Fixture::create([
        'competition_id' => $this->league->id, 'home_team_id' => $this->amapro->id, 'away_team_id' => $this->elite->id,
        'match_date' => now()->addWeek()->toDateString(), 'kickoff_time' => '10:00', 'venue' => 'Main Pitch', 'status' => 'scheduled',
    ]);

    $this->actingAs($this->admin)->put(route('admin.fixtures.update', $fixture), [
        'competition_id' => $this->league->id, 'home_team_id' => $this->amapro->id, 'away_team_id' => $this->elite->id,
        'match_date' => now()->addWeeks(2)->toDateString(), 'kickoff_time' => '16:00', 'venue' => 'Sports Complex Pitch B', 'status' => 'scheduled',
    ])->assertSessionHasNoErrors();

    expect($fixture->fresh()->venue)->toBe('Sports Complex Pitch B');
    $this->get(route('fixtures'))->assertSee('Sports Complex Pitch B')->assertDontSee('Main Pitch');
});

test('completed results require both scores, and scheduled fixtures never keep a score', function () {
    $this->actingAs($this->admin);
    $fixture = Fixture::create([
        'competition_id' => $this->league->id, 'home_team_id' => $this->amapro->id, 'away_team_id' => $this->elite->id,
        'match_date' => today(), 'status' => 'scheduled',
    ]);

    $this->put(route('admin.fixtures.result', $fixture), ['status' => 'completed', 'home_score' => 1])
        ->assertSessionHasErrors('away_score');

    $this->put(route('admin.fixtures.result', $fixture), ['status' => 'postponed', 'home_score' => 3, 'away_score' => 0]);
    expect($fixture->fresh()->home_score)->toBeNull();
});

test('jersey numbers are unique within a team but can repeat across teams', function () {
    $this->actingAs($this->admin);
    makePlayer($this->amapro, ['jersey_number' => 10]);

    $this->post(route('admin.players.store'), [
        'team_id' => $this->amapro->id, 'first_name' => 'Dup', 'last_name' => 'Ten', 'position' => 'forward', 'jersey_number' => 10,
    ])->assertSessionHasErrors('jersey_number');

    $this->post(route('admin.players.store'), [
        'team_id' => $this->elite->id, 'first_name' => 'Other', 'last_name' => 'Ten', 'position' => 'forward', 'jersey_number' => 10,
    ])->assertSessionHasNoErrors();
});

test('match events reject players from the wrong team and teams not in the fixture', function () {
    $this->actingAs($this->admin);
    $fixture = Fixture::create([
        'competition_id' => $this->league->id, 'home_team_id' => $this->amapro->id, 'away_team_id' => $this->elite->id,
        'match_date' => today(), 'status' => 'completed', 'home_score' => 1, 'away_score' => 0,
    ]);
    $elitePlayer = makePlayer($this->elite);
    $outsider = Team::where('slug', 'amcoms')->first();

    // Elite player submitted as an Amapro goal (tampered form).
    $this->post(route('admin.fixtures.events.store', $fixture), [
        'type' => 'goal', 'team_id' => $this->amapro->id, 'player_id' => $elitePlayer->id, 'minute' => 10,
    ])->assertSessionHasErrors('player_id');

    // A team that is not playing in the match.
    $this->post(route('admin.fixtures.events.store', $fixture), [
        'type' => 'goal', 'team_id' => $outsider->id, 'player_id' => makePlayer($outsider)->id, 'minute' => 10,
    ])->assertSessionHasErrors('team_id');

    expect($fixture->matchEvents()->count())->toBe(0);
});

test('only one season is current at a time', function () {
    $this->actingAs($this->admin)->post(route('admin.seasons.store'), ['name' => '2027/2028', 'is_current' => 1, 'is_active' => 1])
        ->assertSessionHasNoErrors();

    expect(Season::current()->pluck('name')->all())->toBe(['2027/2028']);
});

test('deleting a team soft-deletes it and keeps historical fixtures readable', function () {
    $this->actingAs($this->admin);
    $fixture = Fixture::create([
        'competition_id' => $this->league->id, 'home_team_id' => $this->amapro->id, 'away_team_id' => $this->elite->id,
        'match_date' => today()->subWeek(), 'status' => 'completed', 'home_score' => 2, 'away_score' => 2,
    ]);

    $this->delete(route('admin.teams.destroy', $this->elite))->assertRedirect(route('admin.teams.index'));

    expect(Team::find($this->elite->id))->toBeNull()
        ->and($fixture->fresh()->awayTeam->name)->toBe('Elite FC');
    $this->get(route('matches.show', $fixture))->assertOk()->assertSee('Elite FC');
});

test('competitions with fixtures cannot be deleted', function () {
    Fixture::create([
        'competition_id' => $this->league->id, 'home_team_id' => $this->amapro->id, 'away_team_id' => $this->elite->id,
        'match_date' => today(), 'status' => 'scheduled',
    ]);

    $this->actingAs($this->admin)->delete(route('admin.competitions.destroy', $this->league))->assertSessionHas('error');
    expect(Competition::whereKey($this->league->id)->exists())->toBeTrue();
});

test('line-ups only accept players from the selected team', function () {
    $this->actingAs($this->admin);
    $fixture = Fixture::create([
        'competition_id' => $this->league->id, 'home_team_id' => $this->amapro->id, 'away_team_id' => $this->elite->id,
        'match_date' => today(), 'status' => 'scheduled',
    ]);
    $home = makePlayer($this->amapro);
    $away = makePlayer($this->elite);

    $this->put(route('admin.fixtures.lineups.update', [$fixture, $this->amapro]), ['players' => [$home->id, $away->id]])
        ->assertSessionHasErrors('players.1');

    $this->put(route('admin.fixtures.lineups.update', [$fixture, $this->amapro]), ['players' => [$home->id], 'starting' => [$home->id], 'captain_id' => $home->id])
        ->assertSessionHasNoErrors();
    expect($fixture->lineups()->first())->is_starting->toBeTrue()->is_captain->toBeTrue();
});

/* Uploads --------------------------------------------------------------- */

test('team logos are stored on the public disk and replaced cleanly', function () {
    Storage::fake('public');
    $this->actingAs($this->admin);

    $this->put(route('admin.teams.update', $this->amapro), ['name' => 'Amapro FC', 'is_active' => 1, 'logo' => UploadedFile::fake()->image('logo.png', 200, 200)])
        ->assertSessionHasNoErrors();
    $first = $this->amapro->fresh()->logo;
    Storage::disk('public')->assertExists($first);
    expect($first)->toStartWith('teams/logos/');

    $this->put(route('admin.teams.update', $this->amapro), ['name' => 'Amapro FC', 'is_active' => 1, 'logo' => UploadedFile::fake()->image('new.webp', 200, 200)]);
    Storage::disk('public')->assertMissing($first);

    $this->put(route('admin.teams.update', $this->amapro), ['name' => 'Amapro FC', 'is_active' => 1, 'logo' => UploadedFile::fake()->create('script.php', 10, 'text/x-php')])
        ->assertSessionHasErrors('logo');
});

/* Trials ---------------------------------------------------------------- */

test('the public trials form stores a validated application', function () {
    $this->post(route('trials.store'), [
        'full_name' => 'Ada Obi', 'email' => 'ada@example.com', 'phone' => '+234 801 234 5678',
        'matric_number' => 'BOU/2025/0042', 'level' => '200 Level', 'position' => 'midfielder', 'dominant_foot' => 'left',
    ])->assertRedirect(route('trials'))->assertSessionHas('application_submitted');

    $application = TrialApplication::firstOrFail();
    expect($application)->status->toBe('pending')->preferred_position->toBe('midfielder');

    $this->post(route('trials.store'), ['full_name' => '', 'email' => 'not-an-email'])->assertSessionHasErrors(['full_name', 'email', 'phone', 'position']);
    $this->post(route('trials.store'), ['website' => 'spam'] + $application->toArray())->assertSessionHasErrors('website');
});

/* Rendering ------------------------------------------------------------ */

test('every public page renders with an empty database', function (string $url) {
    $this->get($url)->assertOk();
})->with([
    '/', '/about', '/history', '/management', '/coaching-staff', '/teams', '/teams/amapro-fc', '/squad', '/squad?team=elite-fc&position=goalkeeper',
    '/player-development', '/basketball', '/join-the-team', '/fixtures', '/fixtures?tab=results', '/league-table', '/news', '/gallery',
    '/videos', '/fan-zone', '/membership', '/sponsors', '/faq', '/contact', '/match-day', '/privacy', '/terms',
]);

test('legacy static URLs redirect to their dynamic pages', function () {
    $this->get('/player-profile')->assertRedirect('/squad');
    $this->get('/news/article')->assertRedirect('/news');
    $this->get('/match-details')->assertRedirect(route('fixtures'));
});

test('unpublished news is not public', function () {
    $draft = NewsPost::create(['title' => 'Draft story', 'content' => 'Secret', 'is_published' => false]);
    $this->get(route('news.show', $draft))->assertNotFound();
});

test('every admin screen renders', function () {
    $this->actingAs($this->admin);
    $player = makePlayer($this->amapro);
    $fixture = Fixture::create([
        'competition_id' => $this->league->id, 'home_team_id' => $this->amapro->id, 'away_team_id' => $this->elite->id,
        'match_date' => today(), 'status' => 'scheduled',
    ]);
    $post = NewsPost::create(['title' => 'Story', 'content' => 'Body', 'is_published' => true]);
    $application = TrialApplication::create(['full_name' => 'Ada', 'email' => 'a@example.com']);

    foreach ([
        'admin.dashboard', 'admin.seasons.index', 'admin.seasons.create', 'admin.competitions.index', 'admin.competitions.create',
        'admin.teams.index', 'admin.teams.create', 'admin.players.index', 'admin.players.create', 'admin.fixtures.index',
        'admin.fixtures.create', 'admin.match-events.index', 'admin.news.index', 'admin.news.create', 'admin.gallery.index',
        'admin.gallery.create', 'admin.videos.index', 'admin.videos.create', 'admin.staff.index', 'admin.staff.create',
        'admin.trial-applications.index',
    ] as $route) {
        $this->get(route($route))->assertOk();
    }

    $this->get(route('admin.teams.show', $this->amapro))->assertOk();
    $this->get(route('admin.teams.edit', $this->amapro))->assertOk();
    $this->get(route('admin.players.edit', $player))->assertOk()->assertSee($player->full_name);
    $this->get(route('admin.fixtures.show', $fixture))->assertOk()->assertSee('Add event');
    $this->get(route('admin.fixtures.edit', $fixture))->assertOk();
    $this->get(route('admin.news.edit', $post))->assertOk();
    $this->get(route('admin.seasons.edit', Season::first()))->assertOk();
    $this->get(route('admin.competitions.edit', $this->league))->assertOk();
    $this->get(route('admin.trial-applications.show', $application))->assertOk();
    expect($application->fresh()->status)->toBe('reviewing');
});
