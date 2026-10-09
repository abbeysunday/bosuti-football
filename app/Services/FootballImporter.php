<?php

namespace App\Services;

use App\Models\Competition;
use App\Models\Fixture;
use App\Models\MatchEvent;
use App\Models\Player;
use App\Models\Season;
use App\Models\Staff;
use App\Models\Team;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Imports the real BOUESTI football data from CSV files (see database/data/import/README.md).
 *
 * Files (all optional, processed in this order): teams.csv, players.csv, staff.csv,
 * fixtures.csv, match_events.csv. Photos are read from photos/{teams,players,staff}/.
 *
 * Re-running is safe: rows are matched on natural keys and updated, never duplicated.
 * Everything runs in one transaction: if any row is invalid, nothing is saved.
 */
class FootballImporter
{
    /** @var array<int, string> */
    private array $errors = [];

    /** @var array<int, string> */
    private array $warnings = [];

    /** @var array<string, array{created:int, updated:int}> */
    private array $counts = [];

    private string $path;

    private bool $dryRun = false;

    /** @var array<string, Team|null> */
    private array $teamCache = [];

    /** @var array<int, string> photos written this run, removed again if the import is rolled back */
    private array $storedPhotos = [];

    public function __construct(private ImageOptimizer $images)
    {
    }

    /**
     * @return array{errors: array<int,string>, warnings: array<int,string>, counts: array<string,array{created:int,updated:int}>}
     */
    public function import(string $path, bool $dryRun = false): array
    {
        $this->path = rtrim($path, '/\\');
        $this->errors = $this->warnings = $this->counts = $this->teamCache = $this->storedPhotos = [];
        $this->dryRun = $dryRun;

        if (! is_dir($this->path)) {
            throw new RuntimeException("Import folder not found: {$this->path}");
        }

        DB::beginTransaction();

        try {
            $this->importTeams();
            $this->importPlayers();
            $this->importStaff();
            $this->importFixtures();
            $this->importEvents();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->discardPhotos();
            throw $e;
        }

        if ($dryRun || $this->errors) {
            DB::rollBack();
            $this->discardPhotos();
        } else {
            DB::commit();
        }

        return ['errors' => $this->errors, 'warnings' => $this->warnings, 'counts' => $this->counts];
    }

    /* Teams ------------------------------------------------------------ */

    private function importTeams(): void
    {
        foreach ($this->rows('teams.csv') as [$line, $row]) {
            if (! $this->require($row, ['name'], 'teams.csv', $line)) {
                continue;
            }

            $team = Team::withTrashed()->whereRaw('LOWER(name) = ?', [Str::lower($row['name'])])->first() ?? new Team();
            $wasNew = ! $team->exists;

            $team->fill(array_filter([
                'name' => $row['name'],
                'short_name' => $row['short_name'] ?? null,
                'primary_color' => $this->colour($row['primary_color'] ?? null, 'teams.csv', $line),
                'secondary_color' => $this->colour($row['secondary_color'] ?? null, 'teams.csv', $line),
                'description' => $row['description'] ?? null,
                'founded_year' => $this->int($row['founded_year'] ?? null),
                'captain_name' => $row['captain_name'] ?? null,
                'coach_name' => $row['coach_name'] ?? null,
            ], fn ($v) => $v !== null && $v !== ''));
            $team->is_active = $this->bool($row['is_active'] ?? null, true);

            if ($photo = $this->photo('teams', $row['logo'] ?? null, 'teams.csv', $line)) {
                $team->logo = $photo;
            }

            $team->save();
            if ($team->trashed()) {
                $team->restore();
            }
            $this->count('teams', $wasNew);
        }
    }

    /* Players ---------------------------------------------------------- */

