<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resource extends Model
{
    protected $fillable = [
        'category_id',
        'main_category_id',
        'subcategory_id',
        'title',
        'title_ar',
        'slug',
        'resource_type',
        'tech_stack',
        'tech_stack_ar',
        'description',
        'description_ar',
        'features',
        'features_ar',
        'short_description',
        'short_description_ar',
        'preview_image',
        'detail_image',
        'screenshots',
        'featured',
        'is_free',
        'price',
        'version',
        'license',
        'downloads_count',
        'rating',
        'reviews_count',
        'published_at',
        'status',
        'demo_url',
        'seo_title',
        'seo_description',
        'seo_title_ar',
        'seo_description_ar',
        'seo_keywords',
        'image_alt',
        'image_alt_ar',
        'canva_design_id',
        'canva_url',
        'source_url',
        'external_url',
        'gallery_status',
        'media_sync_status',
        'media_synced_at',
        'media_width',
        'media_height',
        'media_source_meta',
        'media_sync_message',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'is_free' => 'boolean',
        'price' => 'decimal:2',
        'rating' => 'decimal:1',
        'published_at' => 'datetime',
        'screenshots' => 'array',
        'features' => 'array',
        'features_ar' => 'array',
        'seo_keywords' => 'array',
        'media_synced_at' => 'datetime',
        'media_source_meta' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function mainCategory(): BelongsTo
    {
        return $this->belongsTo(MainCategory::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(ResourceFile::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(ResourceView::class);
    }

    public function scopePublished($q)
    {
        return $q->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}
