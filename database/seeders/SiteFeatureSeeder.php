<?php

namespace Database\Seeders;

use App\Models\SiteFeature;
use Illuminate\Database\Seeder;

class SiteFeatureSeeder extends Seeder
{
    public function run(): void
    {
        if (SiteFeature::count() > 0) return;

        $features = [
            [
                'title'       => '100% Chính hãng',
                'description' => 'Cam kết mọi sản phẩm bán ra đều là hàng chính hãng Julius Hàn Quốc, đầy đủ hộp sổ thẻ.',
                'icon_svg'    => '<path d="M5 13l4 4L19 7" />',
                'order'       => 1,
                'is_active'   => true,
            ],
            [
                'title'       => 'Bảo hành 12 tháng',
                'description' => 'Chế độ hậu mãi chuyên nghiệp, thay pin miễn phí trọn đời cho mọi đơn hàng tại website.',
                'icon_svg'    => '<path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />',
                'order'       => 2,
                'is_active'   => true,
            ],
            [
                'title'       => 'Giao hàng tận nơi',
                'description' => 'Giao hàng nhanh chóng toàn quốc, thanh toán khi nhận hàng (COD), hỗ trợ kiểm tra hàng.',
                'icon_svg'    => '<path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />',
                'order'       => 3,
                'is_active'   => true,
            ],
        ];

        foreach ($features as $f) {
            SiteFeature::create($f);
        }
    }
}