    private function importPlayers(): void
    {
        $shirts = []; // team_id => [number => "file:line"] to catch duplicates within the file

        foreach ($this->rows('players.csv') as [$line, $row]) {
            if (! $this->require($row, ['team', 'first_name', 'last_name', 'position'], 'players.csv', $line)) {
                continue;
            }

            $team = $this->team($row['team'], 'players.csv', $line);
            $position = $this->position($row['position'], 'players.csv', $line);
            if (! $team || ! $position) {
                continue;
            }

            $foot = isset($row['dominant_foot']) && $row['dominant_foot'] !== '' ? Str::lower($row['dominant_foot']) : null;
            if ($foot && ! array_key_exists($foot, Player::FEET)) {
                $this->error('players.csv', $line, "dominant_foot must be right, left or both (got \"{$row['dominant_foot']}\").");
                continue;
            }

            $number = $this->int($row['jersey_number'] ?? null);
            if ($number !== null && ($number < 1 || $number > 99)) {
                $this->error('players.csv', $line, 'jersey_number must be between 1 and 99.');
                continue;
            }

            // Match on matric number when given, otherwise on team + full name.
            $player = null;
            if (! empty($row['matric_number'])) {
                $player = Player::withTrashed()->where('matric_number', $row['matric_number'])->first();
            }
            $player ??= Player::withTrashed()->where('team_id', $team->id)
                ->whereRaw('LOWER(first_name) = ? AND LOWER(last_name) = ?', [Str::lower($row['first_name']), Str::lower($row['last_name'])])
                ->first();
            $wasNew = ! $player;
            $player ??= new Player();

            if ($number !== null) {
                $clash = Player::where('team_id', $team->id)->where('jersey_number', $number)
                    ->when($player->exists, fn ($q) => $q->whereKeyNot($player->id))->first();
                if (isset($shirts[$team->id][$number]) || $clash) {
                    $other = $shirts[$team->id][$number] ?? $clash->full_name;
                    $this->error('players.csv', $line, "{$team->name} shirt #{$number} is already used by {$other}.");
                    continue;
                }
                $shirts[$team->id][$number] = "{$row['first_name']} {$row['last_name']} (line {$line})";
            }

            $player->fill([
                'team_id' => $team->id,
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'jersey_number' => $number,
                'position' => $position,
                'department' => $row['department'] ?? null ?: null,
                'level' => $this->level($row['level'] ?? null),
                'matric_number' => $row['matric_number'] ?? null ?: null,
                'state_of_origin' => $row['state_of_origin'] ?? null ?: null,
                'dominant_foot' => $foot,
                'height' => $row['height'] ?? null ?: null,
                'bio' => $row['bio'] ?? null ?: null,
                'is_captain' => $this->bool($row['is_captain'] ?? null, false),
                'is_featured' => $this->bool($row['is_featured'] ?? null, false),
                'is_active' => $this->bool($row['is_active'] ?? null, true),
            ]);

            if ($photo = $this->photo('players', $row['photo'] ?? null, 'players.csv', $line)) {
                $player->photo = $photo;
            }

            $player->save();
            if ($player->trashed()) {
                $player->restore();
            }
            $this->count('players', $wasNew);
        }
    }

    /* Staff ------------------------------------------------------------ */

    private function importStaff(): void
    {
        foreach ($this->rows('staff.csv') as [$line, $row]) {
            if (! $this->require($row, ['name', 'role', 'type'], 'staff.csv', $line)) {
                continue;
            }

            $type = Str::lower($row['type']);
            if (! array_key_exists($type, Staff::TYPES)) {
                $this->error('staff.csv', $line, "type must be management or coaching (got \"{$row['type']}\").");
                continue;
            }

            $team = null;
            if (! empty($row['team']) && ! ($team = $this->team($row['team'], 'staff.csv', $line))) {
                continue;
            }

            $member = Staff::withTrashed()->whereRaw('LOWER(name) = ?', [Str::lower($row['name'])])->first();
            $wasNew = ! $member;
            $member ??= new Staff();

            $member->fill([
                'name' => $row['name'],
                'role' => $row['role'],
                'type' => $type,
                'team_id' => $team?->id,
                'bio' => $row['bio'] ?? null ?: null,
                'sort_order' => $this->int($row['sort_order'] ?? null) ?? 0,
                'is_active' => $this->bool($row['is_active'] ?? null, true),
            ]);

            if ($photo = $this->photo('staff', $row['photo'] ?? null, 'staff.csv', $line)) {
                $member->photo = $photo;
            }

            $member->save();
            if ($member->trashed()) {
                $member->restore();
            }
            $this->count('staff', $wasNew);
        }
    }

    /* Fixtures & results ------------------------------------------------ */

