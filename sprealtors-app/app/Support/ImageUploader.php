<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploader
{
    /** Longest edge for stored originals. */
    private const MAX_WIDTH = 1600;

    /**
     * Store an uploaded image as WebP (falling back to the original format if
     * GD lacks WebP support) and return the storage-relative path.
     */
    public static function store(UploadedFile $file, string $directory): string
    {
        $name = Str::random(20);
        $extension = strtolower($file->getClientOriginalExtension());

        // Non-raster or GD unavailable: store as-is.
        if (! function_exists('imagecreatefromstring') || $extension === 'svg') {
            return $file->store($directory, 'public');
        }

        $image = @imagecreatefromstring((string) file_get_contents($file->getRealPath()));

        if ($image === false) {
            return $file->store($directory, 'public');
        }

        $image = self::resize($image);

        $useWebp = function_exists('imagewebp');
        $filename = $name.($useWebp ? '.webp' : '.jpg');
        $path = trim($directory, '/').'/'.$filename;

        $temp = tempnam(sys_get_temp_dir(), 'spr');

        if ($useWebp) {
            imagewebp($image, $temp, 82);
        } else {
            imagejpeg($image, $temp, 82);
        }

        imagedestroy($image);

        Storage::disk('public')->put($path, (string) file_get_contents($temp));
        @unlink($temp);

        return $path;
    }

    /**
     * Replace an existing image, deleting the old file first.
     */
    public static function replace(UploadedFile $file, string $directory, ?string $existing): string
    {
        self::delete($existing);

        return self::store($file, $directory);
    }

    public static function delete(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Downscale to MAX_WIDTH while preserving aspect ratio and transparency.
     *
     * @param  \GdImage  $image
     * @return \GdImage
     */
    private static function resize($image)
    {
        $width = imagesx($image);
        $height = imagesy($image);

        if ($width <= self::MAX_WIDTH) {
            return $image;
        }

        $newWidth = self::MAX_WIDTH;
        $newHeight = (int) round($height * ($newWidth / $width));

        $resized = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($image);

        return $resized;
    }
}
