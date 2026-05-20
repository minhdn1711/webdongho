<?php

namespace App\Http\Controllers\Admin;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AttributeValueController
{
    /**
     * Display a listing of attribute values for a specific attribute.
     */
    public function index(Attribute $attribute)
    {
        $values = $attribute->attributeValues()->paginate(15);

        return Inertia::render('Admin/Attributes/Values/Index', [
            'attribute' => $attribute,
            'values' => $values,
        ]);
    }

    /**
     * Show the form for creating a new attribute value.
     */
    public function create(Attribute $attribute)
    {
        return Inertia::render('Admin/Attributes/Values/Create', [
            'attribute' => $attribute,
        ]);
    }

    /**
     * Store a newly created attribute value in storage.
     */
    public function store(Request $request, Attribute $attribute)
    {
        $validated = $request->validate([
            'value' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'meta_value' => 'nullable|string',
        ]);

        $validated['attribute_id'] = $attribute->id;
        AttributeValue::create($validated);

        return redirect()->route('admin.attributes.values.index', $attribute)
                       ->with('success', 'Giá trị thuộc tính đã được tạo.');
    }

    /**
     * Show the form for editing the specified attribute value.
     */
    public function edit(Attribute $attribute, AttributeValue $value)
    {
        return Inertia::render('Admin/Attributes/Values/Edit', [
            'attribute' => $attribute,
            'value' => $value,
        ]);
    }

    /**
     * Update the specified attribute value in storage.
     */
    public function update(Request $request, Attribute $attribute, AttributeValue $value)
    {
        $validated = $request->validate([
            'value' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'meta_value' => 'nullable|string',
        ]);

        $value->update($validated);

        return redirect()->route('admin.attributes.values.index', $attribute)
                       ->with('success', 'Giá trị thuộc tính đã được cập nhật.');
    }

    /**
     * Remove the specified attribute value from storage.
     */
    public function destroy(Attribute $attribute, AttributeValue $value)
    {
        $value->delete();

        return redirect()->route('admin.attributes.values.index', $attribute)
                       ->with('success', 'Giá trị thuộc tính đã được xóa.');
    }
}
