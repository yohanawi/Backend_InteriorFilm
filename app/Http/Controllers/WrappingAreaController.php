<?php

namespace App\Http\Controllers;

use App\Models\WrappingArea;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WrappingAreaController extends Controller
{
    /**
     * Display a listing of the wrapping areas.
     */
    public function index()
    {
        $wrappingAreas = WrappingArea::ordered()->paginate(10);

        $product_count = [];
        foreach ($wrappingAreas as $wrappingArea) {
            $product_count[$wrappingArea->id] = $wrappingArea->products()->count();
        }

        // Add product_count to each item, but keep paginator object
        $wrappingAreas->getCollection()->transform(function ($item) use ($product_count) {
            $item->product_count = $product_count[$item->id] ?? 0;
            return $item;
        });

        return view('pages.apps.wrapping.index', compact('wrappingAreas'));
    }

    /**
     * Show the form for creating a new wrapping area.
     */
    public function create()
    {
        $products = Product::active()->orderBy('name')->get(['id', 'name', 'thumbnail', 'sku']);
        return view('pages.apps.wrapping.create', compact('products'));
    }

    /**
     * Store a newly created wrapping area in storage.
     */
    public function store(Request $request)
    {
        // Ensure keywords is an array or null
        if ($request->has('keywords') && !is_array($request->keywords)) {
            $request->merge(['keywords' => null]);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:wrapping_areas,slug',
            'meta_title' => 'required|string|max:255',
            'meta_description' => 'required|string',
            'keywords' => 'nullable|array',
            'keywords.*' => 'string',
            'main_heading' => 'required|string|max:255',
            'main_description' => 'required|string',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'why_partner_heading' => 'required|string|max:255',
            'why_partner_description' => 'required|string',
            'why_partner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'features' => 'nullable|array',
            'features.*.title' => 'nullable|string|max:255',
            'features.*.description' => 'nullable|string',
            'guide_heading' => 'required|string|max:255',
            'guide_description' => 'required|string',
            'guide' => 'nullable|array',
            'guide.*.heading' => 'nullable|string|max:255',
            'guide.*.subheading' => 'nullable|string|max:255',
            'guide.*.description' => 'nullable|string',
            'guide.*.image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'guide.*.features' => 'nullable|array',
            'guide.*.features.*.title' => 'nullable|string|max:255',
            'why_use_heading' => 'required|string|max:255',
            'why_use_description' => 'required|string',
            'hero_text' => 'required|string|max:255',
            'hero_subtext' => 'nullable|string',
            'hero_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'gallery_heading' => 'required|string|max:255',
            'gallery_description' => 'required|string',
            'photos' => 'nullable|array',
            'photos.*.file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'photos.*.alt' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Handle main image upload
        if ($request->hasFile('main_image')) {
            $validated['main_image'] = $this->uploadImage($request->file('main_image'), 'wrapping/main');
        }

        // Handle why partner image upload
        if ($request->hasFile('why_partner_image')) {
            $validated['why_partner_image'] = $this->uploadImage($request->file('why_partner_image'), 'wrapping/partner');
        }

        // Handle hero image upload
        if ($request->hasFile('hero_image')) {
            $validated['hero_image'] = $this->uploadImage($request->file('hero_image'), 'wrapping/hero');
        }

        // Handle guide images
        if ($request->has('guide')) {
            $guide = $validated['guide'];
            foreach ($guide as $index => $item) {
                if ($request->hasFile("guide.{$index}.image")) {
                    $guide[$index]['image'] = $this->uploadImage(
                        $request->file("guide.{$index}.image"),
                        'wrapping/guide'
                    );
                }
            }
            $validated['guide'] = $guide;
        }

        // Handle gallery photos
        if ($request->has('photos')) {
            $photos = $validated['photos'];
            foreach ($photos as $index => $photo) {
                if ($request->hasFile("photos.{$index}.file")) {
                    $photos[$index]['src'] = $this->uploadImage(
                        $request->file("photos.{$index}.file"),
                        'wrapping/gallery'
                    );
                }
            }
            $validated['photos'] = $photos;
        }

        $validated['is_active'] = $request->has('is_active');

        $wrappingArea = WrappingArea::create($validated);

        // Sync products with wrapping area
        if ($request->has('products')) {
            $products = collect($request->products)->mapWithKeys(function ($productId, $index) {
                return [$productId => ['sort_order' => $index]];
            });
            $wrappingArea->products()->sync($products);
        }

        return redirect()
            ->route('wrapping-areas.index')
            ->with('success', 'Wrapping area created successfully.');
    }

    /**
     * Display the specified wrapping area.
     */
    public function show(WrappingArea $wrappingArea)
    {
        return view('pages.apps.wrapping.show', compact('wrappingArea'));
    }

    /**
     * Show the form for editing the specified wrapping area.
     */
    public function edit(WrappingArea $wrappingArea)
    {
        $products = Product::active()->orderBy('name')->get(['id', 'name', 'thumbnail', 'sku']);
        $wrappingArea->load('products');
        return view('pages.apps.wrapping.edit', compact('wrappingArea', 'products'));
    }

    /**
     * Update the specified wrapping area in storage.
     */
    public function update(Request $request, WrappingArea $wrappingArea)
    {
        // Ensure keywords is an array or null
        if ($request->has('keywords') && !is_array($request->keywords)) {
            $request->merge(['keywords' => null]);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:wrapping_areas,slug,' . $wrappingArea->id,
            'meta_title' => 'required|string|max:255',
            'meta_description' => 'required|string',
            'keywords' => 'nullable|array',
            'keywords.*' => 'string',
            'main_heading' => 'required|string|max:255',
            'main_description' => 'required|string',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'why_partner_heading' => 'required|string|max:255',
            'why_partner_description' => 'required|string',
            'why_partner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'features' => 'nullable|array',
            'features.*.title' => 'nullable|string|max:255',
            'features.*.description' => 'nullable|string',
            'guide_heading' => 'required|string|max:255',
            'guide_description' => 'required|string',
            'guide' => 'nullable|array',
            'guide.*.existing_image' => 'nullable|string',
            'guide.*.image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'guide.*.heading' => 'nullable|string|max:255',
            'guide.*.subheading' => 'nullable|string|max:255',
            'guide.*.description' => 'nullable|string',
            'guide.*.features' => 'nullable|array',
            'guide.*.features.*.title' => 'nullable|string|max:255',
            'why_use_heading' => 'required|string|max:255',
            'why_use_description' => 'required|string',
            'hero_text' => 'required|string|max:255',
            'hero_subtext' => 'nullable|string',
            'hero_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'gallery_heading' => 'required|string|max:255',
            'gallery_description' => 'required|string',
            'photos' => 'nullable|array',
            'photos.*.existing_src' => 'nullable|string',
            'photos.*.file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'photos.*.alt' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id',
        ]);

        // Handle main image upload
        if ($request->hasFile('main_image')) {
            $this->deleteImage($wrappingArea->main_image);
            $validated['main_image'] = $this->uploadImage($request->file('main_image'), 'wrapping/main');
        }

        // Handle why partner image upload
        if ($request->hasFile('why_partner_image')) {
            $this->deleteImage($wrappingArea->why_partner_image);
            $validated['why_partner_image'] = $this->uploadImage($request->file('why_partner_image'), 'wrapping/partner');
        }

        // Handle hero image upload
        if ($request->hasFile('hero_image')) {
            $this->deleteImage($wrappingArea->hero_image);
            $validated['hero_image'] = $this->uploadImage($request->file('hero_image'), 'wrapping/hero');
        }

        // Handle guide images
        if ($request->has('guide')) {
            $guide = $validated['guide'];
            foreach ($guide as $index => $item) {
                // Preserve existing image if no new file uploaded
                if (isset($item['existing_image']) && !$request->hasFile("guide.{$index}.image")) {
                    $guide[$index]['image'] = $item['existing_image'];
                } elseif ($request->hasFile("guide.{$index}.image")) {
                    $guide[$index]['image'] = $this->uploadImage(
                        $request->file("guide.{$index}.image"),
                        'wrapping/guide'
                    );
                }
                // Remove existing_image from the array before saving
                unset($guide[$index]['existing_image']);
            }
            $validated['guide'] = $guide;
        }

        // Handle gallery photos
        if ($request->has('photos')) {
            $photos = $validated['photos'];
            foreach ($photos as $index => $photo) {
                // Preserve existing image if no new file uploaded
                if (isset($photo['existing_src']) && !$request->hasFile("photos.{$index}.file")) {
                    $photos[$index]['src'] = $photo['existing_src'];
                } elseif ($request->hasFile("photos.{$index}.file")) {
                    $photos[$index]['src'] = $this->uploadImage(
                        $request->file("photos.{$index}.file"),
                        'wrapping/gallery'
                    );
                }
                // Remove existing_src and file from the array before saving
                unset($photos[$index]['existing_src']);
                unset($photos[$index]['file']);
            }
            $validated['photos'] = $photos;
        }

        $validated['is_active'] = $request->has('is_active');

        $wrappingArea->update($validated);

        // Sync products with wrapping area
        if ($request->has('products')) {
            $products = collect($request->products)->mapWithKeys(function ($productId, $index) {
                return [$productId => ['sort_order' => $index]];
            });
            $wrappingArea->products()->sync($products);
        } else {
            $wrappingArea->products()->detach();
        }

        return redirect()
            ->route('wrapping-areas.index')
            ->with('success', 'Wrapping area updated successfully.');
    }

    /**
     * Remove the specified wrapping area from storage.
     */
    public function destroy(WrappingArea $wrappingArea)
    {
        // Delete associated images as before
        $this->deleteImage($wrappingArea->getRawOriginal('main_image'));
        $this->deleteImage($wrappingArea->getRawOriginal('why_partner_image'));
        $this->deleteImage($wrappingArea->getRawOriginal('hero_image'));

        $rawGuide = $wrappingArea->getRawOriginal('guide');
        if ($rawGuide) {
            $guideArray = is_string($rawGuide) ? json_decode($rawGuide, true) : $rawGuide;
            if (is_array($guideArray)) {
                foreach ($guideArray as $item) {
                    if (isset($item['image'])) {
                        $this->deleteImage($item['image']);
                    }
                }
            }
        }

        $rawPhotos = $wrappingArea->getRawOriginal('photos');
        if ($rawPhotos) {
            $photosArray = is_string($rawPhotos) ? json_decode($rawPhotos, true) : $rawPhotos;
            if (is_array($photosArray)) {
                foreach ($photosArray as $photo) {
                    if (isset($photo['src'])) {
                        $this->deleteImage($photo['src']);
                    }
                }
            }
        }

        $wrappingArea->delete();

        if (request()->ajax()) {
            // Send JSON for AJAX delete
            return response()->json(['success' => true]);
        }

        return redirect()
            ->route('wrapping-areas.index')
            ->with('success', 'Wrapping area deleted successfully.');
    }

    /**
     * Upload image to storage
     */
    private function uploadImage($file, $path)
    {
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        // Always store on the public disk so the generated URL is /storage/...
        // and does not depend on the default FILESYSTEM_DISK.
        $path = ltrim($path, '/');
        $file->storeAs($path, $filename, 'public');
        // Return path WITHOUT /storage/ prefix - let the model accessor handle it
        return $path . '/' . $filename;
    }

    /**
     * Delete image from storage
     */
    private function deleteImage($imagePath)
    {
        if (!$imagePath) {
            return;
        }

        // Handle both full URLs and storage paths
        if (Str::startsWith($imagePath, ['http://', 'https://'])) {
            // Extract path from URL
            $imagePath = parse_url($imagePath, PHP_URL_PATH);
            $imagePath = str_replace('/storage/', '', $imagePath);
        } else {
            // Remove /storage/ prefix if present
            $imagePath = str_replace('/storage/', '', ltrim($imagePath, '/'));
        }

        $imagePath = ltrim($imagePath, '/');

        if (Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }
    }

    /**
     * Toggle active status
     */
    public function toggleActive(WrappingArea $wrappingArea)
    {
        $wrappingArea->update(['is_active' => !$wrappingArea->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $wrappingArea->is_active
        ]);
    }
}
