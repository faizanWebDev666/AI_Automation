<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyImage extends Model
{
    protected $fillable = [
        'property_id', 'image_path', 'image_hash', 'is_live_photo',
        'is_watermarked', 'is_duplicate', 'is_from_internet', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_live_photo' => 'boolean',
            'is_watermarked' => 'boolean',
            'is_duplicate' => 'boolean',
            'is_from_internet' => 'boolean',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
