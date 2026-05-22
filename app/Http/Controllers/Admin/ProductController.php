<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\Attribute;
use App\Models\ProductAttributeValue;
use App\Events\ProductSaved;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Services\StorageService;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Products/Index', [
            'products' => Product::with('categories')->latest()->get()
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Products/Create', [
            'categories' => Category::all(),
            'attributes' => Attribute::with('attributeValues')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_ids'      => 'required|array',
            'category_ids.*'    => 'exists:categories,id',
            'name'              => 'required|string|max:255',
            'short_description' => 'nullable|string',
            'description'       => 'nullable|string',
            'price'             => 'required|numeric|min:0',
            'sale_price'        => 'nullable|numeric|min:0',
            'image'             => 'nullable',
            'image_file'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'stock'             => 'required|integer|min:0',
            'is_featured'       => 'boolean',
            'sku'               => 'nullable|string|max:255',
            'barcode'           => 'nullable|string|max:255',
            'gallery_files.*'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'gallery_urls'      => 'nullable|array',
            'gallery_urls.*'    => 'nullable|string',
        ]);

        $data = $request->except(['image_file', 'category_ids', 'gallery_files', 'gallery_urls', 'delete_image_ids']);
        $data['slug'] = Str::slug($request->name);
        $data['category_id'] = $request->category_ids[0] ?? null;

        if ($request->hasFile('image_file')) {
            $path = StorageService::upload($request->file('image_file'), 'products');
            $data['image'] = Storage::url($path);
        }

        $product = Product::create($data);
        $product->categories()->sync($request->category_ids);

        // Save product attributes
        if ($request->has('product_attributes')) {
            $this->saveProductAttributes($product, $request->product_attributes, $request->attribute_images);
        }

        $this->saveGalleryImages($product, $request);

        event(new ProductSaved($product));

        return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được tạo thành công!');
    }

    public function edit(Product $product)
    {
        return Inertia::render('Admin/Products/Edit', [
            'product'    => $product->load(['categories', 'productImages', 'productAttributeValues']),
            'categories' => Category::all(),
            'attributes' => Attribute::with('attributeValues')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'category_ids'      => 'required|array',
            'category_ids.*'    => 'exists:categories,id',
            'name'              => 'required|string|max:255',
            'short_description' => 'nullable|string',
            'description'       => 'nullable|string',
            'price'             => 'required|numeric|min:0',
            'sale_price'        => 'nullable|numeric|min:0',
            'image'             => 'nullable',
            'image_file'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'stock'             => 'required|integer|min:0',
            'is_featured'       => 'boolean',
            'sku'               => 'nullable|string|max:255',
            'barcode'           => 'nullable|string|max:255',
            'gallery_files.*'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'gallery_urls'      => 'nullable|array',
            'gallery_urls.*'    => 'nullable|string',
            'delete_image_ids'  => 'nullable|array',
            'delete_image_ids.*'=> 'integer',
        ]);

        $data = $request->except(['image_file', 'category_ids', 'gallery_files', 'gallery_urls', 'delete_image_ids']);
        $data['slug'] = Str::slug($request->name);
        $data['category_id'] = $request->category_ids[0] ?? null;

        if ($request->hasFile('image_file')) {
            if ($product->image && str_contains($product->image, '/storage/products/')) {
                $oldPath = str_replace(Storage::url(''), '', $product->image);
                StorageService::delete($oldPath);
            }
            $path = StorageService::upload($request->file('image_file'), 'products');
            $data['image'] = Storage::url($path);
        }

        $product->update($data);
        $product->categories()->sync($request->category_ids);

        // Save product attributes
        if ($request->has('product_attributes')) {
            $product->productAttributeValues()->delete();
            $this->saveProductAttributes($product, $request->product_attributes, $request->attribute_images);
        }

        // Delete removed gallery images
        if ($request->has('delete_image_ids') && is_array($request->delete_image_ids)) {
            ProductImage::whereIn('id', $request->delete_image_ids)
                ->where('product_id', $product->id)
                ->delete();
        }

        $this->saveGalleryImages($product, $request);

        event(new ProductSaved($product));

        return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được cập nhật thành công!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->back()->with('success', 'Sản phẩm đã được xóa thành công!');
    }

    public function fetchDriveImages(Request $request)
    {
        $request->validate(['folder_url' => 'required|string']);

        $apiKey = config('services.google.drive_api_key');
        if (!$apiKey) {
            return response()->json([
                'error' => 'Chưa cấu hình GOOGLE_DRIVE_API_KEY trong file .env'
            ], 422);
        }

        if (!preg_match('/\/folders\/([a-zA-Z0-9_-]+)/', $request->folder_url, $matches)) {
            return response()->json(['error' => 'URL Google Drive folder không hợp lệ'], 422);
        }
        $folderId = $matches[1];

        $response = Http::get('https://www.googleapis.com/drive/v3/files', [
            'q'        => "'{$folderId}' in parents and mimeType contains 'image/' and trashed=false",
            'key'      => $apiKey,
            'fields'   => 'files(id,name,mimeType)',
            'pageSize' => 100,
            'orderBy'  => 'name',
        ]);

        if (!$response->successful()) {
            $msg = $response->json('error.message') ?? 'Không thể truy cập folder.';
            return response()->json(['error' => "Google Drive API: {$msg}"], 422);
        }

        $images = array_map(function ($file) {
            return [
                'id'   => $file['id'],
                'name' => $file['name'],
                'url'  => "https://drive.google.com/thumbnail?id={$file['id']}&sz=w800",
            ];
        }, $response->json('files', []));

        return response()->json(['images' => $images]);
    }

    private function saveProductAttributes(Product $product, array $attributes, ?array $attributeImages = []): void
    {
        foreach ($attributes as $attributeId => $attributeValueIds) {
            if (is_array($attributeValueIds)) {
                foreach ($attributeValueIds as $valueId) {
                    ProductAttributeValue::create([
                        'product_id' => $product->id,
                        'attribute_id' => $attributeId,
                        'attribute_value_id' => $valueId,
                        'image_url' => $attributeImages[$valueId] ?? null,
                    ]);
                }
            }
        }
    }

    private function saveGalleryImages(Product $product, Request $request): void
    {
        $sort = ($product->productImages()->max('sort_order') ?? -1) + 1;

        if ($request->hasFile('gallery_files')) {
            foreach ($request->file('gallery_files') as $file) {
                $path = StorageService::upload($file, 'products');
                $product->productImages()->create([
                    'image_url'  => Storage::url($path),
                    'sort_order' => $sort++,
                ]);
            }
        }

        if ($request->has('gallery_urls') && is_array($request->gallery_urls)) {
            foreach (array_filter($request->gallery_urls) as $url) {
                $product->productImages()->create([
                    'image_url'  => $url,
                    'sort_order' => $sort++,
                ]);
            }
        }
    }
}
