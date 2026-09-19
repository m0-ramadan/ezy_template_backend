<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToolJob extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'tool_id',
        'status',
        'progress_percent',
        'status_message',
        'original_filename',
        'output_filename',
        'storage_path',
        'download_token',
        'expires_at',
        'error_message',
    ];

    protected $casts = [
        'progress_percent' => 'integer',
        'expires_at' => 'datetime',
    ];

    public function tool()
    {
        return $this->belongsTo(Tool::class, 'tool_id');
    }
}
