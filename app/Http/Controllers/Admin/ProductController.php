<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Events\ProductSaved;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Products/Index', [
            'products' => Product::with('categories')->latest()->get()
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Products/Create', [
            'categories' => Category::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'image' => 'nullable',
            'image_file' => 'nullable|image|max:2048',
            'stock' => 'required|integer|min:0',
            'is_featured' => 'boolean',
            'sku' => 'nullable|string|max:255',
            'barcode' => 'nullable|string|max:255',
        ]);

        $data = $request->except(['image_file', 'category_ids']);
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('products');
            $data['image'] = Storage::url($path);
        }

        $product = Product::create($data);
        $product->categories()->sync($request->category_ids);

        event(new ProductSaved($product));

        return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được tạo thành công!');
    }

    public function edit(Product $product)
    {
        return Inertia::render('Admin/Products/Edit', [
            'product' => $product->load('categories'),
            'categories' => Category::all()
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'image' => 'nullable',
            'image_file' => 'nullable|image|max:2048',
            'stock' => 'required|integer|min:0',
            'is_featured' => 'boolean',
            'sku' => 'nullable|string|max:255',
            'barcode' => 'nullable|string|max:255',
        ]);

        $data = $request->except(['image_file', 'category_ids']);
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('image_file')) {
            // Delete old image if it was a local file
            if ($product->image && (str_contains($product->image, '/storage/products/') || str_contains($product->image, env('AWS_BUCKET')))) {
                $oldPath = str_replace(Storage::url(''), '', $product->image);
                Storage::delete($oldPath);
            }
            
            $path = $request->file('image_file')->store('products');
            $data['image'] = Storage::url($path);
        }

        $product->update($data);
        $product->categories()->sync($request->category_ids);

        event(new ProductSaved($product));

        return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được cập nhật thành công!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->back()->with('success', 'Sản phẩm đã được xóa thành công!');
    }
}
