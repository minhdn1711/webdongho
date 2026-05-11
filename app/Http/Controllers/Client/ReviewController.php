<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10',
            'customer_name' => 'nullable|string|max:255',
        ]);

        Review::create([
            'product_id' => $request->product_id,
            'user_id' => Auth::id(),
            'customer_name' => Auth::check() ? Auth::user()->name : ($request->customer_name ?? 'Khách'),
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_approved' => true, // Mặc định duyệt luôn cho nhanh
        ]);

        return redirect()->back()->with('success', 'Cảm ơn bạn đã đánh giá sản phẩm!');
    }
}
