<?php

namespace App\Console\Commands;

use App\Services\StorageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ConvertImagesToWebp extends Command
{
    protected $signature = 'images:webp
        {--disk=* : Disk(s) to process (default: the default filesystem disk)}
        {--folder=* : Folder(s) to process (default: images, banners, products, settings, reviews)}
        {--dry-run : Only report what would change}
        {--delete-old : Delete the original file after its references were updated}';

    protected $description = 'Convert existing JPEG/PNG images to WebP and update database references';

    /** table => columns that may hold an image path/URL (plain, or embedded in HTML/JSON). */
    private const REFERENCES = [
        'products' => ['image', 'description'],
        'product_images' => ['image_url'],
        'product_attribute_values' => ['image_url'],
        'categories' => ['image'],
        'banners' => ['image'],
        'posts' => ['image', 'content'],
        'settings' => ['value'],
        'reviews' => ['images'],
    ];

    public function handle(): int
    {
        $disks = $this->option('disk') ?: [config('filesystems.default')];
        $folders = $this->option('folder') ?: ['images', 'banners', 'products', 'settings', 'reviews'];
        $dry = $this->option('dry-run');

        $converted = 0;
        $saved = 0;

        foreach ($disks as $diskName) {
            $disk = Storage::disk($diskName);

            foreach ($folders as $folder) {
                foreach ($disk->allFiles($folder) as $path) {
                    if (!preg_match('/\.(jpe?g|png)$/i', $path)) {
                        continue;
                    }

                    $newPath = preg_replace('/\.(jpe?g|png)$/i', '.webp', $path);
                    if ($disk->exists($newPath)) {
                        // Already converted: make sure references point to the webp, then optionally drop the original.
                        if ($this->option('delete-old')) {
                            $this->line(($dry ? 'would delete' : 'delete') . " original: {$path}");
                            if (!$dry) {
                                $this->updateReferences($path, $newPath);
                                $disk->delete($path);
                            }
                        } else {
                            $this->line("skip (webp exists): {$path}");
                        }
                        continue;
                    }

                    $tmp = tempnam(sys_get_temp_dir(), 'webp');
                    try {
                        file_put_contents($tmp, $disk->get($path));
                        $mime = mime_content_type($tmp);
                        $oldSize = filesize($tmp);
                        $webp = StorageService::toWebp($tmp, $mime, $oldSize);
                    } finally {
                        @unlink($tmp);
                    }

                    if (!$webp) {
                        $this->warn("skip (cannot convert): {$path}");
                        continue;
                    }

                    $this->info(sprintf('%s -> .webp  %s -> %s', $path, $this->kb($oldSize), $this->kb(strlen($webp))));
                    $converted++;
                    $saved += $oldSize - strlen($webp);

                    if ($dry) {
                        continue;
                    }

                    $disk->put($newPath, $webp, ['CacheControl' => 'public, max-age=31536000, immutable']);
                    $this->updateReferences($path, $newPath);

                    if ($this->option('delete-old')) {
                        $disk->delete($path);
                    }
                }
            }
        }

        $this->newLine();
        $this->info(sprintf('%d image(s) %s, saved ~%s', $converted, $dry ? 'would be converted' : 'converted', $this->kb($saved)));

        if (!$dry && $converted) {
            $this->comment('Tip: run `php artisan storage:sync-usage` to refresh the quota, and clear the CDN cache.');
        }

        return self::SUCCESS;
    }

    private function updateReferences(string $old, string $new): void
    {
        // JSON columns store "/" escaped as "\/", so replace both spellings.
        $pairs = [[$old, $new], [str_replace('/', '\/', $old), str_replace('/', '\/', $new)]];

        foreach (self::REFERENCES as $table => $columns) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as $column) {
                if (!Schema::hasColumn($table, $column)) {
                    continue;
                }

                $expr = "`{$column}`";
                $bindings = [];
                foreach ($pairs as [$from, $to]) {
                    $expr = "REPLACE({$expr}, ?, ?)";
                    array_push($bindings, $from, $to);
                }

                $where = implode(' OR ', array_fill(0, count($pairs), "`{$column}` LIKE ?"));
                foreach ($pairs as [$from]) {
                    $bindings[] = '%' . addcslashes($from, '%_\\') . '%';
                }

                DB::update("UPDATE `{$table}` SET `{$column}` = {$expr} WHERE {$where}", $bindings);
            }
        }
    }

    private function kb(int $bytes): string
    {
        return number_format($bytes / 1024, 1) . ' KB';
    }
}
