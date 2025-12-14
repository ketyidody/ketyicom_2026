<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'text',
        'description',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // When activating a setting, deactivate all others with the same key
        static::saving(function ($setting) {
            if ($setting->active && $setting->key) {
                static::where('key', $setting->key)
                    ->where('id', '!=', $setting->id)
                    ->update(['active' => false]);
            }
        });
    }

    /**
     * Get a setting by key
     */
    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)
            ->where('active', true)
            ->first();

        return $setting ? $setting->text : $default;
    }
}
