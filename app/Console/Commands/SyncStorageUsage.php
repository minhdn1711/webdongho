<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\StorageUsage;

class SyncStorageUsage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:sync-usage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync the total storage usage of the system from the storage disk.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Đang đồng bộ dung lượng lưu trữ...');

        $totalSize = 0;
        
        // Quét toàn bộ các file trong thư mục gốc của disk mặc định (thường là s3 minio)
        $files = Storage::allFiles('/');
        
        $bar = $this->output->createProgressBar(count($files));
        $bar->start();

        foreach ($files as $file) {
            $totalSize += Storage::size($file);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $storage = StorageUsage::firstOrCreate(
            ['id' => 1],
            ['max_bytes' => 1073741824] // Mặc định 1GB
        );

        $storage->update(['used_bytes' => $totalSize]);

        $this->info("Đã đồng bộ xong! Tổng dung lượng: " . number_format($totalSize / 1048576, 2) . " MB.");
    }
}
