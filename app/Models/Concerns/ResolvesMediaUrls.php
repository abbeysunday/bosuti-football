<?php

namespace App\Models\Concerns;

/**
 * Turns a stored media value into a browser URL. Uploaded files live on the
 * public disk (storage/app/public); absolute http(s) URLs are returned as-is.
 */
trait ResolvesMediaUrls
{
    protected function mediaUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        // asset() follows the current host, unlike Storage::url() which depends on APP_URL.
        return asset('storage/' . ltrim($path, '/'));
    }
}
