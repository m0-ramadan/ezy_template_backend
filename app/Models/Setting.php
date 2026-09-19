<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        if (!$setting) {
            return $default;
        }

        if ($setting->type === 'json' || is_array($default)) {
            $decoded = json_decode($setting->value, true);
            return json_last_error() === JSON_ERROR_NONE ? $decoded : $setting->value;
        }

        if ($setting->type === 'boolean') {
            return filter_var($setting->value, FILTER_VALIDATE_BOOLEAN);
        }

        if ($setting->type === 'integer') {
            return (int) $setting->value;
        }

        return $setting->value;
    }

    public static function set(string $key, mixed $value, string $type = 'string'): static
    {
        $valToStore = is_array($value) || is_object($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : (string) $value;
        if (is_array($value) || is_object($value)) {
            $type = 'json';
        }

        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $valToStore, 'type' => $type]
        );
    }
}
