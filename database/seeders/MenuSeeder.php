<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $women = Category::where('slug', 'dong-ho-nu')->first();
        $men = Category::where('slug', 'dong-ho-nam')->first();

        $items = [
            [
                'label' => 'Trang Chủ',
                'source_type' => 'custom',
                'source_id' => null,
                'url' => '/',
                'sort_order' => 10,
            ],
            [
                'label' => 'Đồng Hồ Nữ',
                'source_type' => $women ? 'category' : 'custom',
                'source_id' => $women?->id,
                'url' => $women ? null : '/category/dong-ho-nu',
                'sort_order' => 20,
            ],
            [
                'label' => 'Đồng Hồ Nam',
                'source_type' => $men ? 'category' : 'custom',
                'source_id' => $men?->id,
                'url' => $men ? null : '/category/dong-ho-nam',
                'sort_order' => 30,
            ],
            [
                'label' => 'Sản phẩm',
                'source_type' => 'custom',
                'source_id' => null,
                'url' => '/category',
                'sort_order' => 40,
            ],
            [
                'label' => 'Tin tức',
                'source_type' => 'custom',
                'source_id' => null,
                'url' => '/news',
                'sort_order' => 50,
            ],
        ];

        foreach ($items as $item) {
            Menu::updateOrCreate(
                ['label' => $item['label']],
                array_merge($item, [
                    'is_active' => true,
                    'open_new_tab' => false,
                ])
            );
        }
    }
}
