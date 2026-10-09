<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Models\Concerns\ResolvesMediaUrls;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasSlug, ResolvesMediaUrls;

    public const CATEGORIES = ['Highlights', 'Match', 'Training', 'Interview', 'Behind the Scenes'];

    protected $fillable = [
        'title', 'thumbnail', 'video_url', 'platform', 'category', 'description', 'published_at', 'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_featured' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Video $video) {
            $video->platform = $video->youtube_id ? 'youtube' : ($video->platform ?: 'other');
        });
    }

    protected function slugSource(): string
    {
        return $this->title;
    }

    protected function slugSourceColumns(): array
    {
        return ['title'];
    }

    public function scopePublished(Builder $query): void
    {
        $query->where(fn (Builder $q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    /** YouTube video id from watch, share, shorts or embed URLs. */
    public function getYoutubeIdAttribute(): ?string
    {
        $pattern = '~(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/)|youtu\.be/)([A-Za-z0-9_-]{11})~';

        return preg_match($pattern, (string) $this->video_url, $m) ? $m[1] : null;
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->mediaUrl($this->thumbnail)
            ?? ($this->youtube_id ? "https://i.ytimg.com/vi/{$this->youtube_id}/hqdefault.jpg" : null);
    }
}