    private function importFixtures(): void
    {
        foreach ($this->rows('fixtures.csv') as [$line, $row]) {
            if (! $this->require($row, ['date', 'home_team', 'away_team'], 'fixtures.csv', $line)) {
                continue;
            }

            $home = $this->team($row['home_team'], 'fixtures.csv', $line);
            $away = $this->team($row['away_team'], 'fixtures.csv', $line);
            $date = $this->date($row['date'], 'fixtures.csv', $line);
            $competition = $this->competition($row['competition'] ?? null, 'fixtures.csv', $line);
            if (! $home || ! $away || ! $date || ! $competition) {
                continue;
            }
            if ($home->id === $away->id) {
                $this->error('fixtures.csv', $line, "{$home->name} cannot play against itself.");
                continue;
            }

            $homeScore = $this->int($row['home_score'] ?? null);
            $awayScore = $this->int($row['away_score'] ?? null);
            $status = Str::lower($row['status'] ?? '') ?: ($homeScore !== null && $awayScore !== null ? 'completed' : 'scheduled');
            if (! array_key_exists($status, Fixture::STATUSES)) {
                $this->error('fixtures.csv', $line, 'status must be one of: ' . implode(', ', array_keys(Fixture::STATUSES)) . '.');
                continue;
            }
            if (in_array($status, Fixture::SCORED_STATUSES, true) && ($homeScore === null || $awayScore === null)) {
                $this->error('fixtures.csv', $line, "A {$status} match needs both home_score and away_score.");
                continue;
            }

            $time = $this->time($row['time'] ?? null, 'fixtures.csv', $line);
            if ($time === false) {
                continue;
            }

            $fixture = Fixture::where('competition_id', $competition->id)
                ->whereDate('match_date', $date)
                ->where('home_team_id', $home->id)->where('away_team_id', $away->id)
                ->first();
            $wasNew = ! $fixture;
            $fixture ??= new Fixture();

            $fixture->fill([
                'competition_id' => $competition->id,
                'home_team_id' => $home->id,
                'away_team_id' => $away->id,
                'match_date' => $date,
                'kickoff_time' => $time,
                'venue' => $row['venue'] ?? null ?: null,
                'matchday' => $this->int($row['matchday'] ?? null),
                'status' => $status,
                'home_score' => $homeScore,
                'away_score' => $awayScore,
                'referee' => $row['referee'] ?? null ?: null,
                'attendance' => $this->int($row['attendance'] ?? null),
                'featured' => $this->bool($row['featured'] ?? null, false),
                'report' => $row['summary'] ?? null ?: null,
            ])->save();

            $this->count('fixtures', $wasNew);
        }
    }

    /* Match events ------------------------------------------------------ */

    private function importEvents(): void
    {
        $rows = collect($this->rows('match_events.csv'));
        $replaced = []; // fixtures whose old events have been cleared this run

        foreach ($rows as [$line, $row]) {
            if (! $this->require($row, ['date', 'home_team', 'away_team', 'minute', 'type', 'team', 'player'], 'match_events.csv', $line)) {
                continue;
            }

            $home = $this->team($row['home_team'], 'match_events.csv', $line);
            $away = $this->team($row['away_team'], 'match_events.csv', $line);
            $date = $this->date($row['date'], 'match_events.csv', $line);
            $team = $this->team($row['team'], 'match_events.csv', $line);
            if (! $home || ! $away || ! $date || ! $team) {
                continue;
            }

            $fixture = Fixture::whereDate('match_date', $date)->where('home_team_id', $home->id)->where('away_team_id', $away->id)->first();
            if (! $fixture) {
                $this->error('match_events.csv', $line, "No fixture {$home->name} vs {$away->name} on {$date} (add it to fixtures.csv first).");
                continue;
            }
            if (! $fixture->involves($team->id)) {
                $this->error('match_events.csv', $line, "{$team->name} is not playing in {$home->name} vs {$away->name}.");
                continue;
            }

            $type = Str::of($row['type'])->lower()->replace([' ', '-'], '_')->toString();
            if (! array_key_exists($type, MatchEvent::TYPES)) {
                $this->error('match_events.csv', $line, 'type must be one of: ' . implode(', ', array_keys(MatchEvent::TYPES)) . '.');
                continue;
            }

            $player = $this->player($row['player'], $team, 'match_events.csv', $line);
            $related = ! empty($row['related_player']) ? $this->player($row['related_player'], $team, 'match_events.csv', $line) : null;
            if (! $player || (! empty($row['related_player']) && ! $related)) {
                continue;
            }

            $minute = $this->int($row['minute']);
            if ($minute === null || $minute < 0 || $minute > 130) {
                $this->error('match_events.csv', $line, 'minute must be a number between 0 and 130.');
                continue;
            }

            // The file is the source of truth for each match it mentions.
            if (! isset($replaced[$fixture->id])) {
                $fixture->matchEvents()->delete();
                $replaced[$fixture->id] = true;
            }

            $fixture->matchEvents()->create([
                'team_id' => $team->id,
                'player_id' => $player->id,
                'related_player_id' => $related?->id,
                'type' => $type,
                'minute' => $minute,
                'additional_minute' => $this->int($row['added_time'] ?? null),
                'description' => $row['note'] ?? null ?: null,
            ]);
            $this->count('match events', true);
        }
    }

