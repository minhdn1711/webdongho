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
        $isAjax = $request->wantsJson() && !$request->header('X-Inertia');

        try {
            $folders = ['images', 'banners', 'products'];
            $allFiles = [];
            foreach ($folders as $folder) {
                if (Storage::exists($folder)) {
                    $files = Storage::allFiles($folder);
                    if (is_array($files)) {
                        $allFiles = array_merge($allFiles, $files);
                    }
                }
            }

            $images = [];
            foreach ($allFiles as $file) {
                try {
                    $images[] = [
                        'name'          => basename($file),
                        'url'           => Storage::url($file),
                        'path'          => $file,
                        'size'          => Storage::size($file) ?: 0,
                        'last_modified' => Storage::lastModified($file) ?: 0,
                    ];
                } catch (\Throwable $e) {
                    // skip files that can't be accessed
                }
            }

            usort($images, fn($a, $b) => $b['last_modified'] <=> $a['last_modified']);

            if ($isAjax) {
                return response()->json(['images' => $images]);
            }

            return Inertia::render('Admin/Images/Index', ['images' => $images]);

        } catch (\Throwable $e) {
            // Log safely — nếu log thất bại không được throw ra ngoài
            try {
                \Log::error('Media Library Error: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                ]);
            } catch (\Throwable) {
                // ignore log failure
            }

            if ($isAjax) {
                return response()->json([
                    'images' => [],
                    'error'  => $e->getMessage(),
                ]);
            }

            return Inertia::render('Admin/Images/Index', [
                'images' => [],
                'error'  => 'Lỗi Storage: ' . $e->getMessage(),
            ]);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:20480', // 20MB/file
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
