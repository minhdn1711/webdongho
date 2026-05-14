<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function show($slug)
    {
        $product = Product::with(['categories', 'reviews' => function($q) {
            $q->where('is_approved', true)->latest();
        }])->where('slug', $slug)->firstOrFail();
        
        $categoryIds = $product->categories->pluck('id');
        $relatedProducts = Product::whereHas('categories', function($q) use ($categoryIds) {
                $q->whereIn('categories.id', $categoryIds) ->orWhereIn('category_id', $categoryIds);
            })
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        $isWishlisted = false;
        if (Auth::check()) {
            $isWishlisted = Wishlist::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->exists();
        }

        // Tính trung bình sao
        $averageRating = $product->reviews->avg('rating') ?: 0;
        $reviewCount = $product->reviews->count();

        return Inertia::render('Client/ProductDetail', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'isWishlisted' => $isWishlisted,
            'averageRating' => round($averageRating, 1),
            'reviewCount' => $reviewCount
        ]);
    }
}
