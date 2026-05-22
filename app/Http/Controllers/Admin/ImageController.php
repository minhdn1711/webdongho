<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\StorageService;
use Inertia\Inertia;

class ImageController extends Controller
{
    public function index(Request $request)
    {
        try {
            $folders = ['images', 'banners', 'products'];
            $allFiles = [];
            foreach ($folders as $folder) {
                if (Storage::exists($folder)) {
                    $allFiles = array_merge($allFiles, Storage::allFiles($folder));
                }
            }

            $images = array_map(function ($file) {
                return [
                    'name' => basename($file),
                    'url' => Storage::url($file),
                    'path' => $file,
                    'size' => Storage::size($file),
                    'last_modified' => Storage::lastModified($file),
                ];
            }, $allFiles);

            // Sort by last modified
            usort($images, function ($a, $b) {
                return $b['last_modified'] <=> $a['last_modified'];
            });

            if ($request->wantsJson()) {
                return response()->json(['images' => $images]);
            }

            return Inertia::render('Admin/Images/Index', [
                'images' => $images
            ]);
        } catch (\Exception $e) {
            \Log::error('Media Library Error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'debug_info' => 'Kiểm tra log để biết thêm chi tiết'
                ], 500);
            }
            return Inertia::render('Admin/Images/Index', [
                'images' => [],
                'error' => 'Lỗi kết nối Storage: ' . $e->getMessage()
            ]);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                StorageService::upload($image, 'images');
            }
        }

        return redirect()->back()->with('success', 'Ảnh đã được tải lên thành công!');
    }

    public function destroy(Request $request)
    {
        $path = $request->input('path');
        if (Storage::exists($path)) {
            StorageService::delete($path);
            return back()->with('success', 'Xóa ảnh thành công!');
        }
        return back()->with('error', 'Không tìm thấy ảnh!');
    }
}
