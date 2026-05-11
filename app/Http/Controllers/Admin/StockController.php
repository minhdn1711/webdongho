<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Stock/Index', [
            'products' => Product::with(['category'])->latest()->get(),
            'histories' => StockHistory::with(['product', 'user'])->latest()->take(50)->get()
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'type' => 'required|in:import,export,set',
            'quantity' => 'required|integer|min:0',
            'note' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $oldStock = $product->stock;
            $quantity = $request->quantity;
            $type = $request->type;

            if ($type === 'import') {
                $product->stock += $quantity;
            } elseif ($type === 'export') {
                if ($product->stock < $quantity) {
                    throw new \Exception('Số lượng xuất kho không thể lớn hơn số lượng tồn kho.');
                }
                $product->stock -= $quantity;
            } else {
                $product->stock = $quantity;
            }

            $product->save();

            // Record history
            StockHistory::create([
                'product_id' => $product->id,
                'type' => $type,
                'quantity' => $quantity,
                'old_stock' => $oldStock,
                'new_stock' => $product->stock,
                'note' => $request->note,
                'user_id' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Cập nhật kho hàng thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }
}
