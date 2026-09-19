<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToolAnalytics extends Model
{
    protected $table = 'tool_analytics';

    protected $fillable = [
        'tool_id',
        'event_type',
        'status',
        'processing_time_ms',
        'input_size_bytes',
        'output_size_bytes',
        'user_ip_hash',
        'country',
        'device',
        'user_agent',
    ];

    public function tool()
    {
        return $this->belongsTo(Tool::class, 'tool_id');
    }
}
