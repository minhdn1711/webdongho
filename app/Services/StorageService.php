<?php

namespace App\Services;

use App\Models\StorageUsage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class StorageService
{
    /** Raster formats that get converted to WebP (svg/ico/animated gif are left untouched). */
    private const CONVERTIBLE_MIMES = ['image/jpeg', 'image/png', 'image/webp'];

    private const WEBP_QUALITY = 82;
    private const MAX_WIDTH = 1600;
    private const MAX_PIXELS = 40_000_000; // refuse to decode huge images (memory safety)

    /**
     * Upload file and update storage usage quota.
     * Raster images are resized and converted to WebP before being stored.
     *
     * @throws ValidationException
     */
    public static function upload(UploadedFile $file, string $folder, ?string $disk = null, bool $trackQuota = true): string
    {
        $webp = self::toWebp($file->getRealPath(), $file->getMimeType(), $file->getSize());
        $size = $webp ? strlen($webp) : $file->getSize();

        $storage = null;
        if ($trackQuota) {
            $storage = StorageUsage::firstOrCreate(
                ['id' => 1],
                ['max_bytes' => 1073741824] // default 1GB
            );

            if (($storage->used_bytes + $size) > $storage->max_bytes) {
                throw ValidationException::withMessages([
                    'file' => 'Đã vượt quá dung lượng lưu trữ cho phép của hệ thống.',
                ]);
            }
        }

        // Uses the default disk from config/filesystems.php when $disk is null
        $filesystem = Storage::disk($disk);

        if ($webp) {
            $path = trim($folder, '/') . '/' . Str::random(40) . '.webp';
            $filesystem->put($path, $webp, ['CacheControl' => 'public, max-age=31536000, immutable']);
        } else {
            $path = $filesystem->putFile($folder, $file, ['CacheControl' => 'public, max-age=31536000, immutable']);
        }

        if ($storage) {
            $storage->increment('used_bytes', $size);
        }

        return $path;
    }

    /**
     * Convert an image to a resized WebP binary string.
     * Returns null when the file should be stored as-is (unsupported type, failure, or no size benefit).
     */
    public static function toWebp(string $path, ?string $mime, ?int $originalSize = null): ?string
    {
        if (!in_array($mime, self::CONVERTIBLE_MIMES, true) || !function_exists('imagewebp')) {
            return null;
        }

        try {
            $info = @getimagesize($path);
            if (!$info || ($info[0] * $info[1]) > self::MAX_PIXELS) {
                return null;
            }

            $image = @imagecreatefromstring(file_get_contents($path));
            if (!$image) {
                return null;
            }

            if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
                $orientation = @exif_read_data($path)['Orientation'] ?? 1;
                $angle = [3 => 180, 6 => -90, 8 => 90][$orientation] ?? 0;
                if ($angle) {
                    $rotated = imagerotate($image, $angle, 0);
                    if ($rotated) {
                        $image = $rotated;
                    }
                }
            }

            imagepalettetotruecolor($image);
            imagealphablending($image, false);
            imagesavealpha($image, true);

            if (imagesx($image) > self::MAX_WIDTH) {
                $scaled = imagescale($image, self::MAX_WIDTH, -1, IMG_BICUBIC);
                if ($scaled) {
                    $image = $scaled;
                    imagealphablending($image, false);
                    imagesavealpha($image, true);
                }
            }

            ob_start();
            imagewebp($image, null, self::WEBP_QUALITY);
            $data = ob_get_clean();

            // Keep the original if WebP would not make it smaller (e.g. already-optimised webp)
            if (!$data || ($originalSize !== null && strlen($data) >= $originalSize && $mime === 'image/webp')) {
                return null;
            }

            return $data;
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }

    /**
     * Delete file and decrement storage usage quota
     */
    public static function delete(string $path): void
    {
        if (Storage::exists($path)) {
            $size = Storage::size($path);

            Storage::delete($path);

            $storage = StorageUsage::find(1);
            if ($storage) {
                // Ensure it doesn't go below 0
                if ($storage->used_bytes >= $size) {
                    $storage->decrement('used_bytes', $size);
                } else {
                    $storage->update(['used_bytes' => 0]);
                }
            }
        }
    }
}
