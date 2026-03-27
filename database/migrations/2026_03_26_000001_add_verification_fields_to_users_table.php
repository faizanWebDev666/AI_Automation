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
            // Verification fields
            $table->string('phone')->nullable()->after('email');
            $table->enum('verification_status', ['unverified', 'pending', 'verified', 'rejected'])->default('unverified')->after('phone');
            
            // CNIC
            $table->string('cnic_number')->nullable()->after('verification_status');
            $table->string('cnic_front_image')->nullable()->after('cnic_number');
            $table->string('cnic_back_image')->nullable()->after('cnic_front_image');
            
            // Verification photos
            $table->string('live_photo')->nullable()->after('cnic_back_image');
            $table->string('selfie_photo')->nullable()->after('live_photo');
            
            // Verification results
            $table->text('verification_notes')->nullable()->after('selfie_photo');
            $table->timestamp('verified_at')->nullable()->after('verification_notes');
            $table->timestamp('verification_submitted_at')->nullable()->after('verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'verification_status',
                'cnic_number',
                'cnic_front_image',
                'cnic_back_image',
                'live_photo',
                'selfie_photo',
                'verification_notes',
                'verified_at',
                'verification_submitted_at'
            ]);
        });
    }
};
