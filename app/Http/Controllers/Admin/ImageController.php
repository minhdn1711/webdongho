<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ImageController extends Controller
{
    public function index()
    {
        $files = Storage::disk('public')->files('images');
        $images = array_map(function ($file) {
            return [
                'name' => basename($file),
                'url' => Storage::url($file),
                'path' => $file,
                'size' => Storage::disk('public')->size($file),
                'last_modified' => Storage::disk('public')->lastModified($file),
            ];
        }, $files);

        // Sort by last modified
        usort($images, function ($a, $b) {
            return $b['last_modified'] <=> $a['last_modified'];
        });

        return Inertia::render('Admin/Images/Index', [
            'images' => $images
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $image->store('images', 'public');
            }
        }

        return back()->with('success', 'Tải ảnh lên thành công!');
    }

    public function destroy(Request $request)
    {
        $path = $request->input('path');
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
            return back()->with('success', 'Xóa ảnh thành công!');
        }
        return back()->with('error', 'Không tìm thấy ảnh!');
    }
}
