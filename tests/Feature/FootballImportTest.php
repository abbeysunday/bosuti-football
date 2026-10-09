<?php

use App\Models\Fixture;
use App\Models\MatchEvent;
use App\Models\Player;
use App\Models\Staff;
use App\Models\Team;
use App\Services\FootballImporter;
use App\Services\LeagueTableService;
use App\Services\PlayerStatisticsService;
use Database\Seeders\CompetitionSeeder;
use Database\Seeders\SeasonSeeder;
use Database\Seeders\TeamSeeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed([SeasonSeeder::class, CompetitionSeeder::class, TeamSeeder::class]);
    Storage::fake('public');

    $this->dir = storage_path('framework/testing/import-' . uniqid());
    File::makeDirectory("{$this->dir}/photos/players", 0755, true);

    $this->write = function (string $file, array $lines) {
        file_put_contents("{$this->dir}/{$file}", "\xEF\xBB\xBF" . implode("\r\n", $lines) . "\r\n"); // Excel-style BOM + CRLF
    };

    // Test fixtures only (made-up rows used to exercise the importer).
    ($this->write)('teams.csv', [
        'name,short_name,primary_color,secondary_color,coach_name,captain_name,founded_year,description,logo,is_active',
        'Amapro FC,AMA,#0A7A3D,,Coach A,,2019,Test description,,yes',
    ]);
    ($this->write)('players.csv', [
        'team,first_name,last_name,jersey_number,position,department,level,matric_number,state_of_origin,dominant_foot,height,bio,is_captain,is_featured,is_active,photo',
        'Amapro FC,Test,Striker,9,FWD,Computer Science,300,MAT/001,Ekiti,right,1.80 m,,no,yes,yes,striker.png',
        'Amapro FC,Test,Playmaker,8,midfielder,Physics,200,MAT/002,,left,,,yes,no,,',
        'Elite FC,Test,Keeper,1,GK,Chemistry,100 Level,MAT/003,,right,,,no,no,yes,',
    ]);
    ($this->write)('staff.csv', [
        'name,role,type,team,bio,sort_order,is_active,photo',
        'Test Coach,Head Coach,coaching,Amapro FC,,1,yes,',
        'Test Coordinator,Football Coordinator,management,,,1,yes,',
    ]);
    ($this->write)('fixtures.csv', [
        'competition,date,time,home_team,away_team,venue,matchday,status,home_score,away_score,referee,attendance,featured,summary',
        ',14/10/2026,4:00 PM,Amapro FC,Elite FC,Sports Complex,1,,2,1,,,,Close game',
        ',2026-10-21,16:30,Elite FC,Young Boys FC,Sports Complex,2,,,,,,yes,',
    ]);
    ($this->write)('match_events.csv', [
        'date,home_team,away_team,minute,added_time,type,team,player,related_player,note',
        '2026-10-14,Amapro FC,Elite FC,12,,goal,Amapro FC,Test Striker,Test Playmaker,',
        '2026-10-14,Amapro FC,Elite FC,62,,goal,Amapro FC,#9,,',
        '2026-10-14,Amapro FC,Elite FC,80,,yellow_card,Elite FC,#1,,',
    ]);

    $png = imagecreatetruecolor(1200, 1600);
    imagepng($png, "{$this->dir}/photos/players/striker.png");
});

afterEach(fn () => File::deleteDirectory($this->dir));

