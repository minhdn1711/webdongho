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
            'categories' => Category::with('parent')->withCount('products')->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id'
        ]);

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'image' => $request->image,
            'parent_id' => $request->parent_id
        ]);

        event(new CategorySaved($category));

        return redirect()->back()->with('success', 'Danh mục đã được tạo thành công!');
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id|different:id'
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'image' => $request->image,
            'parent_id' => $request->parent_id
        ]);

        event(new CategorySaved($category));

        return redirect()->back()->with('success', 'Danh mục đã được cập nhật thành công!');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return redirect()->back()->withErrors(['message' => 'Không thể xóa danh mục đang có sản phẩm.']);
        }

        if ($category->children()->count() > 0) {
            return redirect()->back()->withErrors(['message' => 'Không thể xóa danh mục đang có danh mục con.']);
        }

        $category->delete();
        return redirect()->back()->with('success', 'Danh mục đã được xóa thành công!');
    }
}
