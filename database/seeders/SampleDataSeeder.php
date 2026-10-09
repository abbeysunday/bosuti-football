<?php

namespace Database\Seeders;

use App\Models\Competition;
use App\Models\ContactMessage;
use App\Models\Fixture;
use App\Models\GalleryItem;
use App\Models\NewsPost;
use App\Models\Player;
use App\Models\Season;
use App\Models\Staff;
use App\Models\Team;
use App\Models\TrialApplication;
use App\Models\User;
use App\Services\SampleGraphics;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * A complete, realistic season for the seven BOUESTI teams, so every page of the site can be
 * presented with data: 126 players, coaches and management, a full double round-robin league
 * (played matchdays with consistent results, goals, assists, cards, substitutions and line-ups;
 * upcoming matchdays scheduled), match reports, announcements and gallery photos.
 *
 * IMPORTANT: the people are FICTIONAL (realistic Nigerian names, not real students or staff).
 * Replace them with the real squads via `php artisan football:import`, or start clean with
 * `php artisan migrate:fresh --seed` (SEED_SAMPLE_DATA=false).
 *
 * Deterministic: the same season is generated every time.
 *
 *   php artisan db:seed --class=SampleDataSeeder
 */
class SampleDataSeeder extends Seeder
{
    /** Team identity used for graphics and match simulation (strength 60–80). */
    private const TEAMS = [
        'Amapro FC' => ['AMA', '#0a7a3d', '#f2cf70', 78],
        'Elite FC' => ['ELI', '#1e3a8a', '#ffffff', 75],
        'Young Boys FC' => ['YBF', '#c1121f', '#ffd166', 71],
        'CSC Elites' => ['CSC', '#0b6e99', '#f4f4f4', 73],
        'Engines Boys' => ['ENG', '#e76f00', '#141414', 67],
        'Sovereignty FC' => ['SOV', '#5b1a8f', '#f2cf70', 70],
        'AMCOMS' => ['AMC', '#1f2937', '#10b981', 65],
    ];

    private const FIRST_NAMES = [
        'Adebayo', 'Oluwaseun', 'Tunde', 'Femi', 'Kunle', 'Segun', 'Damilola', 'Ayomide', 'Tobiloba', 'Kayode',
        'Sola', 'Bamidele', 'Olamide', 'Ifeoluwa', 'Toluwani', 'Babatunde', 'Olumide', 'Temitope', 'Ademola', 'Gbenga',
        'Akinola', 'Dapo', 'Wale', 'Niyi', 'Yinka', 'Rotimi', 'Kolade', 'Seyi', 'Deji', 'Ibukun',
        'Opeyemi', 'Emmanuel', 'Samuel', 'David', 'Daniel', 'Joshua', 'Victor', 'Michael', 'Stephen', 'Godwin',
        'Chinedu', 'Emeka', 'Obinna', 'Ikenna', 'Kelechi', 'Uchenna', 'Chidi', 'Ebuka', 'Somto', 'Tochukwu',
        'Ibrahim', 'Abdullahi', 'Yusuf', 'Aliyu', 'Musa', 'Sani', 'Favour', 'Precious', 'Praise', 'Blessing',
        'Timilehin', 'Oluwafemi', 'Ayodeji', 'Oreoluwa', 'Mayowa', 'Tomiwa', 'Jide', 'Lanre', 'Fisayo', 'Pelumi',
    ];

    private const LAST_NAMES = [
        'Adeyemi', 'Ogunleye', 'Adebayo', 'Ojo', 'Olatunji', 'Akinola', 'Babalola', 'Ogunbiyi', 'Adewale', 'Oladipo',
        'Afolabi', 'Olaniyan', 'Akintola', 'Ajayi', 'Adeleke', 'Omotayo', 'Aluko', 'Fagbemi', 'Ogundipe', 'Olabode',
        'Adeniran', 'Akinwale', 'Falade', 'Ayodele', 'Bamgbose', 'Ogunsola', 'Faleye', 'Oluwadare', 'Ajibade', 'Ilesanmi',
        'Akinyemi', 'Oyewole', 'Fasanmi', 'Ogunmodede', 'Adegoke', 'Oyeniyi', 'Olowookere', 'Ojomo', 'Arogundade', 'Fatunla',
        'Okafor', 'Nwosu', 'Eze', 'Okeke', 'Chukwu', 'Nnaji', 'Ugwu', 'Onyekachi', 'Obiora', 'Anyanwu',
        'Bello', 'Garba', 'Danladi', 'Suleiman', 'Lawal', 'Salami', 'Yusuf', 'Adamu', 'Ibe', 'Etim',
    ];

