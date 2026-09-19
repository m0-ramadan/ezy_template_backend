<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tool extends Model
{
    protected $fillable = [
        'category_id',
        'slug',
        'name',
        'name_ar',
        'description',
        'description_ar',
        'short_description',
        'short_description_ar',
        'icon',
        'component_key',
        'tool_class',
        'required_dependency',
        'is_active',
        'is_featured',
        'is_popular',
        'is_new',
        'usage_count',
        'seo_title',
        'seo_title_ar',
        'seo_description',
        'seo_description_ar',
        'keywords',
        'features',
        'usage_steps',
        'faqs',
        'related_resource_categories',
        'sort_order',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_popular' => 'boolean',
        'is_new' => 'boolean',
        'usage_count' => 'integer',
        'sort_order' => 'integer',
        'keywords' => 'array',
        'features' => 'array',
        'usage_steps' => 'array',
        'faqs' => 'array',
        'related_resource_categories' => 'array',
        'settings' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(ToolCategory::class, 'category_id');
    }

    public function analytics()
    {
        return $this->hasMany(ToolAnalytics::class, 'tool_id');
    }
}
