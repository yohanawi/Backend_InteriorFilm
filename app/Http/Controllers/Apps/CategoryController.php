<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Catalog;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::with('catalog')->withCount('products')
            ->whereNull('deleted_at');

        if ($request->has('catalog_id') && $request->catalog_id) {
            $query->where('catalog_id', $request->catalog_id);
        }

        $categories = $query->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(10);

        $catalogs = Catalog::whereNull('deleted_at')->orderBy('name')->get();

        return view('pages.apps.catalog.categories.index', compact('categories', 'catalogs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $catalogs = Catalog::active()->orderBy('name')->get();
        $selectedCatalogId = $request->get('catalog_id');
        $products = Product::all();

        return view('pages.apps.catalog.categories.create', compact('catalogs', 'selectedCatalogId', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'catalog_id' => 'required|exists:catalogs,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
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

        // Check for unique slug within catalog
        $exists = Category::where('catalog_id', $validated['catalog_id'])
            ->where('slug', $validated['slug'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['slug' => 'This slug already exists in this catalog.'])->withInput();
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $category = Category::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Category created successfully!',
                'data' => $category
            ]);
        }

        return redirect()
            ->route('catalog.categories.index', ['catalog_id' => $category->catalog_id])
            ->with('success', 'Category created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $category->load(['catalog', 'products', 'relatedProduct']);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $category
            ]);
        }

        return view('pages.apps.catalog.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $catalogs = Catalog::active()->orderBy('name')->get();
        $products = Product::all();
        return view('pages.apps.catalog.categories.edit', compact('category', 'catalogs', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'catalog_id' => 'required|exists:catalogs,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
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

        // Check for unique slug within catalog
        $exists = Category::where('catalog_id', $validated['catalog_id'])
            ->where('slug', $validated['slug'])
            ->where('id', '!=', $category->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['slug' => 'This slug already exists in this catalog.'])->withInput();
        }

        if ($request->hasFile('image')) {
            // Delete old image
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully!',
                'data' => $category
            ]);
        }

        return redirect()
            ->route('catalog.categories.index', ['catalog_id' => $category->catalog_id])
            ->with('success', 'Category updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Soft delete the category
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category moved to trash successfully!'
        ]);
    }
}
