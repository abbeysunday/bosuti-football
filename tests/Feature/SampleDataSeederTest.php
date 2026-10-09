<?php

use App\Models\Competition;
use App\Models\Fixture;
use App\Models\NewsPost;
use App\Models\Player;
use App\Models\Staff;
use App\Models\Team;
use App\Services\LeagueTableService;
use App\Services\SampleGraphics;
use Database\Seeders\AdminSeeder;
use Database\Seeders\SampleDataSeeder;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');

    // Drawing ~150 images is slow; only the first test uses the real graphics.
    if (! str_contains(str_replace('_', ' ', $this->name()), 'sample season is complete')) {
        $this->app->instance(SampleGraphics::class, new class extends SampleGraphics
        {
            public function crest(string $initials, string $primary, string $secondary): string { return 'teams/logos/test.webp'; }

            public function playerCard(?int $number, string $primary, string $secondary, string $initials): string { return 'players/test.webp'; }

            public function staffPortrait(string $initials, string $primary = '#009a56', string $accent = '#f2cf70'): string { return 'staff/test.webp'; }
        });
    }

    $this->seed([AdminSeeder::class, SampleDataSeeder::class]);
});

test('the sample season is complete', function () {
    expect(Team::count())->toBe(7)
        ->and(Player::count())->toBe(126)
        ->and(Staff::where('type', 'coaching')->count())->toBe(16)
        ->and(Staff::where('type', 'management')->count())->toBe(6)
        ->and(Fixture::count())->toBe(42)
        ->and(Fixture::completed()->count())->toBeGreaterThan(0)
        ->and(Fixture::upcoming()->count())->toBeGreaterThan(0)
        ->and(NewsPost::published()->count())->toBeGreaterThanOrEqual(5);

    // Every team, player and staff member has a generated image on the public disk.
    foreach ([Team::pluck('logo'), Player::pluck('photo'), Staff::pluck('photo')] as $paths) {
        $paths->each(fn ($path) => Storage::disk('public')->assertExists($path));
    }
});

test('every played match has events and line-ups consistent with its score', function () {
    foreach (Fixture::completed()->with('matchEvents', 'lineups')->get() as $fixture) {
        $goalsFor = fn (int $teamId) => $fixture->matchEvents
            ->filter(fn ($e) => (in_array($e->type, ['goal', 'penalty_scored']) && $e->team_id === $teamId)
                || ($e->type === 'own_goal' && $e->team_id !== $teamId))
            ->count();

        expect($goalsFor($fixture->home_team_id))->toBe($fixture->home_score)
            ->and($goalsFor($fixture->away_team_id))->toBe($fixture->away_score);

        foreach ([$fixture->home_team_id, $fixture->away_team_id] as $teamId) {
            $sheet = $fixture->lineups->where('team_id', $teamId);
            expect($sheet->where('is_starting', true))->toHaveCount(11)
                ->and($sheet->where('is_starting', false))->toHaveCount(5)
                ->and($sheet->where('is_captain', true))->toHaveCount(1);
        }

        // Every event's players belong to the event's team.
        foreach ($fixture->matchEvents as $event) {
            expect(Player::find($event->player_id)->team_id)->toBe($event->team_id);
        }
    }
});

test('each team plays every other team home and away', function () {
    $pairs = Fixture::get()->map(fn ($f) => "{$f->home_team_id}-{$f->away_team_id}");

    expect($pairs->unique())->toHaveCount(42);
    Team::pluck('id')->each(fn ($id) => expect(Fixture::involving($id)->count())->toBe(12));
});

test('the standings are consistent with the results', function () {
    $table = app(LeagueTableService::class)->generate(Competition::first());

    expect($table)->toHaveCount(7)
        ->and($table->sum('won'))->toBe($table->sum('lost'))
        ->and($table->sum('goals_for'))->toBe($table->sum('goals_against'))
        ->and($table->sum('played'))->toBe(Fixture::completed()->count() * 2);
});

test('every public page renders with the sample season', function () {
    $fixture = Fixture::completed()->first();
    $player = Player::first();

    foreach (['/', '/teams', "/teams/{$player->team->slug}", '/squad', "/players/{$player->slug}", '/fixtures', '/fixtures?tab=results',
        "/matches/{$fixture->id}", '/league-table', '/news', '/news/' . NewsPost::first()->slug, '/gallery', '/management', '/coaching-staff'] as $url) {
        $this->get($url)->assertOk();
    }
});

test('running it again does not duplicate anything', function () {
    $this->seed(SampleDataSeeder::class);

    expect(Player::count())->toBe(126)->and(Fixture::count())->toBe(42);
});
