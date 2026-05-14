<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('order')->get()->map(function($banner) {
            $banner->image_url = Storage::url($banner->image);
            return $banner;
        });

        return Inertia::render('Admin/Banners/Index', [
            'banners' => $banners
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Banners/Form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required',
            'title' => 'nullable|string|max:255',
            'link' => 'nullable|url',
            'order' => 'integer',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('banners');
        } else {
            // Nếu chọn từ thư viện, nó gửi lên dạng URL hoặc Path.
            // Chúng ta cần lấy phần path tương đối để lưu vào DB.
            $path = str_replace(Storage::url(''), '', $request->image);
            $path = ltrim($path, '/');
        }

        Banner::create([
            'title' => $request->title,
            'image' => $path,
            'link' => $request->link,
            'order' => $request->order ?? 0,
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner created successfully.');
    }

    public function edit(Banner $banner)
    {
        return Inertia::render('Admin/Banners/Form', [
            'banner' => $banner
        ]);
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'image' => 'nullable',
            'title' => 'nullable|string|max:255',
            'link' => 'nullable|url',
            'order' => 'integer',
        ]);

        $data = $request->only(['title', 'link', 'order', 'is_active']);

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ trên S3 nếu là ảnh mới hoàn toàn
            if ($banner->image) {
                Storage::delete($banner->image);
            }
            $data['image'] = $request->file('image')->store('banners');
        } elseif ($request->image && is_string($request->image) && !str_contains($request->image, $banner->image)) {
            // Nếu chọn ảnh khác từ thư viện
            $path = str_replace(Storage::url(''), '', $request->image);
            $data['image'] = ltrim($path, '/');
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully.');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image) {
            Storage::delete($banner->image);
        }
        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Banner deleted successfully.');
    }
}
