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
            $config = config('filesystems.disks.s3');
            $bucket = $config['bucket'];
            
            // Khởi tạo S3 Client từ cấu hình chính thức của Laravel
            $client = new S3Client([
                'version' => 'latest',
                'region'  => $config['region'],
                'endpoint' => $config['endpoint'],
                'use_path_style_endpoint' => $config['use_path_style_endpoint'] ?? true,
                'credentials' => [
                    'key'    => $config['key'],
                    'secret' => $config['secret'],
                ],
            ]);

            // Kiểm tra và tạo bucket nếu chưa có
            if (!$client->doesBucketExist($bucket)) {
                $this->info("Bucket '{$bucket}' chưa tồn tại. Đang tiến hành tạo...");
                $client->createBucket(['Bucket' => $bucket]);
                $this->info("Đã tạo Bucket '{$bucket}' thành công.");
            }

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
