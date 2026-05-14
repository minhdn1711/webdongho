<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Aws\S3\S3Client;

class MakeStoragePublic extends Command
{
    protected $signature = 'storage:make-public';
    protected $description = 'Set S3 bucket policy to public for MinIO';

    public function handle()
    {
        $this->info('Đang cấu hình Bucket thành Public...');

        try {
            $disk = Storage::disk('s3');
            $adapter = $disk->getAdapter();
            
            // Lấy client S3 từ adapter
            $client = $adapter->getClient();
            $bucket = env('AWS_BUCKET');

            $policy = [
                'Version' => '2012-10-17',
                'Statement' => [
                    [
                        'Sid' => 'PublicRead',
                        'Effect' => 'Allow',
                        'Principal' => '*',
                        'Action' => ['s3:GetObject'],
                        'Resource' => ["arn:aws:s3:::{$bucket}/*"]
                    ]
                ]
            ];

            $client->putBucketPolicy([
                'Bucket' => $bucket,
                'Policy' => json_encode($policy),
            ]);

            $this->info("Thành công! Bucket '{$bucket}' hiện đã có quyền truy cập công khai.");
        } catch (\Exception $e) {
            $this->error('Lỗi: ' . $e->getMessage());
        }
    }
}
