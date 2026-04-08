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
        if (!Schema::hasTable('plans')) {
            Schema::create('plans', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // Free, Silver, Gold
                $table->string('slug')->unique(); // free, silver, gold
                $table->text('description')->nullable();
                $table->decimal('monthly_price', 10, 2)->default(0);
                $table->decimal('yearly_price', 10, 2)->default(0);
                $table->string('stripe_monthly_price_id')->nullable();
                $table->string('stripe_yearly_price_id')->nullable();
                $table->integer('listings_per_month')->nullable(); // null = unlimited
                $table->boolean('highlighted_listings')->default(false);
                $table->boolean('multiple_images_videos')->default(false);
                $table->string('support_level')->default('email'); // email, chat, priority
                $table->boolean('basic_analytics')->default(false);
                $table->boolean('advanced_analytics')->default(false);
                $table->integer('featured_listings_per_month')->default(0);
                $table->boolean('virtual_tours')->default(false);
                $table->boolean('agency_profile')->default(false);
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
