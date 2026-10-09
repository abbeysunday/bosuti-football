<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Models\Concerns\ResolvesMediaUrls;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Competition extends Model
{
    use HasSlug, ResolvesMediaUrls;

    public const TYPES = [
        'league' => 'League',
        'cup' => 'Cup',
        'friendly' => 'Friendly',
        'tournament' => 'Tournament',
    ];

    protected $fillable = [
        'season_id', 'name', 'short_name', 'type', 'description', 'logo',
        'start_date', 'end_date', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    protected function slugSource(): string
    {
        return trim($this->name . ' ' . ($this->season?->name ?? ''));
    }

    protected function slugSourceColumns(): array
    {
        return ['name', 'season_id'];
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function fixtures(): HasMany
    {
        return $this->hasMany(Fixture::class);
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /** Competitions that produce a standings table. */
    public function scopeWithTable(Builder $query): void
    {
        $query->whereIn('type', ['league', 'tournament']);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->mediaUrl($this->logo);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->short_name ?: $this->name;
    }
}
