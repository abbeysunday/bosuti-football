<?php

namespace App\Services;

use GdImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Draws branded graphics with GD for sample data: team crests, player cards
 * (a shirt in the team colours with the squad number) and staff portraits.
 * These are illustrations, never photographs of people.
 * Font: Barlow Condensed (SIL Open Font License, resources/fonts/OFL.txt).
 */
class SampleGraphics
{
    private string $bold;
    private string $semi;

    public function __construct()
    {
        $this->bold = resource_path('fonts/BarlowCondensed-ExtraBold.ttf');
        $this->semi = resource_path('fonts/BarlowCondensed-SemiBold.ttf');
    }

    /** Shield crest with the team initials. Returns the public-disk path. */
    public function crest(string $initials, string $primary, string $secondary): string
    {
        $size = 512;
        $img = $this->canvas($size, $size, transparent: true);

        $shield = fn (int $inset) => [
            60 + $inset, 40 + $inset, 452 - $inset, 40 + $inset,
            452 - $inset, 250, 430 - $inset, 330, 370 - $inset * 0.6, 410 - $inset * 0.3,
            256, 480 - $inset, 142 + $inset * 0.6, 410 - $inset * 0.3, 82 + $inset, 330, 60 + $inset, 250,
        ];

        imagefilledpolygon($img, array_map('intval', $shield(0)), $this->color($img, $secondary));
        imagefilledpolygon($img, array_map('intval', $shield(16)), $this->color($img, $primary));

        // Diagonal sash in the secondary colour, clipped to the inner shield.
        $sash = $this->canvas($size, $size, transparent: true);
        imagefilledpolygon($sash, [60, 336, 452, 262, 452, 306, 60, 380], $this->color($sash, $secondary, 64));
        $this->maskTo($sash, $shield(16));
        imagecopy($img, $sash, 0, 0, 0, 0, $size, $size);
        imagedestroy($sash);

        $text = $this->contrast($primary);
        $this->centeredText($img, Str::upper($initials), $this->bold, strlen($initials) > 3 ? 110 : 150, 256, 250, $text);
        $this->centeredText($img, 'BOUESTI', $this->semi, 34, 256, 380, $this->color($img, $secondary));

        return $this->save($img, 'teams/logos');
    }

    /** Player card: a shirt in team colours with the squad number on a dark gradient. */
    public function playerCard(?int $number, string $primary, string $secondary, string $initials): string
    {
        // The card is identical for a whole team apart from the number, so draw it once per team.
        $key = "{$primary}|{$secondary}|{$initials}";
        $this->cardCache[$key] ??= $this->cardBase($primary, $secondary, $initials);

        $img = $this->canvas(900, 1200);
        imagecopy($img, $this->cardCache[$key], 0, 0, 0, 0, 900, 1200);

        $numberColour = $this->distance($primary, $secondary) > 120 ? $this->color($img, $secondary) : $this->contrast($primary);
        $this->centeredText($img, (string) ($number ?? ''), $this->bold, 230, 450, 700, $numberColour);

        return $this->save($img, 'players');
    }

    /** @var array<string, GdImage> */
    private array $cardCache = [];

    private function cardBase(string $primary, string $secondary, string $initials): GdImage
    {
        [$w, $h] = [900, 1200];
        $img = $this->canvas($w, $h);
        $this->gradient($img, $this->mix($primary, '#04090a', .55), '#050b08');
        $this->glow($img, 450, 520, 760, $primary, 92);

        // Team initials watermark.
        $this->centeredText($img, Str::upper($initials), $this->bold, 300, 450, 1130, $this->color($img, '#ffffff', 122));

        // Shirt.
        $shirt = [300, 330, 170, 450, 232, 548, 312, 478, 322, 900, 578, 900, 588, 478, 668, 548, 730, 450, 600, 330, 520, 318, 450, 372, 380, 318];
        imagefilledpolygon($img, $shirt, $this->color($img, $primary));
        // Shading on the left half for depth.
        $shade = $this->canvas($w, $h, transparent: true);
        imagefilledpolygon($shade, [300, 330, 170, 450, 232, 548, 312, 478, 322, 900, 450, 900, 450, 372, 380, 318], $this->color($shade, '#000000', 105));
        imagecopy($img, $shade, 0, 0, 0, 0, $w, $h);
        imagedestroy($shade);

        $trim = $this->color($img, $secondary);
        imagesetthickness($img, 14);
        imageline($img, 380, 318, 450, 372, $trim);
        imageline($img, 450, 372, 520, 318, $trim);
        imageline($img, 176, 458, 238, 540, $trim);
        imageline($img, 724, 458, 662, 540, $trim);
        imagesetthickness($img, 1);

        return $img;
    }

