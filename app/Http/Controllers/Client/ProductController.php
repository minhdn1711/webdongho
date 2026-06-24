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
        $product = Product::with([
            'categories',
            'productImages',
            'reviews' => fn($q) => $q->where('is_approved', true)->latest(),
            'productAttributeValues.attribute',
            'productAttributeValues.attributeValue',
            'relatedProducts',
        ])->where('slug', $slug)->firstOrFail();
        
        // Ưu tiên sản phẩm liên quan được chọn thủ công, fallback về cùng danh mục
        $manualRelated = $product->relatedProducts()->with('categories')->limit(8)->get();

        if ($manualRelated->isNotEmpty()) {
            $relatedProducts = $manualRelated;
        } else {
            $categoryIds = $product->categories->pluck('id')->toArray();
            if ($product->category_id) {
                $categoryIds[] = $product->category_id;
            }
            $categoryIds = array_unique($categoryIds);

            $relatedProducts = Product::where(function($query) use ($categoryIds) {
                    $query->whereIn('category_id', $categoryIds)
                          ->orWhereHas('categories', function($q) use ($categoryIds) {
                              $q->whereIn('categories.id', $categoryIds);
                          });
                })
                ->where('id', '!=', $product->id)
                ->limit(4)
                ->get();
        }

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
