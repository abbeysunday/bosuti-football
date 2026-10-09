<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Services\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Stores admin uploads on the public disk under a per-module directory and
 * cleans up the previous file when an image is replaced or removed.
 */
trait ManagesUploads
{
    /**
     * Resolve the stored path for an image field after an update:
     * a new upload replaces the old file, "remove_<field>" clears it, otherwise it is kept.
     */
    protected function handleUpload(Request $request, string $field, string $directory, ?string $current = null): ?string
    {
        if ($request->hasFile($field)) {
            $this->deleteUpload($current);

            return $this->storeUpload($request->file($field), $directory);
        }

        if ($request->boolean("remove_{$field}")) {
            $this->deleteUpload($current);

            return null;
        }

        return $current;
    }

    protected function storeUpload(UploadedFile $file, string $directory): string
    {
        // Resized, re-encoded and saved under a random name (never the client's filename).
        return app(ImageOptimizer::class)->store($file, $directory);
    }

    protected function deleteUpload(?string $path): void
    {
        if ($path && ! preg_match('#^https?://#i', $path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
