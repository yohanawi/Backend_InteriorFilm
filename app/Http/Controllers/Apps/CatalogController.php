<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
use App\Models\Catalog;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CatalogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $catalogs = Catalog::withCount('categories', 'products')
            ->whereNull('deleted_at')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(10);

        return view('pages.apps.catalog.catalogs.index', compact('catalogs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::all();
        return view('pages.apps.catalog.catalogs.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:catalogs,slug',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'related_product_id' => 'nullable|exists:products,id',
        ]);

        if (!isset($validated['slug']) || empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('catalogs', 'public');
        }

        $catalog = Catalog::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Catalog created successfully!',
                'data' => $catalog
            ]);
        }

        return redirect()
            ->route('catalog.catalogs.index')
            ->with('success', 'Catalog created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Catalog $catalog)
    {
        $catalog->load(['categories.products', 'relatedProduct']);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $catalog
            ]);
        }

        return view('pages.apps.catalog.catalogs.show', compact('catalog'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Catalog $catalog)
    {
        $products = Product::all();
        return view('pages.apps.catalog.catalogs.edit', compact('catalog', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Catalog $catalog)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:catalogs,slug,' . $catalog->id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'related_product_id' => 'nullable|exists:products,id',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($catalog->image) {
                Storage::disk('public')->delete($catalog->image);
            }
            $validated['image'] = $request->file('image')->store('catalogs', 'public');
        }

        $catalog->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Catalog updated successfully!',
                'data' => $catalog
            ]);
        }

        return redirect()
            ->route('catalog.catalogs.index')
            ->with('success', 'Catalog updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Catalog $catalog)
    {
        // Soft delete the catalog
        $catalog->delete();

        return response()->json([
            'success' => true,
            'message' => 'Catalog moved to trash successfully!'
        ]);
    }
}
