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
            $bucket = env('AWS_BUCKET');
            
            // Khởi tạo S3 Client thủ công từ cấu hình .env
            $client = new S3Client([
                'version' => 'latest',
                'region'  => env('AWS_DEFAULT_REGION', 'us-east-1'),
                'endpoint' => env('AWS_ENDPOINT'),
                'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', true),
                'credentials' => [
                    'key'    => env('AWS_ACCESS_KEY_ID'),
                    'secret' => env('AWS_SECRET_ACCESS_KEY'),
                ],
            ]);

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
