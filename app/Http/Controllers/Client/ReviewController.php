<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10',
            'customer_name' => 'nullable|string|max:255',
            'images.*' => 'nullable|image|max:2048',
        ]);

        // Upload images to MinIO
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = \App\Services\StorageService::upload($image, 'reviews', 's3', false);
                $imagePaths[] = Storage::disk('s3')->url($path);
            }
        }

        Review::create([
            'product_id' => $request->product_id,
            'user_id' => Auth::id(),
            'customer_name' => Auth::check() ? Auth::user()->name : ($request->customer_name ?? 'Khách'),
            'rating' => $request->rating,
            'comment' => $request->comment,
            'images' => !empty($imagePaths) ? $imagePaths : null,
            'is_approved' => true,
        ]);

        return redirect()->back()->with('success', 'Cảm ơn bạn đã đánh giá sản phẩm!');
    }
}
