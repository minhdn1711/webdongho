<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WatchSeeder extends Seeder
{
    public function run(): void
    {
        // Categories
        $cats = [
            ['name' => 'Đồng Hồ Nữ', 'slug' => 'dong-ho-nu'],
            ['name' => 'Đồng Hồ Nam', 'slug' => 'dong-ho-nam'],
            ['name' => 'Đồng Hồ Cặp', 'slug' => 'dong-ho-cap'],
            ['name' => 'Julius Star', 'slug' => 'julius-star'],
        ];

        foreach ($cats as $cat) {
            Category::create($cat);
        }

        // Products
        $products = [
            [
                'category_id' => 1,
                'name' => 'Đồng Hồ Nữ JS-057C Julius Star (Trắng)',
                'price' => 2550000,
                'sale_price' => 2295000,
                'image' => 'https://julius.vn/wp-content/uploads/2023/05/JS-057C-1-400x400.jpg',
                'is_featured' => true,
            ],
            [
                'category_id' => 2,
                'name' => 'Đồng Hồ Nam JAL-044D Julius (Đen)',
                'price' => 1290000,
                'sale_price' => null,
                'image' => 'https://julius.vn/wp-content/uploads/2023/04/JAL-044D-1-400x400.jpg',
                'is_featured' => true,
            ],
            [
                'category_id' => 1,
                'name' => 'Đồng Hồ Nữ JA-1164D Julius (Xanh)',
                'price' => 890000,
                'sale_price' => 750000,
                'image' => 'https://julius.vn/wp-content/uploads/2019/11/JA-1164D-1-400x400.jpg',
                'is_featured' => false,
            ],
            [
                'category_id' => 3,
                'name' => 'Đồng Hồ Cặp JA-1340 Julius (Nâu)',
                'price' => 1850000,
                'sale_price' => 1665000,
                'image' => 'https://julius.vn/wp-content/uploads/2022/05/JA-1340-1-400x400.jpg',
                'is_featured' => true,
            ],
        ];

        foreach ($products as $p) {
            $p['slug'] = Str::slug($p['name']);
            Product::create($p);
        }
    }
}
