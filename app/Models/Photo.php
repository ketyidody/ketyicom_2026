<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Photo extends Model
{
    protected $fillable = [
        'album_id',
        'title',
        'description',
        'image_path',
        'thumbnail_path',
        'medium_path',
        'width',
        'height',
        'file_size',
        'display_order',
        'is_featured',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
    ];

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
