<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    protected $fillable = [
        'user_id', 'title', 'property_type', 'listing_type', 'ownership_type',
        'price', 'area_marla', 'bedrooms', 'bathrooms', 'kitchens', 'floors',
        'furnished', 'description', 'city', 'area_name', 'full_address',
        'latitude', 'longitude', 'electricity_bill', 'ownership_proof',
        'contact_phone', 'status', 'admin_notes', 'flags', 'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'area_marla' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'flags' => 'array',
            'approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderBy('sort_order');
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isFlagged(): bool
    {
        return $this->status === 'flagged' || !empty($this->flags);
    }

    public function primaryImage(): ?PropertyImage
    {
        return $this->images()->first();
    }
}
