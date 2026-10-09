<?php

namespace App\Models;

use App\Models\Concerns\ResolvesMediaUrls;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryItem extends Model
{
    use ResolvesMediaUrls;

    public const CATEGORIES = [
        'match' => 'Matches',
        'players' => 'Players',
        'training' => 'Training',
        'fans' => 'Fans',
        'behind-scenes' => 'Behind the Scenes',
    ];

    protected $fillable = [
        'title', 'image', 'category', 'fixture_id', 'team_id', 'description', 'is_featured', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function fixture(): BelongsTo
    {
        return $this->belongsTo(Fixture::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class)->withTrashed();
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderByDesc('created_at')->orderByDesc('id');
    }

    public function scopeFeatured(Builder $query): void
    {
        $query->where('is_featured', true);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->mediaUrl($this->image);
    }

    public function getCategoryLabelAttribute(): ?string
    {
        return $this->category ? (self::CATEGORIES[$this->category] ?? ucfirst($this->category)) : null;
    }

    public function getAltTextAttribute(): string
    {
        return $this->title ?: ($this->fixture ? 'Photo from ' . $this->fixture->title : 'BOUESTI football photo');
    }
}
