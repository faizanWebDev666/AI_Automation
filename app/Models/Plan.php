<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'monthly_price',
        'yearly_price',
        'stripe_monthly_price_id',
        'stripe_yearly_price_id',
        'listings_per_month',
        'highlighted_listings',
        'multiple_images_videos',
        'support_level',
        'basic_analytics',
        'advanced_analytics',
        'featured_listings_per_month',
        'virtual_tours',
        'agency_profile',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'highlighted_listings' => 'boolean',
        'multiple_images_videos' => 'boolean',
        'basic_analytics' => 'boolean',
        'advanced_analytics' => 'boolean',
        'virtual_tours' => 'boolean',
        'agency_profile' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // Helpers
    public function isFreePlan()
    {
        return $this->slug === 'free';
    }

    public function isPremium()
    {
        return $this->monthly_price > 0 || $this->yearly_price > 0;
    }

    public function getDisplayPrice($billingCycle = 'monthly')
    {
        if ($billingCycle === 'yearly') {
            return $this->yearly_price;
        }
        return $this->monthly_price;
    }
}
