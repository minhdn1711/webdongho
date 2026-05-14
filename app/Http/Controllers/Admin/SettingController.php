<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->all();
        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->all();

        foreach ($data as $key => $value) {
            if ($request->hasFile($key)) {
                $path = $request->file($key)->store('settings');
                $value = Storage::url($path);
            } elseif ($request->$key && is_string($request->$key) && str_starts_with($request->$key, 'http')) {
                // Nếu là URL từ thư viện
                $value = $request->$key;
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->back()->with('success', 'Cấu hình đã được cập nhật!');
    }
}
