<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Fixture extends Model
{
    public const STATUSES = [
        'scheduled' => 'Scheduled',
        'live' => 'Live',
        'completed' => 'Full time',
        'postponed' => 'Postponed',
        'cancelled' => 'Cancelled',
    ];

    /** Statuses for which a score is meaningful. */
    public const SCORED_STATUSES = ['live', 'completed'];

    /** Statuses that no longer occupy a team's match day. */
    public const INACTIVE_STATUSES = ['postponed', 'cancelled'];

    protected $fillable = [
        'competition_id', 'season_id', 'home_team_id', 'away_team_id', 'match_date', 'kickoff_time',
        'venue', 'status', 'home_score', 'away_score', 'matchday', 'referee', 'attendance',
        'featured', 'report',
    ];

    protected function casts(): array
    {
        return [
            'match_date' => 'date',
            'home_score' => 'integer',
            'away_score' => 'integer',
            'matchday' => 'integer',
            'attendance' => 'integer',
            'featured' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // Scores only belong to live/completed matches, and season always follows the competition.
        static::saving(function (Fixture $fixture) {
            if (! in_array($fixture->status, self::SCORED_STATUSES, true)) {
                $fixture->home_score = null;
                $fixture->away_score = null;
            }

            if ($fixture->competition_id && $fixture->isDirty('competition_id')) {
                $fixture->season_id = Competition::whereKey($fixture->competition_id)->value('season_id');
            }
        });
    }

    /* Relationships -------------------------------------------------------- */

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id')->withTrashed();
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id')->withTrashed();
    }

    public function matchEvents(): HasMany
    {
        return $this->hasMany(MatchEvent::class)->orderBy('minute')->orderByRaw('COALESCE(additional_minute, 0)')->orderBy('id');
    }

    public function lineups(): HasMany
    {
        return $this->hasMany(FixturePlayer::class);
    }

    public function report(): HasOne
    {
        return $this->hasOne(NewsPost::class)->published()->latest('published_at');
    }

    /* Scopes -------------------------------------------------------------- */

    public function scopeUpcoming(Builder $query): void
    {
        $query->whereIn('status', ['scheduled', 'live'])
            ->whereDate('match_date', '>=', today())
            ->orderBy('match_date')
            ->orderByRaw('kickoff_time IS NULL')
            ->orderBy('kickoff_time');
    }

    public function scopeCompleted(Builder $query): void
    {
        $query->where('status', 'completed');
    }

    public function scopeLatestFirst(Builder $query): void
    {
        $query->orderByDesc('match_date')->orderByDesc('kickoff_time')->orderByDesc('id');
    }

    public function scopeInvolving(Builder $query, int|string|null $teamId): void
    {
        if ($teamId) {
            $query->where(fn (Builder $q) => $q->where('home_team_id', $teamId)->orWhere('away_team_id', $teamId));
        }
    }

    public function scopeWithTeams(Builder $query): void
    {
        $query->with(['homeTeam', 'awayTeam', 'competition']);
    }

    /* Accessors & helpers ------------------------------------------------- */

    public function getKickoffAtAttribute(): ?Carbon
    {
        if (! $this->match_date) {
            return null;
        }

        return $this->kickoff_time
            ? Carbon::parse($this->match_date->format('Y-m-d') . ' ' . $this->kickoff_time)
            : $this->match_date->copy()->startOfDay();
    }

    public function getKickoffLabelAttribute(): ?string
    {
        return $this->kickoff_time ? Carbon::parse($this->kickoff_time)->format('g:i A') : null;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function hasScore(): bool
    {
        return in_array($this->status, self::SCORED_STATUSES, true)
            && $this->home_score !== null && $this->away_score !== null;
    }

    public function getScorelineAttribute(): string
    {
        return $this->hasScore() ? "{$this->home_score}–{$this->away_score}" : 'VS';
    }

    public function getTitleAttribute(): string
    {
        return ($this->homeTeam?->name ?? 'TBC') . ' vs ' . ($this->awayTeam?->name ?? 'TBC');
    }

    public function involves(int $teamId): bool
    {
        return (int) $this->home_team_id === $teamId || (int) $this->away_team_id === $teamId;
    }

    /** 'W', 'D' or 'L' from a team's point of view (completed matches only). */
    public function resultFor(int $teamId): ?string
    {
        if (! $this->isCompleted() || ! $this->hasScore() || ! $this->involves($teamId)) {
            return null;
        }

        [$for, $against] = $teamId === (int) $this->home_team_id
            ? [$this->home_score, $this->away_score]
            : [$this->away_score, $this->home_score];

        return $for > $against ? 'W' : ($for === $against ? 'D' : 'L');
    }
}