    private const DEPARTMENTS = [
        'Computer Science', 'Mathematics', 'Physics', 'Chemistry', 'Biology', 'Economics', 'Accounting',
        'Business Administration', 'Political Science', 'English Language', 'Human Kinetics and Health Education',
        'Guidance and Counselling', 'Agricultural Science', 'Fine and Applied Arts', 'Educational Management', 'Geography',
    ];

    private const STATES = [
        'Ekiti', 'Ekiti', 'Ekiti', 'Ekiti', 'Ondo', 'Ondo', 'Osun', 'Oyo', 'Ogun', 'Lagos', 'Kwara', 'Kogi',
        'Edo', 'Delta', 'Enugu', 'Anambra', 'Imo', 'Kaduna', 'Niger', 'Benue',
    ];

    /** Squad template: position => shirt numbers. */
    private const SQUAD = [
        'goalkeeper' => [1, 16],
        'defender' => [2, 3, 4, 5, 12, 15],
        'midfielder' => [6, 8, 10, 14, 17, 20],
        'forward' => [7, 9, 11, 19],
    ];

    private const REFEREES = ['Mr. Olusegun Fadare', 'Mr. Kehinde Ajiboye', 'Mr. Bolaji Oguntuase', 'Mr. Femi Adesida', 'Mr. Ayo Olorunfemi', 'Mr. Tayo Akinbobola'];

    private const VENUE = 'University Sports Complex, BOUESTI';

    /** Free-licence football photos (Unsplash) used for news and gallery only. */
    private const PHOTOS = [
        'https://images.unsplash.com/photo-1522778119026-d647f0565c6a?auto=format&fit=crop&w=1600&q=80',
        'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&w=1600&q=80',
        'https://images.unsplash.com/photo-1579952363873-27f3bade9f55?auto=format&fit=crop&w=1600&q=80',
        'https://images.unsplash.com/photo-1560272564-c83b66b1ad12?auto=format&fit=crop&w=1600&q=80',
        'https://images.unsplash.com/photo-1592656094267-764a45160876?auto=format&fit=crop&w=1600&q=80',
        'https://images.unsplash.com/photo-1517466787929-bc90951d0974?auto=format&fit=crop&w=1600&q=80',
        'https://images.unsplash.com/photo-1489944440615-453fc2b6a9a9?auto=format&fit=crop&w=1600&q=80',
        'https://images.unsplash.com/photo-1517927033932-b3d18e61fb3a?auto=format&fit=crop&w=1600&q=80',
        'https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&w=1600&q=80',
        'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=1600&q=80',
    ];

    private SampleGraphics $graphics;

    /** @var array<int, array{team: Team, strength: int, players: Collection}> */
    private array $squads = [];

    /** @var array<string, bool> */
    private array $usedNames = [];

    public function run(SampleGraphics $graphics): void
    {
        $this->graphics = $graphics;

        if (Player::exists() || Fixture::exists()) {
            $this->command?->warn('SampleDataSeeder skipped: players or fixtures already exist. Run `php artisan migrate:fresh --seed` for a clean database first.');

            return;
        }

        $this->call([SeasonSeeder::class, CompetitionSeeder::class, TeamSeeder::class]);
        mt_srand(20262027);

        DB::transaction(function () {
            $this->teamsAndSquads();
            $this->staff();
            $fixtures = $this->season();
            $this->news($fixtures);
            $this->gallery();
            $this->inbox();
        });

        $this->command?->info(sprintf(
            'Sample season created: %d players, %d staff, %d fixtures (%d played), %d match events, %d news posts.',
            Player::count(), Staff::count(), Fixture::count(), Fixture::completed()->count(),
            DB::table('match_events')->count(), NewsPost::count(),
        ));
        $this->command?->warn('All people in this sample season are fictional. Replace them with the real squads before publishing the site.');
    }

    /* Teams, players & staff ------------------------------------------------ */

