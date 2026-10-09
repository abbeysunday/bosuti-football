<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

/**
 * Loads the real BOUESTI data from database/data/import/*.csv (and photos/).
 *
 *   php artisan db:seed --class=RealDataSeeder
 *
 * Same as `php artisan football:import`; run with --dry-run first to check the files.
 */
class RealDataSeeder extends Seeder
{
    public function run(): void
    {
        $status = Artisan::call('football:import', [], $this->command?->getOutput());

        if ($status !== 0) {
            $this->command?->error('Real data import failed. Nothing was saved; see the problems listed above.');
        }
    }
}
