<?php

namespace App\Services;

use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Resizes and re-encodes uploaded photos with PHP's GD extension (no extra dependency):
 *  - scales down to a maximum box per upload type (never enlarges),
 *  - applies the camera's EXIF rotation so phone photos stand upright,
 *  - saves as WebP (small files, keeps logo transparency),
 *  - drops all metadata, including GPS location, by re-encoding.
 * Anything GD cannot process is stored unchanged.
 */
class ImageOptimizer
{
    /** Maximum [width, height] per storage directory. */
    public const PROFILES = [
        'teams/logos' => [512, 512],
        'competitions' => [512, 512],
        'players' => [900, 1200],
        'staff' => [900, 1200],
        'news' => [1920, 1280],
        'gallery' => [2000, 2000],
        'videos' => [1280, 720],
    ];

    private const DEFAULT_BOX = [1600, 1600];
    private const QUALITY = 82;
    private const MAX_PIXELS = 50_000_000; // guard against decompression bombs

    public function store(UploadedFile $file, string $directory): string
    {
        $image = $this->load($file);

        if (! $image) {
            return $file->store($directory, 'public');
        }

        [$maxWidth, $maxHeight] = self::PROFILES[$directory] ?? self::DEFAULT_BOX;
        $image = $this->resize($image, $maxWidth, $maxHeight);

        ob_start();
        imagewebp($image, null, self::QUALITY);
        $data = ob_get_clean();
        imagedestroy($image);

        if ($data === false || $data === '') {
            return $file->store($directory, 'public');
        }

        $path = trim($directory, '/') . '/' . Str::random(40) . '.webp';
        Storage::disk('public')->put($path, $data);

        return $path;
    }

    private function load(UploadedFile $file): ?GdImage
    {
        $info = @getimagesize($file->getRealPath());

        if (! $info || $info[0] * $info[1] > self::MAX_PIXELS) {
            return null;
        }

        $image = match ($info[2]) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($file->getRealPath()),
            IMAGETYPE_PNG => @imagecreatefrompng($file->getRealPath()),
            IMAGETYPE_WEBP => @imagecreatefromwebp($file->getRealPath()),
            default => false,
        };

        if (! $image) {
            return null;
        }

        if (! imageistruecolor($image)) {
            imagepalettetotruecolor($image);
        }
        imagealphablending($image, false);
        imagesavealpha($image, true);

        return $info[2] === IMAGETYPE_JPEG ? $this->orient($image, $file->getRealPath()) : $image;
    }

    /** Rotate/flip according to the EXIF Orientation tag written by phone cameras. */
    private function orient(GdImage $image, string $path): GdImage
    {
        $orientation = function_exists('exif_read_data') ? (@exif_read_data($path)['Orientation'] ?? 1) : 1;

        $rotated = match ((int) $orientation) {
            2 => $this->flip($image, IMG_FLIP_HORIZONTAL),
            3 => imagerotate($image, 180, 0),
            4 => $this->flip($image, IMG_FLIP_VERTICAL),
            5 => imagerotate($this->flip($image, IMG_FLIP_HORIZONTAL), 90, 0),
            6 => imagerotate($image, -90, 0),
            7 => imagerotate($this->flip($image, IMG_FLIP_HORIZONTAL), -90, 0),
            8 => imagerotate($image, 90, 0),
            default => $image,
        };

        return $rotated ?: $image;
    }

    private function flip(GdImage $image, int $mode): GdImage
    {
        imageflip($image, $mode);

        return $image;
    }

    private function resize(GdImage $image, int $maxWidth, int $maxHeight): GdImage
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $scale = min($maxWidth / $width, $maxHeight / $height, 1);

        if ($scale >= 1) {
            return $image;
        }

        $newWidth = max(1, (int) round($width * $scale));
        $newHeight = max(1, (int) round($height * $scale));

        $resized = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagefill($resized, 0, 0, imagecolorallocatealpha($resized, 0, 0, 0, 127));
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($image);

        return $resized;
    }
}