    private function teamsAndSquads(): void
    {
        foreach (self::TEAMS as $name => [$short, $primary, $secondary, $strength]) {
            $team = Team::where('name', $name)->firstOrFail();
            $team->fill([
                'short_name' => $short,
                'primary_color' => $primary,
                'secondary_color' => $secondary,
                'description' => "{$name} is one of the student football clubs competing in the BOUESTI internal league, "
                    . 'bringing together students from across the university\'s faculties to train, compete and represent their club with pride.',
                'logo' => $this->graphics->crest($short, $primary, $secondary),
                'is_active' => true,
            ])->save();

            $players = collect();
            foreach (self::SQUAD as $position => $numbers) {
                foreach ($numbers as $i => $number) {
                    [$first, $last] = $this->uniqueName();
                    $level = [100, 200, 200, 300, 300, 400][mt_rand(0, 5)];

                    $players->push(Player::create([
                        'team_id' => $team->id,
                        'first_name' => $first,
                        'last_name' => $last,
                        'jersey_number' => $number,
                        'position' => $position,
                        'department' => self::DEPARTMENTS[mt_rand(0, count(self::DEPARTMENTS) - 1)],
                        'level' => "{$level} Level",
                        'matric_number' => sprintf('BOU/%02d/%04d', 26 - intdiv($level, 100), mt_rand(1000, 9999)),
                        'state_of_origin' => self::STATES[mt_rand(0, count(self::STATES) - 1)],
                        'dominant_foot' => mt_rand(1, 100) <= 72 ? 'right' : (mt_rand(1, 100) <= 85 ? 'left' : 'both'),
                        'height' => sprintf('%.2f m', mt_rand($position === 'goalkeeper' ? 180 : 166, $position === 'goalkeeper' ? 193 : 188) / 100),
                        'bio' => $this->bio($first, $position, $name),
                        'is_captain' => $number === 4,
                        'is_featured' => $number === 9,
                        'is_active' => true,
                        'photo' => $this->graphics->playerCard($number, $primary, $secondary, $short),
                    ]));
                }
            }

            $team->update(['captain_name' => $players->firstWhere('is_captain', true)->full_name]);
            $this->squads[$team->id] = ['team' => $team, 'strength' => $strength, 'players' => $players];
        }
    }

    private function staff(): void
    {
        $management = [
            ['Director of Sports', 'Dr.'], ['Football Coordinator', 'Mr.'], ['League Secretary', 'Mrs.'],
            ['Welfare Officer', 'Mrs.'], ['Media & Communications Officer', 'Mr.'], ['Head of Match Officials', 'Mr.'],
        ];
        foreach ($management as $order => [$role, $title]) {
            [$first, $last] = $this->uniqueName($title === 'Mrs.' ? ['Adenike', 'Funmilayo', 'Omolara', 'Bukola', 'Yetunde', 'Folake'] : null);
            $this->makeStaff("{$title} {$first} {$last}", $role, 'management', null, $order);
        }

        $order = 0;
        foreach ($this->squads as ['team' => $team]) {
            foreach (['Head Coach', 'Assistant Coach'] as $role) {
                [$first, $last] = $this->uniqueName();
                $member = $this->makeStaff("Mr. {$first} {$last}", $role, 'coaching', $team, $order++);
                if ($role === 'Head Coach') {
                    $team->update(['coach_name' => $member->name]);
                }
            }
        }

        foreach (['Goalkeeping Coach', 'Fitness & Conditioning Coach'] as $role) {
            [$first, $last] = $this->uniqueName();
            $this->makeStaff("Mr. {$first} {$last}", $role, 'coaching', null, $order++);
        }
    }

    private function makeStaff(string $name, string $role, string $type, ?Team $team, int $order): Staff
    {
        $initials = collect(explode(' ', $name))->slice(1)->map(fn ($w) => mb_substr($w, 0, 1))->implode('');
        $bio = $team
            ? "{$role} of {$team->name}, responsible for training sessions, team selection and player development."
            : "Serves as {$role} for football activities at BOUESTI.";

        return Staff::create([
            'name' => $name,
            'role' => $role,
            'type' => $type,
            'team_id' => $team?->id,
            'bio' => $bio,
            'sort_order' => $order,
            'is_active' => true,
            'photo' => $this->graphics->staffPortrait($initials, $team?->primary_color ?? '#009a56', $team?->secondary_color === '#ffffff' ? '#f2cf70' : ($team?->secondary_color ?? '#f2cf70')),
        ]);
    }

