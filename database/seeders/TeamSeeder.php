<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /** Internal student football clubs at BOUESTI, Ikere-Ekiti. Slugs are generated automatically. */
    public const TEAMS = [
        'Amapro FC',
        'Elite FC',
        'Young Boys FC',
        'CSC Elites',
        'Engines Boys',
        'Sovereignty FC',
        'AMCOMS',
    ];

    public function run(): void
    {
        foreach (self::TEAMS as $name) {
            Team::withTrashed()->firstOrCreate(['name' => $name], ['is_active' => true]);
        }
    }
}
