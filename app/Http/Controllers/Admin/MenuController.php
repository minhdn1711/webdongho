<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Post;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with(['category', 'product', 'post'])
            ->withCount('children')
            ->with(['children' => fn ($q) => $q->with(['category', 'product', 'post'])])
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Menus/Index', [
            'menus' => $menus,
            'parentOptions' => Menu::whereNull('parent_id')->orderBy('sort_order')->orderBy('label')->get(['id', 'label']),
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
        $menu->update($this->validatedData($request, $menu));

        return back()->with('success', 'Menu đã được cập nhật thành công!');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();

        return back()->with('success', 'Menu đã được xóa thành công!');
    }

    private function validatedData(Request $request, ?Menu $menu = null): array
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:menus,id'],
            'source_type' => ['required', 'in:category,product,post,custom'],
            'source_id' => ['nullable', 'integer'],
            'url' => ['nullable', 'string', 'max:2048'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'open_new_tab' => ['boolean'],
        ]);

        if (!empty($data['parent_id'])) {
            if ($menu && (int) $data['parent_id'] === $menu->id) {
                throw ValidationException::withMessages(['parent_id' => 'Menu không thể là menu cha của chính nó.']);
            }

            $parent = Menu::find($data['parent_id']);
            if ($parent && $parent->parent_id) {
                throw ValidationException::withMessages(['parent_id' => 'Chỉ hỗ trợ menu cha - con (2 cấp), không thể chọn một menu con làm menu cha.']);
            }

            if ($menu && $menu->children()->exists()) {
                throw ValidationException::withMessages(['parent_id' => 'Menu này đang có menu con, không thể đặt làm menu con của menu khác.']);
            }
        }

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