    /* The league season --------------------------------------------------- */

    /** Double round-robin: 14 matchdays of 3 matches (one team rests each week). */
    private function season(): Collection
    {
        $competition = Competition::where('season_id', Season::currentOrLatest()->id)->where('type', 'league')->firstOrFail();
        $competition->update(['start_date' => $this->firstMatchday()]);

        $ids = array_keys($this->squads);
        $ids[] = null; // bye
        $rounds = [];
        $n = count($ids);

        for ($round = 0; $round < $n - 1; $round++) {
            $pairs = [];
            for ($i = 0; $i < $n / 2; $i++) {
                [$a, $b] = [$ids[$i], $ids[$n - 1 - $i]];
                if ($a && $b) {
                    $pairs[] = $round % 2 ? [$b, $a] : [$a, $b];
                }
            }
            $rounds[] = $pairs;
            $ids = array_merge([$ids[0]], [$ids[$n - 1]], array_slice($ids, 1, $n - 2)); // rotate, first fixed
        }
        $rounds = array_merge($rounds, array_map(fn ($pairs) => array_map(fn ($p) => [$p[1], $p[0]], $pairs), $rounds));

        $fixtures = collect();
        $kickoffs = ['10:00', '13:00', '16:00'];

        foreach ($rounds as $index => $pairs) {
            $date = $this->firstMatchday()->addWeeks($index);

            foreach (array_values($pairs) as $slot => [$homeId, $awayId]) {
                $kickoff = Carbon::parse($date->toDateString() . ' ' . $kickoffs[$slot]);
                $played = $kickoff->copy()->addHours(2)->isPast();

                $fixture = Fixture::create([
                    'competition_id' => $competition->id,
                    'home_team_id' => $homeId,
                    'away_team_id' => $awayId,
                    'match_date' => $date->toDateString(),
                    'kickoff_time' => $kickoffs[$slot],
                    'venue' => self::VENUE,
                    'matchday' => $index + 1,
                    'status' => 'scheduled',
                ]);

                if ($played) {
                    $this->play($fixture);
                }

                $fixtures->push($fixture);
            }
        }

        // Highlight the next match on the homepage.
        Fixture::upcoming()->first()?->update(['featured' => true]);

        return $fixtures;
    }

    /** Matchday 1 falls on the Saturday five weeks ago, so the season is mid-way through. */
    private function firstMatchday(): Carbon
    {
        return today()->subWeeks(5)->startOfWeek(Carbon::SATURDAY);
    }

