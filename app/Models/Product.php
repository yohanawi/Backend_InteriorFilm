<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'catalog_id',
        'category_id',
        'name',
        'slug',
        'thumbnail',
        'images',
        'price',
        'description',

        'sku',
        'stock_warehouse',
        'allow_backorders',
        'status',
        'published_at',

        'discount_type',
        'discount_value',
        'tax_class_id',
        'vat',

        'is_physical',
        'weight',
        'width',
        'height',
        'length',

        'variations',
        'tags',

        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_featured',
        'is_popular',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'images' => 'array',
        'price' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'vat' => 'decimal:2',
        'variations' => 'array',
        'tags' => 'array',
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
        'allow_backorders' => 'boolean',
        'is_physical' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && !$product->isDirty('slug')) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    /**
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the catalog that owns the product.
     */
    public function catalog()
    {
        return $this->belongsTo(Catalog::class);
    }

    /**
     * Get the product specification.
     */
    public function specification()
    {
        return $this->hasOne(ProductSpecification::class);
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured products.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include popular products.
     */
    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    /**
     * Get the first image URL.
     */
    public function getFirstImageAttribute()
    {
        return $this->images[0] ?? null;
    }

    /**
     * Return only gallery images that exist on disk.
     */
    public function getImagesAttribute($value)
    {
        $images = $value;

        if (is_string($images)) {
            $decoded = json_decode($images, true);
            $images = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($images)) return [];

        $validImages = array_filter($images, function ($path) {
            if (!is_string($path) || trim($path) === '') return false;
            $diskPath = static::normalizePublicDiskPath($path);
            if ($diskPath === null) return false;
            return static::publicDiskExists($diskPath);
        });

        // Convert to absolute paths for Next.js
        return array_values(array_map(function ($path) {
            if (!str_starts_with($path, '/') && !preg_match('#^https?://#i', $path)) {
                $diskPath = static::normalizePublicDiskPath($path);
                return '/storage/' . ltrim($diskPath, '/');
            }
            return $path;
        }, $validImages));
    }

    /**
     * Ensure only valid string paths are persisted for images.
     */
    public function setImagesAttribute($value): void
    {
        if ($value === null || $value === '') {
            $this->attributes['images'] = null;
            return;
        }

        $decoded = $value;
        if (is_string($decoded)) {
            $tmp = json_decode($decoded, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $decoded = $tmp;
            }
        }

        if (!is_array($decoded)) {
            $this->attributes['images'] = null;
            return;
        }

        $normalized = [];
        foreach ($decoded as $item) {
            if (!is_string($item)) {
                continue;
            }
            $t = trim($item);
            if ($t === '' || $t === '0') {
                continue;
            }
            $normalized[] = $t;
        }

        $normalized = array_values(array_unique($normalized));
        $this->attributes['images'] = $normalized ? json_encode($normalized) : null;
    }

    /**
     * Return the thumbnail only if it exists on disk.
     */
    public function getThumbnailAttribute($value)
    {
        if (!is_string($value) || trim($value) === '') return null;
        if (trim($value) === '0') return null;
        $diskPath = static::normalizePublicDiskPath($value);
        if ($diskPath === null) return null;

        // If it doesn't exist, return null
        if (!static::publicDiskExists($diskPath)) return null;

        // Return absolute URL path for Next.js Image component
        if (!str_starts_with($value, '/') && !preg_match('#^https?://#i', $value)) {
            return '/storage/' . ltrim($diskPath, '/');
        }

        return $value;
    }

    /**
     * Ensure only valid string paths are persisted for thumbnail.
     */
    public function setThumbnailAttribute($value): void
    {
        if ($value === null || $value === false) {
            $this->attributes['thumbnail'] = null;
            return;
        }

        if (is_int($value) || is_float($value)) {
            $this->attributes['thumbnail'] = null;
            return;
        }

        if (!is_string($value)) {
            $this->attributes['thumbnail'] = null;
            return;
        }

        $t = trim($value);
        $this->attributes['thumbnail'] = ($t === '' || $t === '0') ? null : $t;
    }

    private static function normalizePublicDiskPath(string $path): ?string
    {
        $trimmed = trim($path);
        if ($trimmed === '') return null;

        // If a full URL is stored, extract the path portion.
        if (preg_match('#^https?://#i', $trimmed)) {
            $parsed = parse_url($trimmed);
            if (!is_array($parsed) || empty($parsed['path'])) return null;
            $trimmed = $parsed['path'];
        }

        // Convert '/storage/...' public URL into disk-relative path.
        $trimmed = preg_replace('#^/+#', '', $trimmed);
        $trimmed = preg_replace('#^storage/#i', '', $trimmed);

        return $trimmed !== '' ? $trimmed : null;
    }

    private static function publicDiskExists(string $diskPath): bool
    {
        static $cache = [];
        if (array_key_exists($diskPath, $cache)) {
            return (bool) $cache[$diskPath];
        }

        $exists = Storage::disk('public')->exists($diskPath);
        $cache[$diskPath] = $exists;
        return $exists;
    }
}
