<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'content_blocks',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'og_type',
        'twitter_card',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'canonical_url',
        'structured_data',
        'status',
        'published_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'published_at' => 'datetime',
        'structured_data' => 'array',
        'content_blocks' => 'array',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug from title if not provided
        static::creating(function ($page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->title);
            }
        });

        // Ensure slug is unique
        static::saving(function ($page) {
            $originalSlug = $page->slug;
            $count = 1;

            // Check if slug exists (excluding current record if updating)
            while (static::where('slug', $page->slug)
                ->where('id', '!=', $page->id ?? 0)
                ->exists()
            ) {
                $page->slug = $originalSlug . '-' . $count++;
            }
        });
    }

    /**
     * Scope a query to only include published pages.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    /**
     * Scope a query to only include draft pages.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    /**
     * Check if the page is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published' &&
            ($this->published_at === null || $this->published_at <= now());
    }

    /**
     * Get meta title or fallback to title.
     */
    public function getMetaTitleAttribute($value)
    {
        return $value ?: $this->title;
    }

    /**
     * Get OG title or fallback to meta title or title.
     */
    public function getOgTitleAttribute($value)
    {
        return $value ?: ($this->meta_title ?: $this->title);
    }

    /**
     * Get Twitter title or fallback to OG title or meta title or title.
     */
    public function getTwitterTitleAttribute($value)
    {
        return $value ?: ($this->og_title ?: ($this->meta_title ?: $this->title));
    }
}
