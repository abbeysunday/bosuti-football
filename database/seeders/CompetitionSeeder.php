<?php

namespace Database\Seeders;

use App\Models\Competition;
use App\Models\Season;
use Illuminate\Database\Seeder;

class CompetitionSeeder extends Seeder
{
    public function run(): void
    {
        $season = Season::currentOrLatest();

        if (! $season) {
            return;
        }

        // Generic default so the league table works out of the box. Rename it in the admin
        // panel to the official competition name.
        Competition::firstOrCreate(
            ['season_id' => $season->id, 'name' => 'BOUESTI Football League'],
            [
                'short_name' => 'BFL',
                'type' => 'league',
                'description' => 'Default internal league for BOUESTI student football teams. Rename or edit in the admin panel.',
                'is_active' => true,
            ],
        );
    }
}