    /** Simulates a played match: line-ups, substitutions, goals, assists and cards that add up to the score. */
    private function play(Fixture $fixture): void
    {
        $home = $this->squads[$fixture->home_team_id];
        $away = $this->squads[$fixture->away_team_id];

        $homeGoals = $this->poisson(1.15 * ($home['strength'] / $away['strength']) ** 1.6 + 0.2);
        $awayGoals = $this->poisson(1.0 * ($away['strength'] / $home['strength']) ** 1.6);

        $sides = [
            $fixture->home_team_id => $this->lineup($fixture, $home),
            $fixture->away_team_id => $this->lineup($fixture, $away),
        ];

        $events = [];
        foreach ($sides as $teamId => $side) {
            foreach ($side['subs'] as [$off, $on, $minute]) {
                $events[] = ['team_id' => $teamId, 'player_id' => $off->id, 'related_player_id' => $on->id, 'type' => 'substitution', 'minute' => $minute];
            }
        }

        foreach ([[$fixture->home_team_id, $fixture->away_team_id, $homeGoals], [$fixture->away_team_id, $fixture->home_team_id, $awayGoals]] as [$teamId, $opponentId, $goals]) {
            for ($g = 0; $g < $goals; $g++) {
                $minute = mt_rand(3, 90);
                $added = $minute === 90 ? mt_rand(1, 4) : null;

                if (mt_rand(1, 100) <= 4) { // own goal by an opponent
                    $player = $this->pick($this->onPitch($sides[$opponentId], $minute), ['defender' => 6, 'midfielder' => 2]);
                    $events[] = ['team_id' => $opponentId, 'player_id' => $player->id, 'related_player_id' => null, 'type' => 'own_goal', 'minute' => $minute, 'additional_minute' => $added];
                    continue;
                }

                $onPitch = $this->onPitch($sides[$teamId], $minute);
                $scorer = $this->pick($onPitch, ['forward' => 10, 'midfielder' => 5, 'defender' => 1]);
                $assist = mt_rand(1, 100) <= 68
                    ? $this->pick($onPitch->reject(fn ($p) => $p->id === $scorer->id), ['midfielder' => 6, 'forward' => 4, 'defender' => 2])
                    : null;
                $penalty = mt_rand(1, 100) <= 9;

                $events[] = [
                    'team_id' => $teamId, 'player_id' => $scorer->id, 'related_player_id' => $penalty ? null : $assist?->id,
                    'type' => $penalty ? 'penalty_scored' : 'goal', 'minute' => $minute, 'additional_minute' => $added,
                ];
            }

            // Discipline.
            $yellows = $this->poisson(1.7);
            for ($y = 0; $y < $yellows; $y++) {
                $minute = mt_rand(10, 90);
                $player = $this->pick($this->onPitch($sides[$teamId], $minute), ['defender' => 5, 'midfielder' => 4, 'forward' => 2, 'goalkeeper' => 1]);
                $events[] = ['team_id' => $teamId, 'player_id' => $player->id, 'related_player_id' => null, 'type' => 'yellow_card', 'minute' => $minute];
            }
            if (mt_rand(1, 100) <= 5) {
                $minute = mt_rand(55, 88);
                $player = $this->pick($this->onPitch($sides[$teamId], $minute), ['defender' => 5, 'midfielder' => 3]);
                $events[] = ['team_id' => $teamId, 'player_id' => $player->id, 'related_player_id' => null, 'type' => 'red_card', 'minute' => $minute];
            }
            if (mt_rand(1, 100) <= 6) {
                $minute = mt_rand(20, 85);
                $player = $this->pick($this->onPitch($sides[$teamId], $minute), ['forward' => 5, 'midfielder' => 3]);
                $events[] = ['team_id' => $teamId, 'player_id' => $player->id, 'related_player_id' => null, 'type' => 'penalty_missed', 'minute' => $minute];
            }
        }

        foreach ($events as $event) {
            $fixture->matchEvents()->create($event);
        }

        $referee = self::REFEREES[mt_rand(0, count(self::REFEREES) - 1)];
        $fixture->update([
            'status' => 'completed',
            'home_score' => $homeGoals,
            'away_score' => $awayGoals,
            'referee' => $referee,
            'attendance' => mt_rand(18, 90) * 10,
            'report' => $this->summary($fixture->fresh(['homeTeam', 'awayTeam', 'matchEvents.player']), $homeGoals, $awayGoals),
        ]);
    }

    /**
     * Picks a starting XI (4-4-2) and five substitutes, saves the line-up and plans three substitutions.
     *
     * @return array{starters: Collection, subs: array<int, array{0: Player, 1: Player, 2: int}>}
     */
    private function lineup(Fixture $fixture, array $squad): array
    {
        $byPosition = $squad['players']->groupBy('position');
        $starters = collect()
            ->push(mt_rand(1, 100) <= 85 ? $byPosition['goalkeeper'][0] : $byPosition['goalkeeper'][1])
            ->merge($this->choose($byPosition['defender'], 4))
            ->merge($this->choose($byPosition['midfielder'], 4))
            ->merge($this->choose($byPosition['forward'], 2));
        $bench = $squad['players']->reject(fn ($p) => $starters->contains('id', $p->id))->values();
        $bench = $this->choose($bench->reject(fn ($p) => $p->position === 'goalkeeper' && $bench->where('position', 'goalkeeper')->count() > 1), 5);

        $captain = $starters->firstWhere('is_captain', true) ?? $starters->firstWhere('position', 'defender');

        // Three substitutions after the hour, like for like where possible.
        $subs = [];
        $available = $bench->where('position', '!=', 'goalkeeper')->values();
        $outfield = $starters->where('position', '!=', 'goalkeeper')->values();
        foreach ($this->choose($available, 3) as $on) {
            $off = $outfield->firstWhere('position', $on->position) ?? $outfield->first();
            $outfield = $outfield->reject(fn ($p) => $p->id === $off->id)->values();
            $subs[] = [$off, $on, mt_rand(55, 85)];
        }

        foreach ($starters->concat($bench) as $player) {
            $isStarter = $starters->contains('id', $player->id);
            $wentOff = collect($subs)->first(fn ($s) => $s[0]->id === $player->id);
            $cameOn = collect($subs)->first(fn ($s) => $s[1]->id === $player->id);

            $fixture->lineups()->create([
                'player_id' => $player->id,
                'team_id' => $squad['team']->id,
                'is_starting' => $isStarter,
                'position' => $player->position,
                'shirt_number' => $player->jersey_number,
                'is_captain' => $captain && $captain->id === $player->id,
                'minutes_played' => $isStarter ? ($wentOff[2] ?? 90) : ($cameOn ? 90 - $cameOn[2] : 0),
            ]);
        }

        return ['starters' => $starters, 'subs' => $subs];
    }

