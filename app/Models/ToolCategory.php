<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToolCategory extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'name_ar',
        'description',
        'description_ar',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function tools()
    {
        return $this->hasMany(Tool::class, 'category_id')->orderBy('sort_order');
    }
}
