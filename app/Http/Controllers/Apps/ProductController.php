<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductSpecification;
use App\Models\Category;
use App\Models\Catalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    private function normalizeTags($raw): array
    {
        if ($raw === null || $raw === '') {
            return [];
        }

        if (is_array($raw)) {
            return array_values(array_filter(array_map(fn($t) => trim((string) $t), $raw), fn($t) => $t !== ''));
        }

        $rawString = (string) $raw;

        // Tagify sometimes posts JSON string
        $trimmed = trim($rawString);
        if ($trimmed !== '' && str_starts_with($trimmed, '[')) {
            $decoded = json_decode($trimmed, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $names = [];
                foreach ($decoded as $item) {
                    if (is_array($item) && isset($item['value'])) {
                        $names[] = trim((string) $item['value']);
                    } elseif (is_string($item)) {
                        $names[] = trim($item);
                    }
                }
                return array_values(array_filter($names, fn($t) => $t !== ''));
            }
        }

        return array_values(array_filter(array_map('trim', explode(',', $rawString)), fn($t) => $t !== ''));
    }

    private function normalizeVariations(array $rawOptions): array
    {
        $variations = [];

        foreach ($rawOptions as $row) {
            if (!is_array($row)) {
                continue;
            }

            $type = isset($row['product_option']) ? trim((string) $row['product_option']) : '';
            $value = isset($row['product_option_value']) ? trim((string) $row['product_option_value']) : '';

            if ($type === '' || $value === '') {
                continue;
            }

            $variations[] = [
                'type' => $type,
                'value' => $value,
            ];
        }

        return $variations;
    }

    /** 
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Handle Ajax requests
        if ($request->ajax()) {
            $query = Product::with(['category.catalog', 'catalog']);

            // Search
            if ($request->has('search') && $request->search) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                        ->orWhere('sku', 'like', "%{$searchTerm}%")
                        ->orWhere('description', 'like', "%{$searchTerm}%");
                });
            }

            // Filter by category
            if ($request->has('category_id') && $request->category_id) {
                $query->where('category_id', $request->category_id);
            }

            // Filter by catalog
            if ($request->has('catalog_id') && $request->catalog_id) {
                $query->where(function ($q) use ($request) {
                    $q->where('catalog_id', $request->catalog_id)
                        ->orWhereHas('category', function ($qc) use ($request) {
                            $qc->where('catalog_id', $request->catalog_id);
                        });
                });
            }

            // Filter by status
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            // Sorting
            $sortField = $request->get('sort_field', 'sort_order');
            $sortDirection = $request->get('sort_direction', 'asc');
            $query->orderBy($sortField, $sortDirection);

            $products = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $products->items(),
                'pagination' => [
                    'total' => $products->total(),
                    'per_page' => $products->perPage(),
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem(),
                ],
            ]);
        }

        // Regular page load
        $query = Product::with(['category.catalog', 'catalog']);

        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('catalog_id') && $request->catalog_id) {
            $query->where(function ($q) use ($request) {
                $q->where('catalog_id', $request->catalog_id)
                    ->orWhereHas('category', function ($qc) use ($request) {
                        $qc->where('catalog_id', $request->catalog_id);
                    });
            });
        }

        $products = $query->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(10);

        $catalogs = Catalog::with('categories')->orderBy('name')->get();

        return view('pages.apps.catalog.products.index', compact('products', 'catalogs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $catalogs = Catalog::with('categories')->active()->orderBy('name')->get();
        $selectedCategoryId = $request->get('category_id');
        $selectedCatalogId = $request->get('catalog_id');

        return view('pages.apps.catalog.products.create', compact('catalogs', 'selectedCategoryId', 'selectedCatalogId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'catalog_id' => 'required|exists:catalogs,id',
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'images' => 'nullable|array|max:10',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'status' => 'required|in:published,draft,scheduled,inactive',
            'published_at' => 'nullable|date',

            'sku' => 'nullable|string|max:255',
            'stock_warehouse' => 'nullable|integer|min:0',
            'allow_backorders' => 'nullable|boolean',

            'discount_type' => 'required|in:none,percentage,fixed',
            // Percentage discount value (some older UIs posted this as `discount_percentage`)
            'discount_value' => 'nullable|numeric|min:0|max:100',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            // Fixed discounted price (stored into `discount_value` when discount_type=fixed)
            'discounted_price' => 'nullable|numeric|min:0',
            'tax_class_id' => 'nullable|integer',
            'vat' => 'nullable|numeric|min:0',

            'is_physical' => 'nullable|boolean',
            'weight' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',

            'kt_ecommerce_add_product_options' => 'nullable|array',
            'kt_ecommerce_add_product_options.*.product_option' => 'nullable|string|max:50',
            'kt_ecommerce_add_product_options.*.product_option_value' => 'nullable|string|max:255',

            'tags' => 'nullable',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:255',

            'is_featured' => 'nullable|boolean',
            'is_popular' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',

            // Specifications fields
            'certification_tags' => 'nullable|string',
            'features' => 'nullable|array',
            'features.*.name' => 'nullable|string|max:255',
            'features.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'features.*.existing_image' => 'nullable|string',
            'spec_dimensions' => 'nullable|string|max:255',
            'surface_finish' => 'nullable|string|max:255',
            'tensile_strength' => 'nullable|string|max:255',
            'application_temperature' => 'nullable|string|max:255',
            'elongation' => 'nullable|string|max:255',
            'service_temperature' => 'nullable|string|max:255',
            'storage' => 'nullable|string|max:255',
            'dimensional_stability' => 'nullable|string|max:255',
            'release_paper' => 'nullable|string|max:255',
            'adhesive' => 'nullable|string|max:255',
            'adhesive_strength' => 'nullable|string|max:255',
            'shelf_life' => 'nullable|string|max:255',
            'warranty' => 'nullable|string|max:255',
        ]);

        // Validation checks
        if (($validated['status'] ?? null) === 'scheduled' && empty($validated['published_at'])) {
            return back()->withErrors(['published_at' => 'Publishing date/time is required for scheduled products.'])->withInput();
        }

        $discountType = $validated['discount_type'] ?? 'none';
        $discountPercentage = $request->input('discount_value', $request->input('discount_percentage'));
        $discountedPrice = $request->input('discounted_price');

        if ($discountType === 'percentage' && ($discountPercentage === null || $discountPercentage === '')) {
            return back()->withErrors(['discount_value' => 'Discount percentage is required.'])->withInput();
        }

        if ($discountType === 'fixed' && ($discountedPrice === null || $discountedPrice === '')) {
            return back()->withErrors(['discounted_price' => 'Discounted price is required.'])->withInput();
        }

        $categoryValid = Category::where('id', $validated['category_id'])
            ->where('catalog_id', $validated['catalog_id'])
            ->exists();

        if (!$categoryValid) {
            return back()->withErrors(['category_id' => 'Selected category does not belong to the selected catalog.'])->withInput();
        }

        if (!isset($validated['slug']) || empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Check for unique slug within category
        $exists = Product::where('category_id', $validated['category_id'])
            ->where('slug', $validated['slug'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['slug' => 'This slug already exists in this category.'])->withInput();
        }

        try {
            DB::beginTransaction();

            // Prepare product data (excluding specification fields)
            $productData = [
                'catalog_id' => $validated['catalog_id'],
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                // Ensure we don't accidentally persist DB defaults like 0/false when nothing is uploaded
                'thumbnail' => null,
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'status' => $validated['status'],
                'published_at' => $validated['published_at'] ?? null,
                'sku' => $validated['sku'] ?? null,
                'stock_warehouse' => $validated['stock_warehouse'] ?? 0,
                'allow_backorders' => $request->boolean('allow_backorders'),
                'discount_type' => $validated['discount_type'],
                'vat' => $validated['vat'] ?? null,
                'is_physical' => $request->boolean('is_physical'),
                'is_featured' => $request->boolean('is_featured'),
                'is_popular' => $request->boolean('is_popular'),
                'is_active' => $validated['status'] !== 'inactive',
                'sort_order' => $validated['sort_order'] ?? 0,
                'meta_title' => $validated['meta_title'] ?? $validated['name'],
                'meta_description' => $validated['meta_description'] ?? null,
                'meta_keywords' => $validated['meta_keywords'] ?? null,
            ];

            // Process tags and variations
            $productData['tags'] = $this->normalizeTags($request->input('tags'));
            $productData['variations'] = $this->normalizeVariations($request->input('kt_ecommerce_add_product_options', []));

            // Discount normalization
            if ($discountType === 'percentage') {
                $productData['discount_value'] = $discountPercentage;
            } elseif ($discountType === 'fixed') {
                $productData['discount_value'] = $discountedPrice;
            } else {
                $productData['discount_value'] = null;
            }

            // Shipping normalization
            if (!$productData['is_physical']) {
                $productData['weight'] = null;
                $productData['width'] = null;
                $productData['height'] = null;
                $productData['length'] = null;
            } else {
                $productData['weight'] = $validated['weight'] ?? null;
                $productData['width'] = $validated['width'] ?? null;
                $productData['height'] = $validated['height'] ?? null;
                $productData['length'] = $validated['length'] ?? null;
            }

            // Handle thumbnail upload (accept legacy field name `thumbnail` as well)
            $avatarFile = $request->file('avatar') ?? $request->file('thumbnail');
            if ($avatarFile && $avatarFile->isValid()) {
                $thumbPath = $avatarFile->store('products/thumbnails', 'public');
                if (is_string($thumbPath) && trim($thumbPath) !== '') {
                    $productData['thumbnail'] = $thumbPath;
                } else {
                    Log::warning('Thumbnail upload failed (store returned non-string).', [
                        'disk' => 'public',
                        'product_name' => $validated['name'] ?? null,
                        'original_name' => $avatarFile->getClientOriginalName() ?? null,
                    ]);
                }
            }

            // Handle gallery images upload
            $images = [];
            $uploadedImages = $request->file('images') ?? $request->file('media');
            if ($uploadedImages) {
                foreach ((array) $uploadedImages as $image) {
                    if (!$image || !$image->isValid()) {
                        continue;
                    }
                    $imgPath = $image->store('products', 'public');
                    if (is_string($imgPath) && trim($imgPath) !== '') {
                        $images[] = $imgPath;
                    } else {
                        Log::warning('Gallery image upload failed (store returned non-string).', [
                            'disk' => 'public',
                            'product_name' => $validated['name'] ?? null,
                            'original_name' => $image->getClientOriginalName() ?? null,
                        ]);
                    }
                }
            }
            $images = array_values(array_slice($images, 0, 10));
            $productData['images'] = $images ?: null;

            // Create the product
            $product = Product::create($productData);

            // Prepare specification data
            $specificationData = [
                'product_id' => $product->id,
                'spec_dimensions' => $validated['spec_dimensions'] ?? null,
                'surface_finish' => $validated['surface_finish'] ?? null,
                'tensile_strength' => $validated['tensile_strength'] ?? null,
                'application_temperature' => $validated['application_temperature'] ?? null,
                'elongation' => $validated['elongation'] ?? null,
                'service_temperature' => $validated['service_temperature'] ?? null,
                'storage' => $validated['storage'] ?? null,
                'dimensional_stability' => $validated['dimensional_stability'] ?? null,
                'release_paper' => $validated['release_paper'] ?? null,
                'adhesive' => $validated['adhesive'] ?? null,
                'adhesive_strength' => $validated['adhesive_strength'] ?? null,
                'shelf_life' => $validated['shelf_life'] ?? null,
                'warranty' => $validated['warranty'] ?? null,
            ];

            // Process certifications (tags)
            $certifications = [];
            if ($request->filled('certification_tags')) {
                $certifications = array_filter(array_map('trim', explode(',', $request->input('certification_tags'))));
            }
            $specificationData['certifications'] = $certifications;

            // Process product features with images
            $features = [];
            if ($request->has('features') && is_array($request->input('features'))) {
                foreach ($request->input('features') as $index => $feature) {
                    if (!empty($feature['name'])) {
                        $featureData = [
                            'name' => $feature['name'],
                            'image' => $feature['existing_image'] ?? null,
                        ];

                        // Handle feature image upload
                        if ($request->hasFile("features.{$index}.image")) {
                            if (!empty($featureData['image'])) {
                                Storage::disk('public')->delete($featureData['image']);
                            }
                            $featureData['image'] = $request->file("features.{$index}.image")->store('products/features', 'public');
                        }

                        $features[] = $featureData;
                    }
                }
            }
            $specificationData['features'] = $features;

            // Create specification record
            ProductSpecification::create($specificationData);

            DB::commit();

            return redirect()
                ->route('catalog.products.index', ['category_id' => $product->category_id])
                ->with('success', 'Product created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product creation failed: ' . $e->getMessage());

            return back()
                ->withErrors(['error' => 'Failed to create product. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load(['category.catalog', 'specification']);
        return view('pages.apps.catalog.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $catalogs = Catalog::with('categories')->active()->orderBy('name')->get();
        $product->load('specification');
        return view('pages.apps.catalog.products.edit', compact('product', 'catalogs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'catalog_id' => 'required|exists:catalogs,id',
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'avatar_remove' => 'nullable|boolean',
            'images' => 'nullable|array|max:10',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'existing_images' => 'nullable|array',
            'existing_images.*' => 'nullable|string',
            'remove_images' => 'nullable|array',
            'remove_images.*' => 'nullable|string',
            'status' => 'required|in:published,draft,scheduled,inactive',
            'published_at' => 'nullable|date',

            'sku' => 'nullable|string|max:255',
            'stock_warehouse' => 'nullable|integer|min:0',
            'allow_backorders' => 'nullable|boolean',

            'discount_type' => 'required|in:none,percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0|max:100',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discounted_price' => 'nullable|numeric|min:0',
            'tax_class_id' => 'nullable|integer',
            'vat' => 'nullable|numeric|min:0',

            'is_physical' => 'nullable|boolean',
            'weight' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',

            'kt_ecommerce_add_product_options' => 'nullable|array',
            'kt_ecommerce_add_product_options.*.product_option' => 'nullable|string|max:50',
            'kt_ecommerce_add_product_options.*.product_option_value' => 'nullable|string|max:255',

            'tags' => 'nullable',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:255',

            'is_featured' => 'nullable|boolean',
            'is_popular' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',

            // Specifications fields
            'certification_tags' => 'nullable|string',
            'features' => 'nullable|array',
            'features.*.name' => 'nullable|string|max:255',
            'features.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'features.*.existing_image' => 'nullable|string',
            'spec_dimensions' => 'nullable|string|max:255',
            'surface_finish' => 'nullable|string|max:255',
            'tensile_strength' => 'nullable|string|max:255',
            'application_temperature' => 'nullable|string|max:255',
            'elongation' => 'nullable|string|max:255',
            'service_temperature' => 'nullable|string|max:255',
            'storage' => 'nullable|string|max:255',
            'dimensional_stability' => 'nullable|string|max:255',
            'release_paper' => 'nullable|string|max:255',
            'adhesive' => 'nullable|string|max:255',
            'adhesive_strength' => 'nullable|string|max:255',
            'shelf_life' => 'nullable|string|max:255',
            'warranty' => 'nullable|string|max:255',
        ]);

        if (($validated['status'] ?? null) === 'scheduled' && empty($validated['published_at'])) {
            return back()->withErrors(['published_at' => 'Publishing date/time is required for scheduled products.'])->withInput();
        }

        $discountType = $validated['discount_type'] ?? 'none';
        $discountPercentage = $request->input('discount_value', $request->input('discount_percentage'));
        $discountedPrice = $request->input('discounted_price');

        if ($discountType === 'percentage' && ($discountPercentage === null || $discountPercentage === '')) {
            return back()->withErrors(['discount_value' => 'Discount percentage is required.'])->withInput();
        }

        if ($discountType === 'fixed' && ($discountedPrice === null || $discountedPrice === '')) {
            return back()->withErrors(['discounted_price' => 'Discounted price is required.'])->withInput();
        }

        $categoryValid = Category::where('id', $validated['category_id'])
            ->where('catalog_id', $validated['catalog_id'])
            ->exists();

        if (!$categoryValid) {
            return back()->withErrors(['category_id' => 'Selected category does not belong to the selected catalog.'])->withInput();
        }

        if (!isset($validated['slug']) || empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Check for unique slug within category
        $exists = Product::where('category_id', $validated['category_id'])
            ->where('slug', $validated['slug'])
            ->where('id', '!=', $product->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['slug' => 'This slug already exists in this category.'])->withInput();
        }

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_popular'] = $request->boolean('is_popular');
        $validated['allow_backorders'] = $request->boolean('allow_backorders');
        $validated['is_physical'] = $request->boolean('is_physical');
        $validated['is_active'] = $validated['status'] !== 'inactive';

        if (empty($validated['meta_title'])) {
            $validated['meta_title'] = $validated['name'];
        }

        $validated['tags'] = $this->normalizeTags($request->input('tags'));
        $validated['variations'] = $this->normalizeVariations($request->input('kt_ecommerce_add_product_options', []));

        // Discount normalization
        if ($discountType === 'percentage') {
            $validated['discount_value'] = $discountPercentage;
        } elseif ($discountType === 'fixed') {
            $validated['discount_value'] = $discountedPrice;
        } else {
            $validated['discount_value'] = null;
        }

        // Shipping normalization
        if (!$validated['is_physical']) {
            $validated['weight'] = null;
            $validated['width'] = null;
            $validated['height'] = null;
            $validated['length'] = null;
        }

        // Thumbnail remove/replace
        if ($request->boolean('avatar_remove') && $product->thumbnail) {
            Storage::disk('public')->delete($product->thumbnail);
            $validated['thumbnail'] = null;
        }

        $avatarFile = $request->file('avatar') ?? $request->file('thumbnail');
        if ($avatarFile && $avatarFile->isValid()) {
            if ($product->thumbnail) {
                Storage::disk('public')->delete($product->thumbnail);
            }
            $thumbPath = $avatarFile->store('products/thumbnails', 'public');
            if (is_string($thumbPath) && trim($thumbPath) !== '') {
                $validated['thumbnail'] = $thumbPath;
            } else {
                Log::warning('Thumbnail upload failed on update (store returned non-string).', [
                    'disk' => 'public',
                    'product_id' => $product->id,
                    'original_name' => $avatarFile->getClientOriginalName() ?? null,
                ]);
            }
        }

        // Handle Gallery Images - Support for existing_images[], remove_images[], and new images[]
        $existingImages = $request->input('existing_images', []);
        $removeImages = $request->input('remove_images', []);
        $uploadedImages = $request->file('images');

        // Start with existing images that weren't marked for removal
        $finalImages = [];
        if (is_array($existingImages)) {
            foreach ($existingImages as $existingImage) {
                // Only keep if not in remove list
                if (!in_array($existingImage, (array) $removeImages)) {
                    $finalImages[] = $existingImage;
                }
            }
        }

        // Delete files marked for removal from storage
        if (is_array($removeImages)) {
            foreach ($removeImages as $removeImage) {
                if (!empty($removeImage) && Storage::disk('public')->exists($removeImage)) {
                    Storage::disk('public')->delete($removeImage);
                    Log::info('Deleted product image from storage', [
                        'product_id' => $product->id,
                        'image_path' => $removeImage
                    ]);
                }
            }
        }

        // Upload and add new images
        if ($uploadedImages && is_array($uploadedImages)) {
            foreach ($uploadedImages as $image) {
                if (!$image || !$image->isValid()) {
                    continue;
                }
                $imgPath = $image->store('products', 'public');
                if (is_string($imgPath) && trim($imgPath) !== '') {
                    $finalImages[] = $imgPath;
                    Log::info('Uploaded new product image', [
                        'product_id' => $product->id,
                        'image_path' => $imgPath
                    ]);
                } else {
                    Log::warning('Gallery image upload failed on update (store returned non-string).', [
                        'disk' => 'public',
                        'product_id' => $product->id,
                        'original_name' => $image->getClientOriginalName() ?? null,
                    ]);
                }
            }
        }

        // Limit to 10 images max and update
        $validated['images'] = array_values(array_slice($finalImages, 0, 10));

        // Process certifications (tags)
        $certifications = [];
        if ($request->filled('certification_tags')) {
            $certifications = array_filter(array_map('trim', explode(',', $request->input('certification_tags'))));
        }
        $validated['certifications'] = $certifications;

        try {
            DB::beginTransaction();

            $product->loadMissing('specification');
            $previousFeatureImages = [];
            if ($product->specification && is_array($product->specification->features ?? null)) {
                foreach ($product->specification->features as $f) {
                    if (is_array($f) && !empty($f['image']) && is_string($f['image'])) {
                        $previousFeatureImages[] = $f['image'];
                    }
                }
            }

            // Prepare product data (excluding specification fields)
            $productData = [
                'catalog_id' => $validated['catalog_id'],
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'status' => $validated['status'],
                'published_at' => $validated['published_at'] ?? null,
                'sku' => $validated['sku'] ?? null,
                'stock_warehouse' => $validated['stock_warehouse'] ?? 0,
                'allow_backorders' => $request->boolean('allow_backorders'),
                'discount_type' => $validated['discount_type'],
                'discount_value' => $validated['discount_value'],
                'vat' => $validated['vat'] ?? null,
                'is_physical' => $request->boolean('is_physical'),
                'is_featured' => $request->boolean('is_featured'),
                'is_popular' => $request->boolean('is_popular'),
                'is_active' => $validated['status'] !== 'inactive',
                'sort_order' => $validated['sort_order'] ?? 0,
                'meta_title' => $validated['meta_title'],
                'meta_description' => $validated['meta_description'] ?? null,
                'meta_keywords' => $validated['meta_keywords'] ?? null,
                'tags' => $validated['tags'],
                'variations' => $validated['variations'],
            ];

            // Handle shipping dimensions
            if (!$productData['is_physical']) {
                $productData['weight'] = null;
                $productData['width'] = null;
                $productData['height'] = null;
                $productData['length'] = null;
            } else {
                $productData['weight'] = $validated['weight'] ?? null;
                $productData['width'] = $validated['width'] ?? null;
                $productData['height'] = $validated['height'] ?? null;
                $productData['length'] = $validated['length'] ?? null;
            }

            // Handle thumbnail
            if (isset($validated['thumbnail'])) {
                $productData['thumbnail'] = $validated['thumbnail'];
            }

            // Handle gallery images
            if (isset($validated['images'])) {
                $productData['images'] = $validated['images'];
            }

            // Update the product
            $product->update($productData);

            // Prepare specification data
            $specificationData = [
                'spec_dimensions' => $validated['spec_dimensions'] ?? null,
                'surface_finish' => $validated['surface_finish'] ?? null,
                'tensile_strength' => $validated['tensile_strength'] ?? null,
                'application_temperature' => $validated['application_temperature'] ?? null,
                'elongation' => $validated['elongation'] ?? null,
                'service_temperature' => $validated['service_temperature'] ?? null,
                'storage' => $validated['storage'] ?? null,
                'dimensional_stability' => $validated['dimensional_stability'] ?? null,
                'release_paper' => $validated['release_paper'] ?? null,
                'adhesive' => $validated['adhesive'] ?? null,
                'adhesive_strength' => $validated['adhesive_strength'] ?? null,
                'shelf_life' => $validated['shelf_life'] ?? null,
                'warranty' => $validated['warranty'] ?? null,
            ];

            // Process certifications (tags)
            $certifications = [];
            if ($request->filled('certification_tags')) {
                $certifications = array_filter(array_map('trim', explode(',', $request->input('certification_tags'))));
            }
            $specificationData['certifications'] = $certifications;

            // Process product features with images
            $features = [];
            if ($request->has('features') && is_array($request->input('features'))) {
                foreach ($request->input('features') as $index => $feature) {
                    if (!is_array($feature)) {
                        continue;
                    }

                    $name = isset($feature['name']) ? trim((string) $feature['name']) : '';
                    if ($name === '') {
                        continue;
                    }

                    $existingImage = isset($feature['existing_image']) ? trim((string) $feature['existing_image']) : '';
                    $existingImage = $existingImage !== '' ? $existingImage : null;

                    $featureData = [
                        'name' => $name,
                        'image' => $existingImage,
                    ];

                    // Handle feature image upload (replace)
                    if ($request->hasFile("features.{$index}.image")) {
                        if (!empty($existingImage)) {
                            Storage::disk('public')->delete($existingImage);
                        }
                        $featureData['image'] = $request->file("features.{$index}.image")->store('products/features', 'public');
                    }

                    $features[] = $featureData;
                }
            }
            $specificationData['features'] = $features;

            // Update or create specification record
            $product->specification()->updateOrCreate(
                ['product_id' => $product->id],
                $specificationData
            );

            // Delete removed feature images (ones that existed before but are not kept now)
            $keptFeatureImages = [];
            foreach ($features as $f) {
                if (is_array($f) && !empty($f['image']) && is_string($f['image'])) {
                    $keptFeatureImages[] = $f['image'];
                }
            }
            $keptFeatureImages = array_values(array_unique($keptFeatureImages));

            $removedFeatureImages = array_diff(array_values(array_unique($previousFeatureImages)), $keptFeatureImages);
            foreach ($removedFeatureImages as $path) {
                Storage::disk('public')->delete($path);
            }

            DB::commit();

            return redirect()
                ->route('catalog.products.index', ['category_id' => $product->category_id])
                ->with('success', 'Product updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product update failed: ' . $e->getMessage());

            return back()
                ->withErrors(['error' => 'Failed to update product. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            DB::beginTransaction();

            // Delete product images
            if ($product->images) {
                foreach ($product->images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            // Delete thumbnail
            if ($product->thumbnail) {
                Storage::disk('public')->delete($product->thumbnail);
            }

            // Delete specification feature images
            if ($product->specification && $product->specification->features) {
                foreach ($product->specification->features as $feature) {
                    if (isset($feature['image']) && $feature['image']) {
                        Storage::disk('public')->delete($feature['image']);
                    }
                }
            }

            // Delete product (cascade will delete specification)
            $product->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product deletion failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product. Please try again.'
            ], 500);
        }
    }
}
