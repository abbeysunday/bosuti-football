<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Season extends Model
{
    use HasSlug;

    protected $fillable = ['name', 'start_date', 'end_date', 'is_current', 'is_active'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_current' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // Only one season can be current at a time.
        static::saved(function (Season $season) {
            if ($season->is_current) {
                static::whereKeyNot($season->getKey())->where('is_current', true)->update(['is_current' => false]);
            }
        });
    }

    protected function slugSource(): string
    {
        return $this->name;
    }

    public function competitions(): HasMany
    {
        return $this->hasMany(Competition::class);
    }

    public function fixtures(): HasMany
    {
        return $this->hasMany(Fixture::class);
    }

    public function scopeCurrent(Builder $query): void
    {
        $query->where('is_current', true);
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /** The current season, falling back to the most recent one. */
    public static function currentOrLatest(): ?self
    {
        return static::current()->first() ?? static::latest('start_date')->latest('id')->first();
    }
}