    /** Players on the pitch at a given minute (starters minus those subbed off, plus those subbed on). */
    private function onPitch(array $side, int $minute): Collection
    {
        $players = $side['starters'];
        foreach ($side['subs'] as [$off, $on, $at]) {
            if ($minute >= $at) {
                $players = $players->reject(fn ($p) => $p->id === $off->id)->push($on);
            }
        }

        return $players->values();
    }

    /** Weighted random player by position (unlisted positions weigh 0.2). */
    private function pick(Collection $players, array $weights): Player
    {
        $scored = $players->map(fn ($p) => [$p, ($weights[$p->position] ?? 0.2) * 10]);
        $roll = mt_rand(1, (int) max(1, $scored->sum(fn ($s) => $s[1])));

        foreach ($scored as [$player, $weight]) {
            if (($roll -= $weight) <= 0) {
                return $player;
            }
        }

        return $players->first();
    }

    private function choose(Collection $items, int $count): Collection
    {
        return $items->sortBy(fn () => mt_rand())->take($count)->values();
    }

    private function poisson(float $lambda): int
    {
        $limit = exp(-$lambda);
        $k = 0;
        $p = 1.0;

        do {
            $k++;
            $p *= mt_rand() / mt_getrandmax();
        } while ($p > $limit && $k < 8);

        return min($k - 1, 6);
    }

    /* Content --------------------------------------------------------------- */

    private function summary(Fixture $fixture, int $home, int $away): string
    {
        $scorers = $fixture->matchEvents
            ->whereIn('type', ['goal', 'penalty_scored'])
            ->groupBy('player_id')
            ->map(fn ($goals) => $goals->first()->player->full_name . ' ' . $goals->map(fn ($g) => $g->minute_label)->implode(', '))
            ->implode('; ');

        $result = $home === $away
            ? "{$fixture->homeTeam->name} and {$fixture->awayTeam->name} shared the points in a {$home}–{$away} draw"
            : ($home > $away
                ? "{$fixture->homeTeam->name} beat {$fixture->awayTeam->name} {$home}–{$away}"
                : "{$fixture->awayTeam->name} won {$away}–{$home} away at {$fixture->homeTeam->name}");

        return "{$result} on matchday {$fixture->matchday} of the BOUESTI Football League." . ($scorers ? " Goals: {$scorers}." : '');
    }