test('a full import creates everything and feeds standings and statistics', function () {
    $result = app(FootballImporter::class)->import($this->dir);

    expect($result['errors'])->toBe([])
        ->and($result['counts']['players']['created'])->toBe(3)
        ->and($result['counts']['fixtures']['created'])->toBe(2)
        ->and($result['counts']['match events']['created'])->toBe(3);

    $team = Team::where('name', 'Amapro FC')->first();
    expect($team)->short_name->toBe('AMA')->primary_color->toBe('#0a7a3d')->coach_name->toBe('Coach A');

    $striker = Player::where('matric_number', 'MAT/001')->first();
    expect($striker)->position->toBe('forward')->level->toBe('300 Level')->is_featured->toBeTrue()
        ->and($striker->photo)->toStartWith('players/')->toEndWith('.webp');
    [$w, $h] = getimagesizefromstring(Storage::disk('public')->get($striker->photo));
    expect([$w, $h])->toBe([900, 1200]);

    expect(Player::where('matric_number', 'MAT/003')->value('position'))->toBe('goalkeeper');
    expect(Staff::count())->toBe(2);

    $played = Fixture::where('status', 'completed')->first();
    expect($played)->home_score->toBe(2)->kickoff_time->toStartWith('16:00')->match_date->toDateString()->toBe('2026-10-14');
    expect(Fixture::where('status', 'scheduled')->value('featured'))->toBeTruthy();

    $table = app(LeagueTableService::class)->generate($played->competition);
    expect($table->first()->team->name)->toBe('Amapro FC')->and($table->first()->points)->toBe(3);

    $stats = app(PlayerStatisticsService::class)->forPlayer($striker);
    expect($stats)->goals->toBe(2)->appearances->toBe(1);
    expect(app(PlayerStatisticsService::class)->forPlayer(Player::where('matric_number', 'MAT/002')->first())['assists'])->toBe(1);
});

test('running the import again updates instead of duplicating', function () {
    app(FootballImporter::class)->import($this->dir);

    // Correct a score and re-run.
    ($this->write)('fixtures.csv', [
        'competition,date,time,home_team,away_team,venue,matchday,status,home_score,away_score,referee,attendance,featured,summary',
        ',14/10/2026,4:00 PM,Amapro FC,Elite FC,Sports Complex,1,,3,1,,,,',
    ]);
    $result = app(FootballImporter::class)->import($this->dir);

    expect($result['errors'])->toBe([])
        ->and(Player::count())->toBe(3)
        ->and(Fixture::count())->toBe(2)
        ->and(MatchEvent::count())->toBe(3)
        ->and(Fixture::where('status', 'completed')->value('home_score'))->toBe(3);
});

test('any invalid row rolls back the whole import with clear messages', function () {
    unlink("{$this->dir}/match_events.csv"); // this test replaces the players those events refer to
    ($this->write)('players.csv', [
        'team,first_name,last_name,jersey_number,position',
        'Amapro FC,Good,Row,9,forward',
        'Amapro,Bad,Team,10,forward',
        'Amapro FC,Bad,Position,11,sweeper',
        'Amapro FC,Duplicate,Number,9,defender',
    ]);

    $result = app(FootballImporter::class)->import($this->dir);

    expect($result['errors'])->toHaveCount(3)
        ->and($result['errors'][0])->toContain('players.csv line 3')->toContain('Unknown team "Amapro"')
        ->and($result['errors'][1])->toContain('line 4')->toContain('position must be')
        ->and($result['errors'][2])->toContain('line 5')->toContain('shirt #9 is already used');

    expect(Player::count())->toBe(0)->and(Fixture::count())->toBe(0);
    expect(Storage::disk('public')->allFiles())->toBe([]);
});

test('a dry run checks the files but saves nothing', function () {
    $this->artisan('football:import', ['--path' => $this->dir, '--dry-run' => true])
        ->expectsOutputToContain('All rows are valid')
        ->assertSuccessful();

    expect(Player::count())->toBe(0)->and(Fixture::count())->toBe(0);
    expect(Storage::disk('public')->allFiles())->toBe([]);
});

test('the command reports failures with a non-zero exit code', function () {
    ($this->write)('fixtures.csv', ['date,home_team,away_team', '2026-10-14,Amapro FC,Amapro FC']);

    $this->artisan('football:import', ['--path' => $this->dir])
        ->expectsOutputToContain('cannot play against itself')
        ->assertFailed();
});

test('a missing photo is a warning, not a failure', function () {
    unlink("{$this->dir}/match_events.csv");
    ($this->write)('players.csv', [
        'team,first_name,last_name,position,photo',
        'Amapro FC,No,Photo,forward,missing.jpg',
    ]);

    $result = app(FootballImporter::class)->import($this->dir);

    expect($result['errors'])->toBe([])
        ->and($result['warnings'][0])->toContain('Photo not found: photos/players/missing.jpg')
        ->and(Player::where('last_name', 'Photo')->value('photo'))->toBeNull();
});
