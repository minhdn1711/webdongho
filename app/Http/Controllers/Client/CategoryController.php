<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function show(Request $request, $slug = null)
    {
        $query = Product::query();

        // Filter by Category
        $currentCategory = null;
        if ($slug) {
            $currentCategory = Category::where('slug', $slug)->firstOrFail();
            $query->whereHas('categories', function($q) use ($currentCategory) {
                $q->where('categories.id', $currentCategory->id);
            });
        }

        // Advanced Search & Filters
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->latest();
                break;
        }

        return Inertia::render('Client/Category/Show', [
            'category' => $currentCategory,
            'products' => $query->get(),
            'categories' => Category::all(),
            'filters' => $request->only(['search', 'min_price', 'max_price', 'sort']),
        ]);
    }
}
