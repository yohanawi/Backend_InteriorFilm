<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Catalog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'image',
        'description',
        'is_active',
        'sort_order',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'related_product_id',
    ];
    /**
     * Get the related product for the catalog.
     */
    public function relatedProduct()
    {
        return $this->belongsTo(Product::class, 'related_product_id');
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($catalog) {
            if (empty($catalog->slug)) {
                $catalog->slug = Str::slug($catalog->name);
            }
        });

        static::updating(function ($catalog) {
            if ($catalog->isDirty('name') && !$catalog->isDirty('slug')) {
                $catalog->slug = Str::slug($catalog->name);
            }
        });
    }

    /**
     * Get the categories for the catalog.
     */
    public function categories()
    {
        return $this->hasMany(Category::class)->orderBy('sort_order');
    }

    /**
     * Get all products through categories.
     */
    public function products()
    {
        return $this->hasManyThrough(Product::class, Category::class);
    }

    /**
     * Scope a query to only include active catalogs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
