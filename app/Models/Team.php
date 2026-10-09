<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Models\Concerns\ResolvesMediaUrls;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Team extends Model
{
    use HasSlug, ResolvesMediaUrls, SoftDeletes;

    protected $fillable = [
        'name', 'short_name', 'logo', 'primary_color', 'secondary_color', 'description',
        'founded_year', 'captain_name', 'coach_name', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'founded_year' => 'integer',
        ];
    }

    protected function slugSource(): string
    {
        return $this->name;
    }

    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }

    public function homeFixtures(): HasMany
    {
        return $this->hasMany(Fixture::class, 'home_team_id');
    }

    public function awayFixtures(): HasMany
    {
        return $this->hasMany(Fixture::class, 'away_team_id');
    }

    public function staff(): HasMany
    {
        return $this->hasMany(Staff::class);
    }

    /** Every fixture this team plays in, home or away. */
    public function fixtures(): Builder
    {
        return Fixture::query()->involving($this->getKey());
    }

    public function hasFixtures(): bool
    {
        return $this->homeFixtures()->exists() || $this->awayFixtures()->exists();
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->mediaUrl($this->logo);
    }

    /** Short label for badges: the short name, or initials of the full name. */
    public function getInitialsAttribute(): string
    {
        if ($this->short_name) {
            return Str::upper($this->short_name);
        }

        $words = preg_split('/\s+/', trim(preg_replace('/\bFC\b/i', '', $this->name))) ?: [];

        return Str::upper(count($words) > 1
            ? collect($words)->map(fn ($w) => mb_substr($w, 0, 1))->take(3)->implode('')
            : mb_substr($words[0] ?? $this->name, 0, 3));
    }
}
