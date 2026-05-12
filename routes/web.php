<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'categories' => \App\Models\Category::all(),
        'products' => \App\Models\Product::with('category')->where('is_featured', true)->get(),
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

Route::redirect('/admin', '/admin/dashboard');

Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Quản lý sản phẩm
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

    // Pancake POS Integration
    Route::prefix('pancake')->group(function () {
        Route::get('/settings', [\Modules\PancakeIntegration\Http\Controllers\Admin\PancakeController::class, 'settings'])->name('admin.pancake.settings');
        Route::post('/settings', [\Modules\PancakeIntegration\Http\Controllers\Admin\PancakeController::class, 'updateSettings'])->name('admin.pancake.settings.update');
        Route::get('/sync-logs', [\Modules\PancakeIntegration\Http\Controllers\Admin\PancakeController::class, 'syncLogs'])->name('admin.pancake.sync-logs');
        Route::get('/webhook-logs', [\Modules\PancakeIntegration\Http\Controllers\Admin\PancakeController::class, 'webhookLogs'])->name('admin.pancake.webhook-logs');
        Route::post('/manual-sync/{type}/{id}', [\Modules\PancakeIntegration\Http\Controllers\Admin\PancakeController::class, 'manualSync'])->name('admin.pancake.manual-sync');
        Route::post('/retry-sync/{id}', [\Modules\PancakeIntegration\Http\Controllers\Admin\PancakeController::class, 'retrySync'])->name('admin.pancake.retry-sync');
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
