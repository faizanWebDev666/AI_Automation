<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Basic Info
            $table->string('title');
            $table->enum('property_type', ['house', 'portion', 'apartment', 'plot', 'commercial'])->default('house');
            $table->enum('listing_type', ['sale', 'rent'])->default('sale');
            $table->enum('ownership_type', ['owner', 'dealer', 'builder'])->default('owner');
            $table->decimal('price', 12, 2);
            $table->decimal('area_marla', 6, 2);

            // Property Details
            $table->tinyInteger('bedrooms')->default(1);
            $table->tinyInteger('bathrooms')->default(1);
            $table->tinyInteger('kitchens')->default(1);
            $table->tinyInteger('floors')->default(1);
            $table->enum('furnished', ['furnished', 'semi-furnished', 'unfurnished'])->default('unfurnished');
            $table->text('description')->nullable();

            // Location
            $table->string('city');
            $table->string('area_name');
            $table->string('full_address');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Documents
            $table->string('electricity_bill')->nullable();
            $table->string('ownership_proof')->nullable();

            // Contact
            $table->string('contact_phone');

            // Moderation
            $table->enum('status', ['draft', 'pending_review', 'approved', 'rejected', 'flagged'])->default('pending_review');
            $table->text('admin_notes')->nullable();
            $table->json('flags')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            // Indexes for fraud detection
            $table->index('contact_phone');
            $table->index('city');
            $table->index('status');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