    private function news(Collection $fixtures): void
    {
        $author = User::where('role', User::ROLE_ADMIN)->value('id');
        $played = $fixtures->filter(fn ($f) => $f->fresh()->isCompleted())->map->fresh(['homeTeam', 'awayTeam', 'matchEvents.player', 'matchEvents.relatedPlayer']);
        $start = $this->firstMatchday();

        $posts = [[
            'title' => '2026/2027 BOUESTI Football League fixtures released',
            'category' => 'Competition',
            'content' => '<div>The fixture list for the new BOUESTI Football League season has been released. Seven student teams — Amapro FC, Elite FC, Young Boys FC, CSC Elites, Engines Boys, Sovereignty FC and AMCOMS — will meet home and away over fourteen matchdays.</div><h1>How the league works</h1><ul><li>Three points for a win, one for a draw</li><li>Ties on points are separated by goal difference, then goals scored</li><li>All matches are played at the University Sports Complex</li></ul><div>The full schedule is available on the Fixtures page.</div>',
            'published_at' => $start->copy()->subDays(10)->setTime(10, 0),
            'image' => self::PHOTOS[1],
        ], [
            'title' => 'Football trials open to all BOUESTI students',
            'category' => 'Announcement',
            'content' => '<div>Students who want to play for one of the university\'s football teams can now register for trials through the <strong>Join the Team</strong> page.</div><div><br></div><div>Coaches from all seven clubs will assess players across every position. Bring your student ID card and sports kit.</div>',
            'published_at' => $start->copy()->subDays(14)->setTime(9, 0),
            'image' => self::PHOTOS[2],
        ]];

        // Match reports for the biggest game of each played matchday (most recent three).
        foreach ($played->groupBy('matchday')->sortKeysDesc()->take(3) as $matchday => $games) {
            $game = $games->sortByDesc(fn ($f) => $f->home_score + $f->away_score)->first();
            $posts[] = [
                'title' => $this->headline($game),
                'category' => 'Match Report',
                'content' => $this->reportHtml($game),
                'published_at' => $game->match_date->copy()->addDay()->setTime(11, 0),
                'image' => self::PHOTOS[$matchday % count(self::PHOTOS)],
                'fixture_id' => $game->id,
            ];
        }

        $lastPlayed = $played->max('matchday');
        if ($lastPlayed) {
            $posts[] = [
                'title' => "Matchday " . ($lastPlayed + 1) . ' preview: title race heats up',
                'category' => 'Team News',
                'content' => '<div>With ' . $lastPlayed . ' rounds played, the top of the BOUESTI Football League table is tightening. Coaches report full squads available as teams prepare for another weekend of action at the University Sports Complex.</div><div><br></div><div>Check the Fixtures page for kick-off times and the League Table for the latest standings.</div>',
                'published_at' => now()->subDay()->setTime(15, 0),
                'image' => self::PHOTOS[6],
            ];
        }

        foreach ($posts as $post) {
            NewsPost::create([
                'title' => $post['title'],
                'category' => $post['category'],
                'content' => $post['content'],
                'featured_image' => $post['image'],
                'fixture_id' => $post['fixture_id'] ?? null,
                'author_id' => $author,
                'published_at' => $post['published_at'],
                'is_published' => true,
                'is_featured' => $post['category'] === 'Match Report',
            ]);
        }
    }

    private function headline(Fixture $f): string
    {
        [$home, $away] = [$f->homeTeam->name, $f->awayTeam->name];

        return match (true) {
            $f->home_score === $f->away_score => "{$home} and {$away} share the spoils in {$f->home_score}–{$f->away_score} draw",
            // Winner's score first, e.g. "4–1" even when the away side won.
            abs($f->home_score - $f->away_score) >= 3 => ($f->home_score > $f->away_score ? $home : $away) . ' cruise past ' . ($f->home_score > $f->away_score ? $away : $home)
                . ' in ' . max($f->home_score, $f->away_score) . '–' . min($f->home_score, $f->away_score) . ' win',
            $f->home_score > $f->away_score => "{$home} edge {$away} {$f->home_score}–{$f->away_score}",
            default => "{$away} claim {$f->away_score}–{$f->home_score} away win at {$home}",
        };
    }

    private function reportHtml(Fixture $f): string
    {
        $lines = $f->matchEvents->whereIn('type', ['goal', 'penalty_scored', 'own_goal'])->map(function ($e) {
            $what = match ($e->type) {
                'penalty_scored' => 'scored from the penalty spot',
                'own_goal' => 'turned the ball into his own net',
                default => 'scored' . ($e->relatedPlayer ? ' after good work from ' . e($e->relatedPlayer->full_name) : ''),
            };

            return '<li><strong>' . $e->minute_label . '</strong> — ' . e($e->player->full_name) . ' (' . e($e->team->name) . ") {$what}</li>";
        })->implode('');

        $cards = $f->matchEvents->where('type', 'yellow_card')->count();
        $reds = $f->matchEvents->where('type', 'red_card');

        return '<div>' . e($f->report) . '</div>'
            . ($lines ? '<h1>Goals</h1><ul>' . $lines . '</ul>' : '<div><br></div><div>Neither side could find a breakthrough in a tight contest.</div>')
            . '<h1>Discipline</h1><div>Referee ' . e($f->referee) . " showed {$cards} yellow " . ($cards === 1 ? 'card' : 'cards')
            . ($reds->isNotEmpty() ? ' and sent off ' . e($reds->first()->player->full_name) : '') . '. Attendance: ' . number_format($f->attendance) . '.</div>';
    }

