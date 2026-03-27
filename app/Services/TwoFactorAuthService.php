<?php

namespace App\Services;

use App\Models\User;

class TwoFactorAuthService
{
    /**
     * Generate a random secret for 2FA
     */
    public static function generateSecret(): string
    {
        return substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', 32)), 0, 32);
    }

    /**
     * Generate recovery codes
     */
    public static function generateRecoveryCodes(int $count = 10): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(substr(str_shuffle('0123456789ABCDEF'), 0, 8));
        }
        return $codes;
    }

    /**
     * Verify a TOTP code (simple implementation)
     */
    public static function verifyCode(User $user, string $code): bool
    {
        // For now, we'll implement a simple verification
        // In production, use a proper TOTP library
        if (!$user->google2fa_secret) {
            return false;
        }

        // Check if it's a recovery code
        if ($user->google2fa_recovery_codes) {
            $codes = json_decode($user->google2fa_recovery_codes, true) ?? [];
            if (in_array($code, $codes)) {
                // Remove used recovery code
                $codes = array_diff($codes, [$code]);
                $user->update(['google2fa_recovery_codes' => json_encode($codes)]);
                return true;
            }
        }

        // In a basic implementation, you could store temporary codes
        // For now, return false (implement proper TOTP if needed)
        return false;
    }

    /**
     * Generate QR code URL for authenticator apps
     */
    public static function getQrCodeUrl(User $user, string $secret): string
    {
        $issuer = config('app.name');
        return "otpauth://totp/{$issuer}:{$user->email}?secret={$secret}&issuer={$issuer}";
    }
}
