<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Setting;
use App\Mail\LowStockSummaryNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class StockCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:stock-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kiểm tra và gửi email thông báo hàng tồn thấp cho Admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $settings = Setting::pluck('value', 'key')->all();
        
        // Chỉ gửi mail khi bật chế độ "Cho phép đặt hàng khi hết hàng" (theo yêu cầu khách hàng)
        $allowOutOfStock = ($settings['allow_out_of_stock_orders'] ?? '0') === '1';
        
        if (!$allowOutOfStock) {
            $this->info('Chế độ bán hàng khi hết hàng đang tắt. Bỏ qua gửi mail thông báo.');
            return;
        }

        $lowStockThreshold = (int)($settings['low_stock_threshold'] ?? 5);
        $adminEmail = $settings['admin_notification_email'] ?? $settings['contact_email'] ?? null;

        if (!$adminEmail) {
            $this->error('Không tìm thấy email Admin để nhận thông báo.');
            return;
        }

        // Lấy danh sách sản phẩm dưới ngưỡng tồn kho
        $lowStockProducts = Product::where('stock', '<=', $lowStockThreshold)
            ->with('category')
            ->get();

        if ($lowStockProducts->isEmpty()) {
            $this->info('Không có sản phẩm nào dưới ngưỡng tồn kho.');
            return;
        }

        try {
            Mail::to($adminEmail)->send(new LowStockSummaryNotification($lowStockProducts));
            $this->info('Đã gửi email thông báo cho ' . $adminEmail);
            Log::info('Daily stock check mail sent to ' . $adminEmail);
        } catch (\Exception $e) {
            $this->error('Lỗi khi gửi mail: ' . $e->getMessage());
            Log::error('Stock check mail error: ' . $e->getMessage());
        }
    }
}
