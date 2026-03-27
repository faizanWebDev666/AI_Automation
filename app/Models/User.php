<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'google2fa_secret', 'google2fa_enabled', 'google2fa_recovery_codes', 'phone', 'verification_status', 'cnic_number', 'cnic_front_image', 'cnic_back_image', 'live_photo', 'selfie_photo', 'verification_notes', 'verified_at', 'verification_submitted_at'])]
#[Hidden(['password', 'remember_token', 'google2fa_secret', 'google2fa_recovery_codes'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'google2fa_enabled' => 'boolean',
            'verified_at' => 'datetime',
            'verification_submitted_at' => 'datetime',
        ];
    }

    /**
     * Check if dealer is fully verified
     */
    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }
}
