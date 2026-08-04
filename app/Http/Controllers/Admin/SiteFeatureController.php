<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteFeature;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SiteFeatureController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/SiteFeatures/Index', [
            'features' => SiteFeature::orderBy('order')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/SiteFeatures/Form', ['feature' => null]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon_svg'    => 'nullable|string',
            'order'       => 'integer',
            'is_active'   => 'boolean',
        ]);

        SiteFeature::create($data);

        return redirect()->route('admin.site-features.index')->with('success', 'Đã thêm tính năng.');
    }

    public function edit(SiteFeature $siteFeature)
    {
        return Inertia::render('Admin/SiteFeatures/Form', ['feature' => $siteFeature]);
    }

    public function update(Request $request, SiteFeature $siteFeature)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon_svg'    => 'nullable|string',
            'order'       => 'integer',
            'is_active'   => 'boolean',
        ]);

        $siteFeature->update($data);

        return redirect()->route('admin.site-features.index')->with('success', 'Đã cập nhật.');
    }

    public function destroy(SiteFeature $siteFeature)
    {
        $siteFeature->delete();

        return redirect()->route('admin.site-features.index')->with('success', 'Đã xóa.');
    }
}