    /* Lookups ---------------------------------------------------------- */

    private function team(string $name, string $file, int $line): ?Team
    {
        $key = Str::lower(trim($name));

        $team = $this->teamCache[$key] ??= Team::whereRaw('LOWER(name) = ? OR LOWER(short_name) = ? OR slug = ?', [$key, $key, Str::slug($name)])->first();

        if (! $team) {
            unset($this->teamCache[$key]);
            $this->error($file, $line, "Unknown team \"{$name}\". Use one of: " . Team::orderBy('name')->pluck('name')->implode(', ') . '.');
        }

        return $team;
    }

    /** A player in the given team, by full name ("Tunde Adeyemi") or shirt number ("#9"). */
    private function player(string $value, Team $team, string $file, int $line): ?Player
    {
        $value = trim($value);
        $query = Player::where('team_id', $team->id);

        $player = preg_match('/^#?(\d{1,2})$/', $value, $m)
            ? (clone $query)->where('jersey_number', (int) $m[1])->first()
            : (clone $query)->get()->first(fn (Player $p) => Str::lower($p->full_name) === Str::lower($value));

        if (! $player) {
            $this->error($file, $line, "No {$team->name} player \"{$value}\" (use the full name or #shirt number from players.csv).");
        }

        return $player;
    }

    private function competition(?string $name, string $file, int $line): ?Competition
    {
        if (blank($name)) {
            $season = Season::currentOrLatest();
            $competition = $season ? Competition::where('season_id', $season->id)->where('type', 'league')->first() : null;
            $competition ??= Competition::latest('id')->first();

            if (! $competition) {
                $this->error($file, $line, 'No competition exists yet. Create one in the admin panel or name it in the "competition" column.');
            }

            return $competition;
        }

        $competition = Competition::whereRaw('LOWER(name) = ? OR LOWER(short_name) = ?', [Str::lower($name), Str::lower($name)])
            ->orderByDesc('season_id')->first();

        if (! $competition) {
            $this->error($file, $line, "Unknown competition \"{$name}\". Create it in the admin panel first.");
        }

        return $competition;
    }

    private function position(string $value, string $file, int $line): ?string
    {
        $aliases = ['gk' => 'goalkeeper', 'keeper' => 'goalkeeper', 'def' => 'defender', 'mid' => 'midfielder', 'fwd' => 'forward', 'striker' => 'forward', 'winger' => 'forward'];
        $key = Str::lower(trim($value));
        $key = $aliases[$key] ?? rtrim($key, 's');

        if (! array_key_exists($key, Player::POSITIONS)) {
            $this->error($file, $line, "position must be goalkeeper, defender, midfielder or forward (got \"{$value}\").");

            return null;
        }

        return $key;
    }

    /* Photos ----------------------------------------------------------- */

    private function photo(string $folder, ?string $filename, string $file, int $line): ?string
    {
        if (blank($filename)) {
            return null;
        }

        $source = "{$this->path}/photos/{$folder}/" . basename($filename);
        if (! is_file($source)) {
            $this->warning($file, $line, "Photo not found: photos/{$folder}/" . basename($filename) . ' (row imported without it).');

            return null;
        }

        if ($this->dryRun) {
            return null; // checked, but nothing is written during a dry run
        }

        $directory = ['teams' => 'teams/logos', 'players' => 'players', 'staff' => 'staff'][$folder];

        return $this->storedPhotos[] = $this->images->store(new UploadedFile($source, basename($source), null, null, true), $directory);
    }

