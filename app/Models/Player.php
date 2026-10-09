<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Models\Concerns\ResolvesMediaUrls;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Player extends Model
{
    use HasSlug, ResolvesMediaUrls, SoftDeletes;

    public const POSITIONS = [
        'goalkeeper' => 'Goalkeeper',
        'defender' => 'Defender',
        'midfielder' => 'Midfielder',
        'forward' => 'Forward',
    ];

    public const POSITION_SHORT = [
        'goalkeeper' => 'GK',
        'defender' => 'DEF',
        'midfielder' => 'MID',
        'forward' => 'FWD',
    ];

    public const FEET = [
        'right' => 'Right',
        'left' => 'Left',
        'both' => 'Both',
    ];

    public const LEVELS = ['100 Level', '200 Level', '300 Level', '400 Level', '500 Level'];

    protected $fillable = [
        'team_id', 'first_name', 'last_name', 'photo', 'jersey_number', 'position',
        'department', 'level', 'matric_number', 'state_of_origin', 'dominant_foot', 'height',
        'bio', 'is_captain', 'is_featured', 'is_active',
    ];

    /** Student data that must never reach public JSON/serialisation. */
    protected $hidden = ['matric_number'];

    protected function casts(): array
    {
        return [
            'jersey_number' => 'integer',
            'is_captain' => 'boolean',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected function slugSource(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    protected function slugSourceColumns(): array
    {
        return ['first_name', 'last_name'];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class)->withTrashed();
    }

    public function matchEvents(): HasMany
    {
        return $this->hasMany(MatchEvent::class);
    }

    public function lineups(): HasMany
    {
        return $this->hasMany(FixturePlayer::class);
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): void
    {
        $query->where('is_featured', true);
    }

    public function scopePosition(Builder $query, ?string $position): void
    {
        if ($position && array_key_exists($position, self::POSITIONS)) {
            $query->where('position', $position);
        }
    }

    /** Squad ordering: goalkeepers → forwards, then shirt number. */
    public function scopeSquadOrder(Builder $query): void
    {
        $query->orderByRaw("CASE position WHEN 'goalkeeper' THEN 1 WHEN 'defender' THEN 2 WHEN 'midfielder' THEN 3 ELSE 4 END")
            ->orderByRaw('jersey_number IS NULL')
            ->orderBy('jersey_number')
            ->orderBy('last_name');
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->mediaUrl($this->photo);
    }

    public function getPositionLabelAttribute(): string
    {
        return self::POSITIONS[$this->position] ?? ucfirst((string) $this->position);
    }

    public function getPositionShortAttribute(): string
    {
        return self::POSITION_SHORT[$this->position] ?? '—';
    }

    public function getShirtAttribute(): string
    {
        return $this->jersey_number !== null ? str_pad((string) $this->jersey_number, 2, '0', STR_PAD_LEFT) : '—';
    }
}
