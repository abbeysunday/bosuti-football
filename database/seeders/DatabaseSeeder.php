<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Structural data (season, league, the seven teams, admin) is always seeded.
     *
     * With SEED_SAMPLE_DATA=true in .env a complete sample season is added as well
     * (fictional players and staff; see SampleDataSeeder). Keep it false in production.
     * Real squads are loaded with `php artisan football:import`.
     */
    public function run(): void
    {
        $this->call([
            SeasonSeeder::class,
            CompetitionSeeder::class,
            TeamSeeder::class,
            AdminSeeder::class,
        ]);

        if (filter_var(env('SEED_SAMPLE_DATA', false), FILTER_VALIDATE_BOOL)) {
            $this->call(SampleDataSeeder::class);
        }
    }
}
