<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Generates a unique `slug` from the model's slug source when it is created,
 * and refreshes it when the source changes. Models define slugSource().
 */
trait HasSlug
{
    abstract protected function slugSource(): string;

    protected static function bootHasSlug(): void
    {
        static::saving(function ($model) {
            if (blank($model->slug) || $model->slugSourceChanged()) {
                $model->slug = $model->uniqueSlug(Str::slug(str_replace('/', ' ', $model->slugSource())) ?: Str::lower(Str::random(8)));
            }
        });
    }

    protected function slugSourceColumns(): array
    {
        return ['name'];
    }

    protected function slugSourceChanged(): bool
    {
        return $this->exists && $this->isDirty($this->slugSourceColumns());
    }

    protected function uniqueSlug(string $base): string
    {
        $slug = $base;
        $i = 2;

        while ($this->slugTaken($slug)) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    protected function slugTaken(string $slug): bool
    {
        $query = static::query()->where('slug', $slug);

        // Soft-deleted rows still own their slug (the unique index covers them).
        if (in_array(SoftDeletes::class, class_uses_recursive(static::class))) {
            $query->withTrashed();
        }

        if ($this->exists) {
            $query->whereKeyNot($this->getKey());
        }

        return $query->exists();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
