<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductIndexRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     *
     * @param ProductIndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(ProductIndexRequest $request): AnonymousResourceCollection
    {
        $validated = $request->validatedWithDefaults();

        $query = Product::query();

        // Apply filters
        $this->applyFilters($query, $validated);

        // Apply search
        if (!empty($validated['search'])) {
            $this->applySearch($query, $validated['search']);
        }

        // Load relationships
        if (!empty($validated['with_relations'])) {
            $query->with($validated['with_relations']);
        }

        // Apply sorting
        $this->applySorting($query, $validated['sort_by'], $validated['sort_order']);

        // Paginate results
        $products = $query->get();

        return ProductResource::collection($products);
    }

    /**
     * Display the specified product.
     *
     * @param string $identifier
     * @return ProductResource|JsonResponse
     */
    public function show(string $identifier)
    {
        // Try to find by ID first, then by slug
        $product = is_numeric($identifier)
            ? Product::find($identifier)
            : Product::where('slug', $identifier)->first();

        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
                'error' => 'NOT_FOUND'
            ], 404);
        }

        // Load all relationships for single product view
        $product->load(['category', 'catalog', 'specification']);

        return new ProductResource($product);
    }

    /**
     * Get featured products.
     *
     * @return AnonymousResourceCollection
     */
    public function featured()
    {
        $products = Product::featured()
            ->active()
            ->with(['category', 'catalog'])
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return ProductResource::collection($products);
    }

    /**
     * Get popular products.
     *
     * @return AnonymousResourceCollection
     */
    public function popular()
    {
        $products = Product::popular()
            ->active()
            ->with(['category', 'catalog'])
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return ProductResource::collection($products);
    }

    /**
     * Apply filters to the query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return void
     */
    private function applyFilters($query, array $filters): void
    {
        // Filter by category
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Filter by catalog
        if (!empty($filters['catalog_id'])) {
            $query->where('catalog_id', $filters['catalog_id']);
        }

        // Filter by active status
        if (isset($filters['is_active']) && $filters['is_active'] !== null) {
            $query->where('is_active', $filters['is_active']);
        }

        // Filter by featured status
        if (isset($filters['is_featured']) && $filters['is_featured'] !== null) {
            $query->where('is_featured', $filters['is_featured']);
        }

        // Filter by popular status
        if (isset($filters['is_popular']) && $filters['is_popular'] !== null) {
            $query->where('is_popular', $filters['is_popular']);
        }

        // Filter by price range
        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }
    }

    /**
     * Apply search to the query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return void
     */
    private function applySearch($query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('description', 'LIKE', "%{$search}%")
                ->orWhere('sku', 'LIKE', "%{$search}%")
                ->orWhere('tags', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Apply sorting to the query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $sortBy
     * @param string $sortOrder
     * @return void
     */
    private function applySorting($query, string $sortBy, string $sortOrder): void
    {
        switch ($sortBy) {
            case 'name':
                $query->orderBy('name', $sortOrder);
                break;
            case 'price':
                $query->orderBy('price', $sortOrder);
                break;
            case 'popularity':
                $query->orderBy('is_popular', 'desc')
                    ->orderBy('is_featured', 'desc')
                    ->orderBy('created_at', 'desc');
                break;
            case 'updated_at':
                $query->orderBy('updated_at', $sortOrder);
                break;
            case 'created_at':
            default:
                $query->orderBy('created_at', $sortOrder);
                break;
        }
    }
}
