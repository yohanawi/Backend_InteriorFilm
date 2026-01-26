<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class WrappingArea extends Model
{
    use HasFactory, SoftDeletes;

    private function toPublicStorageUrl(?string $value): ?string
    {
        if (!$value) {
            return null;
        } 

        // If already a full URL, return as is
        if (Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        // Normalize common stored variants
        $value = ltrim($value, '/');
        if (Str::startsWith($value, 'storage/')) {
            $value = substr($value, 8);
        }

        // Use the current request host when available (fixes misconfigured APP_URL).
        // Prefer HTTPS in non-local environments to avoid mixed-content blocking.
        return app()->environment('local')
            ? url('/storage/' . $value)
            : secure_url('/storage/' . $value);
    }

    protected $fillable = [
        'slug',
        'title',
        'meta_title',
        'meta_description',
        'keywords',
        'main_heading',
        'main_description',
        'main_image',
        'why_partner_heading',
        'why_partner_description',
        'why_partner_image',
        'features',
        'guide_heading',
        'guide_description',
        'guide',
        'why_use_heading',
        'why_use_description',
        'hero_text',
        'hero_subtext',
        'hero_image',
        'gallery_heading',
        'gallery_description',
        'photos',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'keywords' => 'array',
        'features' => 'array',
        'guide' => 'array',
        'photos' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($wrappingArea) {
            if (empty($wrappingArea->slug)) {
                $wrappingArea->slug = Str::slug($wrappingArea->title);
            }
        });

        static::updating(function ($wrappingArea) {
            if ($wrappingArea->isDirty('title') && empty($wrappingArea->slug)) {
                $wrappingArea->slug = Str::slug($wrappingArea->title);
            }
        });
    }

    /**
     * Scope a query to only include active wrapping areas.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc');
    }

    /**
     * Get wrapping area by slug
     */
    public static function findBySlug($slug)
    {
        return static::where('slug', $slug)->firstOrFail();
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the products associated with the wrapping area
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'wrapping_area_product')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderBy('wrapping_area_product.sort_order', 'asc');
    }

    /**
     * Get the main image with absolute path
     */
    public function getMainImageAttribute($value)
    {
        if (!$value) {
            return null;
        }

        // Support older values that may include /storage/ prefix
        $value = Str::startsWith($value, '/storage/') ? substr($value, 9) : $value;
        return $this->toPublicStorageUrl($value);
    }

    /**
     * Get the why partner image with absolute path
     */
    public function getWhyPartnerImageAttribute($value)
    {
        if (!$value) {
            return null;
        }

        $value = Str::startsWith($value, '/storage/') ? substr($value, 9) : $value;
        return $this->toPublicStorageUrl($value);
    }

    /**
     * Get the hero image with absolute path
     */
    public function getHeroImageAttribute($value)
    {
        if (!$value) {
            return null;
        }

        $value = Str::startsWith($value, '/storage/') ? substr($value, 9) : $value;
        return $this->toPublicStorageUrl($value);
    }

    /**
     * Get the guide array with absolute image paths
     */
    public function getGuideAttribute($value)
    {
        if (!$value) {
            return [];
        }

        $guide = is_string($value) ? json_decode($value, true) : $value;

        if (!is_array($guide)) {
            return [];
        }

        return array_map(function ($item) {
            if (isset($item['image']) && $item['image']) {
                $image = Str::startsWith($item['image'], '/storage/') ? substr($item['image'], 9) : $item['image'];
                $item['image'] = $this->toPublicStorageUrl($image);
            }
            return $item;
        }, $guide);
    }

    /**
     * Get the photos array with absolute image paths
     */
    public function getPhotosAttribute($value)
    {
        if (!$value) {
            return [];
        }

        $photos = is_string($value) ? json_decode($value, true) : $value;

        if (!is_array($photos)) {
            return [];
        }

        return array_map(function ($photo) {
            if (isset($photo['src']) && $photo['src']) {
                $src = Str::startsWith($photo['src'], '/storage/') ? substr($photo['src'], 9) : $photo['src'];
                $photo['src'] = $this->toPublicStorageUrl($src);
            }
            return $photo;
        }, $photos);
    }
}
