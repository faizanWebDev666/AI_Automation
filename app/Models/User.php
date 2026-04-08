<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'google_id',
        'avatar',
        'role',
        'google2fa_secret',
        'google2fa_enabled',
        'google2fa_recovery_codes',
        'phone',
        'verification_status',
        'cnic_number',
        'cnic_front_image',
        'cnic_back_image',
        'live_photo',
        'selfie_photo',
        'verification_notes',
        'verified_at',
        'verification_submitted_at',
        'verification_failed_attempts',
        'verification_banned',
        'verification_banned_at',
        'verification_ban_reason',
        'last_verification_attempt_at',
        'stripe_customer_id',
        'subscription_status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
        'google2fa_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'google2fa_enabled' => 'boolean',
            'verified_at' => 'datetime',
            'verification_submitted_at' => 'datetime',
            'verification_banned' => 'boolean',
            'verification_banned_at' => 'datetime',
            'last_verification_attempt_at' => 'datetime',
        ];
    }

    /**
     * Check if dealer is fully verified
     */
    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }

    /**
     * Check if dealer is banned from verification
     */
    public function isVerificationBanned(): bool
    {
        return $this->verification_banned === true;
    }

    /**
     * Get remaining verification attempts
     */
    public function getRemainingVerificationAttempts(): int
    {
        $MAX_ATTEMPTS = 5;
        return max(0, $MAX_ATTEMPTS - $this->verification_failed_attempts);
    }

    /**
     * Increment failed verification attempts and ban if needed
     */
    public function recordFailedVerification(): void
    {
        $MAX_ATTEMPTS = 5;
        $this->verification_failed_attempts++;
        $this->last_verification_attempt_at = now();

        if ($this->verification_failed_attempts >= $MAX_ATTEMPTS) {
            $this->verification_banned = true;
            $this->verification_banned_at = now();
            $this->verification_ban_reason = "Maximum verification attempts ({$MAX_ATTEMPTS}) exceeded";
        }

        $this->save();
    }

    /**
     * Reset verification attempts (after successful verification)
     */
    public function resetVerificationAttempts(): void
    {
        $this->verification_failed_attempts = 0;
        $this->last_verification_attempt_at = now();
        $this->save();
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    // Subscription Relationships
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            })
            ->latest('created_at');
    }

    // Subscription Helper Methods
    public function hasActiveSubscription()
    {
        return $this->activeSubscription()->exists();
    }

    public function getActiveSubscription()
    {
        return $this->activeSubscription()->first();
    }

    public function isPremiumUser()
    {
        $subscription = $this->getActiveSubscription();
        return $subscription && !$subscription->plan->isFreePlan();
    }

    public function getCurrentPlan()
    {
        $subscription = $this->getActiveSubscription();
        return $subscription ? $subscription->plan : Plan::where('slug', 'free')->first();
    }

    public function canAddListing()
    {
        $subscription = $this->getActiveSubscription();
        if (!$subscription) {
            // Free plan user
            $freePlan = Plan::where('slug', 'free')->first();
            if ($freePlan) {
                // Create free subscription if doesn't exist
                if (!$this->subscriptions()->where('plan_id', $freePlan->id)->exists()) {
                    $this->subscriptions()->create([
                        'plan_id' => $freePlan->id,
                        'status' => 'active',
                        'billing_cycle' => 'monthly',
                        'starts_at' => now(),
                    ]);
                }
                $subscription = $this->getActiveSubscription();
            }
        }

        return $subscription ? $subscription->canAddListing() : false;
    }

    public function getRemainingListings()
    {
        $subscription = $this->getActiveSubscription();
        return $subscription ? $subscription->getRemainingListings() : 0;
    }

    public function addListing()
    {
        $subscription = $this->getActiveSubscription();
        if ($subscription) {
            return $subscription->addListing();
        }
        return false;
    }
}
