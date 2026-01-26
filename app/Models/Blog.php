<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Blog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'tags',
        'publish_date',
        'catalog_id',
        'meta_title',
        'meta_description',
        'keywords',
        'author_name',
        'author_position',
        'author_profile_image',
        'status',
        'allow_comments',
    ];

    protected $casts = [
        'publish_date' => 'date',
        'allow_comments' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($blog) {
            if (empty($blog->slug)) {
                $blog->slug = Str::slug($blog->title);
            }
            if (empty($blog->meta_title)) {
                $blog->meta_title = $blog->title;
            }
        });

        static::updating(function ($blog) {
            if ($blog->isDirty('title') && !$blog->isDirty('slug')) {
                $blog->slug = Str::slug($blog->title);
            }
        });
    }

    /**
     * Get the catalog that the blog belongs to.
     */
    public function catalog()
    {
        return $this->belongsTo(Catalog::class);
    }

    /**
     * Get tags as array
     */
    public function getTagsArrayAttribute()
    {
        return $this->tags ? explode(',', $this->tags) : [];
    }

    /**
     * Scope a query to only include published blogs.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('publish_date', '<=', now());
    }

    /**
     * Scope a query to only include draft blogs.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }
}
