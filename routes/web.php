<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'banners' => \App\Models\Banner::where('is_active', true)->orderBy('order')->get()->map(function($banner) {
            $banner->image_url = \Illuminate\Support\Facades\Storage::url($banner->image);
            return $banner;
        }),
        'categories' => \App\Models\Category::whereNull('parent_id')->get(),
        'products' => \App\Models\Product::with('categories')->where('is_featured', true)->where('is_hidden', false)->get(),
        'latest_posts' => \App\Models\Post::where('is_published', true)->latest()->limit(3)->get(),
    ]);
});

Route::get('/cart', function () {
    return Inertia::render('Client/Cart');
})->name('cart.index');

Route::get('/checkout', [App\Http\Controllers\Client\CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [App\Http\Controllers\Client\CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/success/{order}', [App\Http\Controllers\Client\CheckoutController::class, 'success'])->name('checkout.success');

Route::get('/category/{slug?}', [App\Http\Controllers\Client\CategoryController::class, 'show'])->name('category.show');
Route::get('/product/{slug}', [App\Http\Controllers\Client\ProductController::class, 'show'])->name('product.show');

// Reviews
Route::post('/reviews', [App\Http\Controllers\Client\ReviewController::class, 'store'])->name('reviews.store');

// Wishlist
Route::middleware('auth')->group(function () {
    Route::get('/wishlist', [App\Http\Controllers\Client\WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle', [App\Http\Controllers\Client\WishlistController::class, 'toggle'])->name('wishlist.toggle');
});

// Admin Auth
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [\App\Http\Controllers\Admin\Auth\AdminLoginController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [\App\Http\Controllers\Admin\Auth\AdminLoginController::class, 'store'])->name('admin.login.store');
});
Route::post('/admin/logout', [\App\Http\Controllers\Admin\Auth\AdminLoginController::class, 'destroy'])->name('admin.logout');

Route::redirect('/admin', '/admin/dashboard');

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        $totalOrders = \App\Models\Order::count();
        $totalProducts = \App\Models\Product::count();
        $totalRevenue = \App\Models\Order::where('status', 'completed')->sum('total_amount');

        return Inertia::render('Dashboard', [
            'stats' => [
                'total_orders' => $totalOrders,
                'total_products' => $totalProducts,
                'total_revenue' => $totalRevenue,
            ]
        ]);
    })->name('dashboard');

    // Quản lý sản phẩm
    Route::post('/products/fetch-drive-images', [App\Http\Controllers\Admin\ProductController::class, 'fetchDriveImages'])->name('admin.products.fetch-drive');
    Route::get('/products/{product}/duplicate', [App\Http\Controllers\Admin\ProductController::class, 'duplicate'])->name('admin.products.duplicate');
    Route::patch('/products/{product}/toggle-hide', [App\Http\Controllers\Admin\ProductController::class, 'toggleHide'])->name('admin.products.toggle-hide');
    Route::resource('products', App\Http\Controllers\Admin\ProductController::class)->names('admin.products');

    // Quản lý danh mục
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class)->names('admin.categories');

    // Quản lý kho hàng
    Route::get('/stock', [App\Http\Controllers\Admin\StockController::class, 'index'])->name('admin.stock.index');
    Route::patch('/stock/{product}', [App\Http\Controllers\Admin\StockController::class, 'update'])->name('admin.stock.update');

    // Quản lý đơn hàng
    Route::get('/orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/{order}', [App\Http\Controllers\Admin\OrderController::class, 'show'])->name('admin.orders.show');
    // Change to patch for status update
    Route::patch('/orders/{order}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('admin.orders.update-status');
    Route::delete('/orders/{order}', [App\Http\Controllers\Admin\OrderController::class, 'destroy'])->name('admin.orders.destroy');

    // Cấu hình hệ thống
    Route::get('/settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('admin.settings.index');
    Route::post('/settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('admin.settings.update');

    // Quản lý hình ảnh
    Route::get('/images', [App\Http\Controllers\Admin\ImageController::class, 'index'])->name('admin.images.index');
    Route::post('/images', [App\Http\Controllers\Admin\ImageController::class, 'store'])->name('admin.images.store');
    Route::delete('/images', [App\Http\Controllers\Admin\ImageController::class, 'destroy'])->name('admin.images.destroy');

    // Quản lý tin tức
    Route::resource('posts', App\Http\Controllers\Admin\PostController::class)->names('admin.posts');

    // Quản lý Banner
    Route::resource('banners', App\Http\Controllers\Admin\BannerController::class)->names('admin.banners');

    // Quản lý Thuộc tính sản phẩm
    Route::resource('attributes', App\Http\Controllers\Admin\AttributeController::class)->names('admin.attributes');
    Route::resource('attributes.values', App\Http\Controllers\Admin\AttributeValueController::class)->names('admin.attributes.values');

    // Quản lý Đánh giá
    Route::get('/reviews', [App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('admin.reviews.index');
    Route::post('/reviews/{review}/approve', [App\Http\Controllers\Admin\ReviewController::class, 'approve'])->name('admin.reviews.approve');
    Route::post('/reviews/{review}/reject', [App\Http\Controllers\Admin\ReviewController::class, 'reject'])->name('admin.reviews.reject');
    Route::delete('/reviews/{review}', [App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('admin.reviews.destroy');

    // Pancake POS Integration
    Route::prefix('pancake')->group(function () {
        Route::get('/settings', [\Modules\PancakeIntegration\Http\Controllers\Admin\PancakeController::class, 'settings'])->name('admin.pancake.settings');
        Route::post('/settings', [\Modules\PancakeIntegration\Http\Controllers\Admin\PancakeController::class, 'updateSettings'])->name('admin.pancake.settings.update');
        Route::get('/sync-logs', [\Modules\PancakeIntegration\Http\Controllers\Admin\PancakeController::class, 'syncLogs'])->name('admin.pancake.sync-logs');
        Route::get('/webhook-logs', [\Modules\PancakeIntegration\Http\Controllers\Admin\PancakeController::class, 'webhookLogs'])->name('admin.pancake.webhook-logs');
        Route::post('/manual-sync/{type}/{id}', [\Modules\PancakeIntegration\Http\Controllers\Admin\PancakeController::class, 'manualSync'])->name('admin.pancake.manual-sync');
        Route::post('/retry-sync/{id}', [\Modules\PancakeIntegration\Http\Controllers\Admin\PancakeController::class, 'retrySync'])->name('admin.pancake.retry-sync');

        // Ẩn/hiện & tồn kho sản phẩm
        Route::post('/products/{id}/hide', [\Modules\PancakeIntegration\Http\Controllers\Admin\PancakeController::class, 'hideProduct'])->name('admin.pancake.product.hide');
        Route::post('/products/{id}/show', [\Modules\PancakeIntegration\Http\Controllers\Admin\PancakeController::class, 'showProduct'])->name('admin.pancake.product.show');
        Route::post('/products/{id}/sync-stock', [\Modules\PancakeIntegration\Http\Controllers\Admin\PancakeController::class, 'syncStock'])->name('admin.pancake.product.sync-stock');

        // Kho (Warehouse)
        Route::get('/warehouses', [\Modules\PancakeIntegration\Http\Controllers\Admin\PancakeController::class, 'warehouses'])->name('admin.pancake.warehouses');
        Route::post('/warehouses', [\Modules\PancakeIntegration\Http\Controllers\Admin\PancakeController::class, 'createWarehouse'])->name('admin.pancake.warehouses.create');
        Route::put('/warehouses', [\Modules\PancakeIntegration\Http\Controllers\Admin\PancakeController::class, 'updateWarehouse'])->name('admin.pancake.warehouses.update');
        Route::post('/warehouses/set-default', [\Modules\PancakeIntegration\Http\Controllers\Admin\PancakeController::class, 'setDefaultWarehouse'])->name('admin.pancake.warehouses.set-default');
    });
});

// Client News Routes
Route::get('/news', [App\Http\Controllers\Client\NewsController::class, 'index'])->name('news.index');
Route::get('/news/{slug}', [App\Http\Controllers\Client\NewsController::class, 'show'])->name('news.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
