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
            'productAttributeValues.attribute',
            'productAttributeValues.attributeValue',
            'relatedProducts',
            'reviews' => fn($q) => $q->where('is_approved', true)->latest(),
        ])->where('slug', $slug)->firstOrFail();

        if ($product->is_hidden) {
            abort(404);
        }

        $manualRelated = $product->relatedProducts()->with('categories')->where('is_hidden', false)->limit(8)->get();

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
                ->where('is_hidden', false)
                ->inRandomOrder()
                ->limit(4)
                ->get();
        }

        $isWishlisted = false;
        if (Auth::check()) {
            $isWishlisted = Wishlist::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->exists();
        }

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
