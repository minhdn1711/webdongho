<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Post;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MenuController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Menus/Index', [
            'menus' => Menu::with(['category', 'product', 'post'])->orderBy('sort_order')->orderBy('id')->paginate(20)->withQueryString(),
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'products' => Product::where('is_hidden', false)->orderBy('name')->get(['id', 'name']),
            'posts' => Post::where('is_published', true)->latest()->get(['id', 'title']),
        ]);
    }

    public function store(Request $request)
    {
        Menu::create($this->validatedData($request));

        return back()->with('success', 'Menu đã được tạo thành công!');
    }

    public function update(Request $request, Menu $menu)
    {
        $menu->update($this->validatedData($request));

        return back()->with('success', 'Menu đã được cập nhật thành công!');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();

        return back()->with('success', 'Menu đã được xóa thành công!');
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'source_type' => ['required', 'in:category,product,post,custom'],
            'source_id' => ['nullable', 'integer'],
            'url' => ['nullable', 'string', 'max:2048'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'open_new_tab' => ['boolean'],
        ]);

        if ($data['source_type'] === 'category') {
            validator($data, ['source_id' => ['required', 'exists:categories,id']])->validate();
            $data['url'] = null;
        } elseif ($data['source_type'] === 'product') {
            validator($data, ['source_id' => ['required', 'exists:products,id']])->validate();
            $data['url'] = null;
        } elseif ($data['source_type'] === 'post') {
            validator($data, ['source_id' => ['required', 'exists:posts,id']])->validate();
            $data['url'] = null;
        } else {
            validator($data, ['url' => ['required', 'string', 'max:2048']])->validate();
            $data['source_id'] = null;
        }

        $data['is_active'] = $request->boolean('is_active');
        $data['open_new_tab'] = $request->boolean('open_new_tab');

        return $data;
    }
}
