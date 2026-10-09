<?php

namespace Database\Seeders;

use App\Models\Season;
use Illuminate\Database\Seeder;

class SeasonSeeder extends Seeder
{
    public function run(): void
    {
        // Dates are left empty for the admin to set to the official session dates.
        Season::firstOrCreate(['name' => '2026/2027'], ['is_current' => true, 'is_active' => true]);
    }
}
