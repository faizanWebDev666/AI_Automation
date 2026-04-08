<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'stripe_subscription_id',
        'stripe_customer_id',
        'billing_cycle',
        'status',
        'starts_at',
        'ends_at',
        'trial_ends_at',
        'canceled_at',
        'listings_used',
        'listed_this_month',
        'month_reset_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'canceled_at' => 'datetime',
        'month_reset_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function transactions()
    {
        return $this->hasMany(SubscriptionTransaction::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOfUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeWithoutCanceled($query)
    {
        return $query->whereIn('status', ['active', 'paused']);
    }

    // Status Checks
    public function isActive()
    {
        return $this->status === 'active' && (!$this->ends_at || $this->ends_at->isFuture());
    }

    public function isCanceled()
    {
        return $this->status === 'canceled';
    }

    public function isPaused()
    {
        return $this->status === 'paused';
    }

    public function isOnTrial()
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    // Listing Management
    public function canAddListing()
    {
        if (!$this->isActive()) {
            return false;
        }

        $plan = $this->plan;

        // If unlimited listings
        if ($plan->listings_per_month === null) {
            return true;
        }

        // Reset monthly count if needed
        $this->resetMonthlyCountIfNeeded();

        return $this->listed_this_month < $plan->listings_per_month;
    }

    public function getRemainingListings()
    {
        $plan = $this->plan;

        if ($plan->listings_per_month === null) {
            return PHP_INT_MAX; // Unlimited
        }

        $this->resetMonthlyCountIfNeeded();

        return max(0, $plan->listings_per_month - $this->listed_this_month);
    }

    public function addListing()
    {
        if ($this->canAddListing()) {
            $this->increment('listed_this_month');
            return true;
        }
        return false;
    }

    public function resetMonthlyCountIfNeeded()
    {
        if (!$this->month_reset_at || $this->month_reset_at->isPast()) {
            $this->update([
                'listed_this_month' => 0,
                'month_reset_at' => Carbon::now()->addMonth(),
            ]);
        }
    }

    // Feature Checks
    public function hasHighlightedListings()
    {
        return $this->isActive() && $this->plan->highlighted_listings;
    }

    public function hasMultipleImagesVideos()
    {
        return $this->isActive() && $this->plan->multiple_images_videos;
    }

    public function hasBasicAnalytics()
    {
        return $this->isActive() && $this->plan->basic_analytics;
    }

    public function hasAdvancedAnalytics()
    {
        return $this->isActive() && $this->plan->advanced_analytics;
    }

    public function hasFeaturedListings()
    {
        return $this->isActive() && $this->plan->featured_listings_per_month > 0;
    }

    public function getFeaturedListingsCount()
    {
        return $this->plan->featured_listings_per_month;
    }

    public function hasVirtualTours()
    {
        return $this->isActive() && $this->plan->virtual_tours;
    }

    public function hasAgencyProfile()
    {
        return $this->isActive() && $this->plan->agency_profile;
    }

    public function getSupportLevel()
    {
        return $this->isActive() ? $this->plan->support_level : 'none';
    }

    // Price Info
    public function getCurrentPrice()
    {
        return $this->plan->getDisplayPrice($this->billing_cycle);
    }
}