    /** Staff card: a silhouette portrait in club colours with the person's initials. */
    public function staffPortrait(string $initials, string $primary = '#009a56', string $accent = '#f2cf70'): string
    {
        [$w, $h] = [1000, 850];
        $img = $this->canvas($w, $h);
        $this->gradient($img, $this->mix($primary, '#04090a', .45), '#060f0a');
        $this->glow($img, 500, 360, 700, $accent, 110);

        $figure = $this->color($img, '#dfe8e2', 92);
        imagefilledellipse($img, 500, 820, 640, 520, $figure);
        imagefilledellipse($img, 500, 330, 270, 300, $figure);

        $this->centeredText($img, Str::upper($initials), $this->bold, 96, 500, 740, $this->color($img, $accent));

        return $this->save($img, 'staff');
    }

    /* Drawing helpers ---------------------------------------------------- */

    private function canvas(int $w, int $h, bool $transparent = false): GdImage
    {
        $img = imagecreatetruecolor($w, $h);
        imagealphablending($img, true);
        imagesavealpha($img, true);
        imageantialias($img, true);

        if ($transparent) {
            imagealphablending($img, false);
            imagefill($img, 0, 0, imagecolorallocatealpha($img, 0, 0, 0, 127));
            imagealphablending($img, true);
        }

        return $img;
    }

    private function gradient(GdImage $img, string $top, string $bottom): void
    {
        [$r1, $g1, $b1] = $this->rgb($top);
        [$r2, $g2, $b2] = $this->rgb($bottom);
        $h = imagesy($img);

        for ($y = 0; $y < $h; $y++) {
            $t = $y / max(1, $h - 1);
            $c = imagecolorallocate($img, (int) ($r1 + ($r2 - $r1) * $t), (int) ($g1 + ($g2 - $g1) * $t), (int) ($b1 + ($b2 - $b1) * $t));
            imageline($img, 0, $y, imagesx($img), $y, $c);
        }
    }

    /** Soft radial light made of stacked translucent ellipses. */
    private function glow(GdImage $img, int $cx, int $cy, int $size, string $hex, int $alpha): void
    {
        $steps = 40;
        for ($i = 0; $i < $steps; $i++) {
            $d = (int) ($size * (1 - $i / $steps));
            imagefilledellipse($img, $cx, $cy, $d, $d, $this->color($img, $hex, min(127, $alpha + 12 + (int) (($steps - $i) / 4))));
        }
    }

    private function maskTo(GdImage $layer, array $polygon): void
    {
        $mask = $this->canvas(imagesx($layer), imagesy($layer), transparent: true);
        imagealphablending($layer, false); // write transparent pixels instead of blending them
        imagefilledpolygon($mask, array_map('intval', $polygon), imagecolorallocate($mask, 255, 255, 255));

        for ($x = 0; $x < imagesx($layer); $x++) {
            for ($y = 0; $y < imagesy($layer); $y++) {
                if ((imagecolorat($mask, $x, $y) >> 24 & 0x7F) === 127) {
                    imagesetpixel($layer, $x, $y, imagecolorallocatealpha($layer, 0, 0, 0, 127));
                }
            }
        }

        imagedestroy($mask);
        imagealphablending($layer, true);
    }

    private function centeredText(GdImage $img, string $text, string $font, int $size, int $cx, int $baseline, int $color): void
    {
        if ($text === '') {
            return;
        }

        $box = imagettfbbox($size, 0, $font, $text);
        $width = $box[2] - $box[0];
        imagettftext($img, $size, 0, (int) ($cx - $width / 2 - $box[0]), $baseline, $color, $font, $text);
    }

    private function color(GdImage $img, string $hex, int $alpha = 0): int
    {
        [$r, $g, $b] = $this->rgb($hex);

        return imagecolorallocatealpha($img, $r, $g, $b, $alpha);
    }

    /** White or near-black, whichever reads better on the given colour. */
    private function contrast(string $hex): int
    {
        [$r, $g, $b] = $this->rgb($hex);
        $luma = 0.299 * $r + 0.587 * $g + 0.114 * $b;
        $img = imagecreatetruecolor(1, 1);

        return $luma > 150 ? imagecolorallocate($img, 11, 23, 16) : imagecolorallocate($img, 255, 255, 255);
    }

    private function distance(string $a, string $b): float
    {
        [$r1, $g1, $b1] = $this->rgb($a);
        [$r2, $g2, $b2] = $this->rgb($b);

        return sqrt(($r1 - $r2) ** 2 + ($g1 - $g2) ** 2 + ($b1 - $b2) ** 2);
    }

    private function mix(string $a, string $b, float $t): string
    {
        [$r1, $g1, $b1] = $this->rgb($a);
        [$r2, $g2, $b2] = $this->rgb($b);

        return sprintf('#%02x%02x%02x', $r1 + ($r2 - $r1) * $t, $g1 + ($g2 - $g1) * $t, $b1 + ($b2 - $b1) * $t);
    }

    private function rgb(string $hex): array
    {
        $hex = ltrim($hex, '#');

        return [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
    }

    private function save(GdImage $img, string $directory): string
    {
        ob_start();
        imagewebp($img, null, 86);
        $data = ob_get_clean();
        imagedestroy($img);

        $path = "{$directory}/" . Str::random(40) . '.webp';
        Storage::disk('public')->put($path, $data);

        return $path;
    }
}
