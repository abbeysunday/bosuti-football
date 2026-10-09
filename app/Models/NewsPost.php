<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Models\Concerns\ResolvesMediaUrls;
use App\Support\RichText;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class NewsPost extends Model
{
    use HasSlug, ResolvesMediaUrls, SoftDeletes;

    public const CATEGORIES = ['Club News', 'Match Report', 'Team News', 'Competition', 'Announcement'];

    protected $fillable = [
        'title', 'excerpt', 'content', 'featured_image', 'category', 'author_id',
        'fixture_id', 'published_at', 'is_published', 'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // Publishing without a date publishes immediately.
        static::saving(function (NewsPost $post) {
            if ($post->is_published && ! $post->published_at) {
                $post->published_at = now();
            }
        });
    }

    /** Rich-text bodies are sanitised before they are stored. */
    protected function content(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => RichText::isHtml((string) $value) ? RichText::sanitize((string) $value) : $value,
        );
    }

    protected function slugSource(): string
    {
        return $this->title;
    }

    protected function slugSourceColumns(): array
    {
        return ['title'];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function fixture(): BelongsTo
    {
        return $this->belongsTo(Fixture::class);
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true)->where('published_at', '<=', now());
    }

    public function scopeNewest(Builder $query): void
    {
        $query->orderByDesc('published_at')->orderByDesc('id');
    }

    public function isLive(): bool
    {
        return $this->is_published && $this->published_at && $this->published_at->isPast();
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->mediaUrl($this->featured_image);
    }

    public function getSummaryAttribute(): string
    {
        return $this->excerpt ?: Str::limit(RichText::plainText($this->content), 180);
    }

    public function getReadingTimeAttribute(): int
    {
        return max(1, (int) ceil(str_word_count(RichText::plainText($this->content)) / 200));
    }

    /** Safe HTML for display (and for loading into the editor). */
    public function getBodyHtmlAttribute(): string
    {
        return RichText::toHtml($this->content);
    }
}
