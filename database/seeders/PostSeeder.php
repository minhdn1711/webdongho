<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'title' => 'Cách chọn đồng hồ phù hợp với cổ tay nam giới',
                'content' => "Việc chọn một chiếc đồng hồ không chỉ dựa vào sở thích mà còn phải phù hợp với kích thước cổ tay. Đối với nam giới có cổ tay nhỏ (dưới 16cm), nên ưu tiên các dòng đồng hồ có mặt số từ 38mm-40mm. Trong khi đó, cổ tay từ 17cm trở lên có thể tự tin diện những mẫu đồng hồ hầm hố từ 42mm-44mm.\n\nNgoài kích thước, chất liệu dây đeo cũng đóng vai trò quan trọng. Dây da thường mang lại cảm giác thanh lịch, trong khi dây kim loại lại toát lên vẻ mạnh mẽ, thể thao.",
                'image' => 'https://donghohaitrieu.com/wp-content/uploads/2023/08/cach-chon-dong-ho-nam-phu-hop-voi-co-tay-1.jpg',
            ],
            [
                'title' => 'Top 5 xu hướng đồng hồ nữ được yêu thích nhất năm 2024',
                'content' => "Năm 2024 đánh dấu sự lên ngôi của các dòng đồng hồ tối giản (Minimalism) với tông màu pastel nhẹ nhàng. Bên cạnh đó, các mẫu đồng hồ mặt vuông cũng đang quay trở lại mạnh mẽ, mang đến vẻ ngoài cá tính nhưng không kém phần sang trọng cho phái đẹp.\n\nCác thương hiệu như Julius luôn đi đầu trong việc cập nhật những xu hướng này, kết hợp giữa chất lượng máy Nhật Bản và thiết kế thời thượng từ Hàn Quốc.",
                'image' => 'https://julius.vn/wp-content/uploads/2021/10/banner-nu.jpg',
            ],
            [
                'title' => 'Hướng dẫn bảo quản đồng hồ cơ luôn bền đẹp như mới',
                'content' => "Đồng hồ cơ là một kiệt tác của kỹ thuật, nhưng nó cũng đòi hỏi sự chăm sóc kỹ lưỡng. Bạn nên tránh để đồng hồ gần các nguồn từ trường mạnh như loa thùng, tivi hay nam châm. Ngoài ra, việc lên dây cót định kỳ và bảo dưỡng máy mỗi 2-3 năm một lần là cực kỳ cần thiết.\n\nLưu ý quan trọng: Không nên chỉnh giờ vào khung giờ đêm (từ 22h đến 2h sáng) vì đây là thời điểm các bánh răng chuyển lịch đang hoạt động, dễ gây hỏng hóc.",
                'image' => 'https://thewatchclub.vn/wp-content/uploads/2023/10/banner-dong-ho-chinh-hang.jpg',
            ],
        ];

        foreach ($posts as $post) {
            Post::create([
                'title' => $post['title'],
                'slug' => Str::slug($post['title']) . '-' . rand(100, 999),
                'content' => $post['content'],
                'image' => $post['image'],
                'is_published' => true,
            ]);
        }
    }
}
