<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Verification retry tracking
            $table->integer('verification_failed_attempts')->default(0)->after('verification_status');
            $table->boolean('verification_banned')->default(false)->after('verification_failed_attempts');
            $table->timestamp('verification_banned_at')->nullable()->after('verification_banned');
            $table->text('verification_ban_reason')->nullable()->after('verification_banned_at');
            $table->timestamp('last_verification_attempt_at')->nullable()->after('verification_ban_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'verification_failed_attempts',
                'verification_banned',
                'verification_banned_at',
                'verification_ban_reason',
                'last_verification_attempt_at',
            ]);
        });
    }
};
