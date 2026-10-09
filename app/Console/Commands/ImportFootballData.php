<?php

namespace App\Console\Commands;

use App\Services\FootballImporter;
use Illuminate\Console\Command;

class ImportFootballData extends Command
{
    protected $signature = 'football:import
                            {--path= : Folder with the CSV files (default: database/data/import)}
                            {--dry-run : Check every row and report problems without saving anything}';

    protected $description = 'Import real teams, players, staff, fixtures and match events from CSV files';

    public function handle(FootballImporter $importer): int
    {
        $path = $this->option('path') ?: database_path('data/import');
        $dryRun = (bool) $this->option('dry-run');

        $this->info(($dryRun ? 'Checking' : 'Importing') . " football data from {$path}");

        $result = $importer->import($path, $dryRun);

        if ($result['counts']) {
            $this->table(['Data', 'New', 'Updated'], collect($result['counts'])->map(fn ($c, $type) => [ucfirst($type), $c['created'], $c['updated']])->values());
        } else {
            $this->line('No rows found. Fill in the CSV files first (see database/data/import/README.md).');
        }

        foreach ($result['warnings'] as $warning) {
            $this->warn("  ! {$warning}");
        }

        if ($result['errors']) {
            $this->newLine();
            $this->error(count($result['errors']) . ' problem(s) found. Nothing was saved. Fix these rows and run again:');
            foreach ($result['errors'] as $error) {
                $this->line("  - {$error}");
            }

            return self::FAILURE;
        }

        $this->newLine();
        $dryRun
            ? $this->info('All rows are valid. Run again without --dry-run to import.')
            : $this->info('Import complete. Standings and statistics are calculated automatically.');

        return self::SUCCESS;
    }
}
