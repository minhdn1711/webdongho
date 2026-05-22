<?php

namespace App\Services;

use App\Models\StorageUsage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class StorageService
{
    /**
     * Upload file and update storage usage quota
     *
     * @param UploadedFile $file
     * @param string $folder
     * @return string
     * @throws ValidationException
     */
    public static function upload(UploadedFile $file, string $folder): string
    {
        // Get global storage usage
        $storage = StorageUsage::firstOrCreate(
            ['id' => 1],
            ['max_bytes' => 1073741824] // default 1GB
        );

        $size = $file->getSize();

        // Check quota
        if (($storage->used_bytes + $size) > $storage->max_bytes) {
            throw ValidationException::withMessages([
                'file' => 'Đã vượt quá dung lượng lưu trữ cho phép của hệ thống.',
            ]);
        }

        // Upload file (uses default disk configured in config/filesystems.php)
        $path = $file->store($folder);

        // Increment used bytes
        $storage->increment('used_bytes', $size);

        return $path;
    }

    /**
     * Delete file and decrement storage usage quota
     *
     * @param string $path
     * @return void
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
