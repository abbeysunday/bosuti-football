<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Models\Concerns\ResolvesMediaUrls;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use HasSlug, ResolvesMediaUrls, SoftDeletes;

    protected $table = 'staff';

    public const TYPES = [
        'management' => 'Management',
        'coaching' => 'Coaching',
    ];

    protected $fillable = ['name', 'photo', 'role', 'type', 'team_id', 'bio', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected function slugSource(): string
    {
        return $this->name;
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class)->withTrashed();
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function scopeOfType(Builder $query, string $type): void
    {
        $query->where('type', $type);
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('name');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->mediaUrl($this->photo);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }
}