    private function discardPhotos(): void
    {
        Storage::disk('public')->delete($this->storedPhotos);
        $this->storedPhotos = [];
    }

    /* CSV parsing & value helpers ------------------------------------------ */

    /** @return array<int, array{0:int, 1:array<string,string>}> [line number, row] pairs, blank rows skipped */
    private function rows(string $file): array
    {
        $full = "{$this->path}/{$file}";
        if (! is_file($full)) {
            return [];
        }

        $handle = fopen($full, 'r');
        $header = null;
        $rows = [];
        $line = 0;

        while (($data = fgetcsv($handle, 0, ',', '"', '')) !== false) {
            $line++;
            if ($header === null) {
                $data[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $data[0]); // Excel's UTF-8 BOM
                $header = array_map(fn ($h) => Str::snake(Str::lower(trim((string) $h))), $data);
                continue;
            }

            $values = array_map(fn ($v) => trim((string) $v), $data);
            if (implode('', $values) === '') {
                continue;
            }

            $values = array_pad(array_slice($values, 0, count($header)), count($header), '');
            $rows[] = [$line, array_map(fn ($v) => $this->utf8($v), array_combine($header, $values))];
        }

        fclose($handle);

        return $rows;
    }

    private function utf8(string $value): string
    {
        return mb_check_encoding($value, 'UTF-8') ? $value : mb_convert_encoding($value, 'UTF-8', 'Windows-1252');
    }

    private function require(array $row, array $columns, string $file, int $line): bool
    {
        $missing = array_filter($columns, fn ($c) => ($row[$c] ?? '') === '');
        if ($missing) {
            $this->error($file, $line, 'Missing required value(s): ' . implode(', ', $missing) . '.');

            return false;
        }

        return true;
    }

    private function date(string $value, string $file, int $line): ?string
    {
        foreach (['Y-m-d', 'd/m/Y', 'd-m-Y', 'd.m.Y', 'j/n/Y'] as $format) {
            $date = \DateTime::createFromFormat("!{$format}", trim($value));
            if ($date && $date->format($format) === trim($value)) {
                return $date->format('Y-m-d');
            }
        }

        $this->error($file, $line, "Unrecognised date \"{$value}\". Use YYYY-MM-DD (e.g. 2026-10-14) or DD/MM/YYYY.");

        return null;
    }

    /** @return string|null|false  H:i, null when empty, false when invalid */
    private function time(?string $value, string $file, int $line): string|null|false
    {
        if (blank($value)) {
            return null;
        }

        foreach (['H:i', 'G:i', 'g:i A', 'g:iA', 'h:i A', 'H:i:s'] as $format) {
            $time = \DateTime::createFromFormat("!{$format}", strtoupper(trim($value)));
            if ($time && $time->format($format) === strtoupper(trim($value))) {
                return $time->format('H:i');
            }
        }

        $this->error($file, $line, "Unrecognised time \"{$value}\". Use 24-hour HH:MM (e.g. 16:00) or 4:00 PM.");

        return false;
    }

    private function level(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        return preg_match('/^(\d{3})/', trim($value), $m) ? "{$m[1]} Level" : trim($value);
    }

    private function colour(?string $value, string $file, int $line): ?string
    {
        if (blank($value)) {
            return null;
        }

        $value = '#' . ltrim(trim($value), '#');
        if (! preg_match('/^#[0-9A-Fa-f]{6}$/', $value)) {
            $this->warning($file, $line, "Colour \"{$value}\" ignored; use a hex colour like #009A56.");

            return null;
        }

        return Str::lower($value);
    }

    private function int(?string $value): ?int
    {
        return is_numeric($value ?? '') ? (int) $value : null;
    }

    private function bool(?string $value, bool $default): bool
    {
        if (blank($value)) {
            return $default;
        }

        return in_array(Str::lower(trim($value)), ['1', 'yes', 'y', 'true', 'x'], true);
    }

    private function error(string $file, int $line, string $message): void
    {
        $this->errors[] = "{$file} line {$line}: {$message}";
    }

    private function warning(string $file, int $line, string $message): void
    {
        $this->warnings[] = "{$file} line {$line}: {$message}";
    }

    private function count(string $type, bool $created): void
    {
        $this->counts[$type] ??= ['created' => 0, 'updated' => 0];
        $this->counts[$type][$created ? 'created' : 'updated']++;
    }
}