    private function gallery(): void
    {
        $items = [
            ['Match day at the University Sports Complex', 'match', 9], ['Pre-match huddle', 'match', 0], ['Midfield battle', 'match', 3],
            ['Set-piece practice', 'training', 2], ['Evening training session', 'training', 5], ['Fitness drills', 'training', 7],
            ['Supporters in the stands', 'fans', 6], ['Celebrating a late winner', 'fans', 4], ['Warm-up routine', 'behind-scenes', 1],
            ['Kit room ready for kick-off', 'behind-scenes', 8], ['Goalkeeper training', 'players', 3], ['Squad photo day', 'players', 0],
        ];

        foreach ($items as $order => [$title, $category, $photo]) {
            GalleryItem::create([
                'title' => $title,
                'image' => self::PHOTOS[$photo],
                'category' => $category,
                'is_featured' => $order < 5,
                'sort_order' => $order,
            ]);
        }
    }

    /** A few trial applications and contact messages so the admin inbox can be demonstrated. */
    private function inbox(): void
    {
        foreach ([['forward', '200 Level', 'pending'], ['goalkeeper', '100 Level', 'pending'], ['midfielder', '300 Level', 'reviewing'], ['defender', '100 Level', 'accepted']] as $i => [$position, $level, $status]) {
            [$first, $last] = $this->uniqueName();
            $application = TrialApplication::create([
                'full_name' => "{$first} {$last}",
                'email' => strtolower("{$first}.{$last}") . '@example.com',
                'phone' => '+234 80' . mt_rand(10000000, 99999999),
                'matric_number' => sprintf('BOU/%02d/%04d', 25, mt_rand(1000, 9999)),
                'department' => self::DEPARTMENTS[mt_rand(0, count(self::DEPARTMENTS) - 1)],
                'level' => $level,
                'preferred_position' => $position,
                'dominant_foot' => 'right',
                'playing_experience' => 'Played for my secondary school team and in inter-hall competitions.',
            ]);
            $application->forceFill(['status' => $status, 'created_at' => now()->subDays(6 - $i)])->save();
        }

        foreach ([
            ['Watching matches', 'Are league matches open to all students and visitors, and is there an entry fee?'],
            ['Team kits', 'Where can supporters buy the official Amapro FC jersey for this season?'],
            ['Media request', 'Our departmental press club would like to cover the next matchday. Who should we contact?'],
        ] as [$subject, $message]) {
            [$first, $last] = $this->uniqueName();
            ContactMessage::create([
                'name' => "{$first} {$last}",
                'email' => strtolower("{$first}.{$last}") . '@example.com',
                'subject' => $subject,
                'message' => $message,
            ]);
        }
    }

    /* Names ------------------------------------------------------------------ */

    /** @return array{0: string, 1: string} */
    private function uniqueName(?array $firstNames = null): array
    {
        $firstNames ??= self::FIRST_NAMES;

        do {
            $first = $firstNames[mt_rand(0, count($firstNames) - 1)];
            $last = self::LAST_NAMES[mt_rand(0, count(self::LAST_NAMES) - 1)];
        } while (isset($this->usedNames["{$first} {$last}"]));

        $this->usedNames["{$first} {$last}"] = true;

        return [$first, $last];
    }

    private function bio(string $first, string $position, string $team): string
    {
        $roles = [
            'goalkeeper' => 'a commanding goalkeeper known for his shot-stopping and organisation of the back line',
            'defender' => 'a composed defender who reads the game well and is strong in the air',
            'midfielder' => 'an energetic midfielder who links defence and attack with his passing range',
            'forward' => 'a direct forward with pace and an eye for goal',
        ];

        return "{$first} is {$roles[$position]}. He balances his studies with training and match days for {$team}.";
    }
}
