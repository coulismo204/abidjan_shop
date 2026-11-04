<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Redimensionne et convertit en WebP les photos envoyées, pour un site rapide
 * même avec une connexion moyenne. Repli : stockage tel quel si GD est absent.
 */
class Images
{
    public static function store(UploadedFile $file, string $dir, int $maxSide = 1400): string
    {
        $path = $dir.'/'.Str::random(24).'.webp';

        if (function_exists('imagewebp') && ($image = @imagecreatefromstring((string) file_get_contents($file->getRealPath())))) {
            $image = self::orient($image, $file);
            $w = imagesx($image);
            $h = imagesy($image);
            $ratio = min(1, $maxSide / max($w, $h));
            if ($ratio < 1) {
                $resized = imagescale($image, (int) round($w * $ratio), (int) round($h * $ratio), IMG_BICUBIC);
                imagedestroy($image);
                $image = $resized;
            }
            ob_start();
            imagewebp($image, null, 80);
            $data = ob_get_clean();
            imagedestroy($image);
            Storage::disk('public')->put($path, $data);

            return $path;
        }

        return $file->store($dir, 'public');
    }

    private static function orient(\GdImage $image, UploadedFile $file): \GdImage
    {
        if (! function_exists('exif_read_data') || ! in_array($file->getMimeType(), ['image/jpeg', 'image/jpg'])) {
            return $image;
        }
        $orientation = @exif_read_data($file->getRealPath())['Orientation'] ?? 1;
        $angle = match ((int) $orientation) {
            3 => 180, 6 => -90, 8 => 90, default => 0,
        };

        return $angle ? imagerotate($image, $angle, 0) : $image;
    }
}
