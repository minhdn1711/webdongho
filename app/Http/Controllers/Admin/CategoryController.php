<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use App\Events\CategorySaved;

class CategoryController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Categories/Index', [
            'categories' => Category::withCount('products')->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|string'
        ]);

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'image' => $request->image
        ]);

        event(new CategorySaved($category));

        return redirect()->back()->with('success', 'Danh mục đã được tạo thành công!');
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|string'
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'image' => $request->image
        ]);

        event(new CategorySaved($category));

        return redirect()->back()->with('success', 'Danh mục đã được cập nhật thành công!');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return redirect()->back()->withErrors(['message' => 'Không thể xóa danh mục đang có sản phẩm.']);
        }
        $category->delete();
        return redirect()->back()->with('success', 'Danh mục đã được xóa thành công!');
    }
}
