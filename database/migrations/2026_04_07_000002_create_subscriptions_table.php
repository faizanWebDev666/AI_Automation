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
        if (!Schema::hasTable('subscriptions')) {
            Schema::create('subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('plan_id')->constrained('plans')->onDelete('restrict');
                $table->string('stripe_subscription_id')->unique()->nullable();
                $table->string('stripe_customer_id')->nullable();
                $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly');
                $table->enum('status', ['active', 'paused', 'canceled', 'expired'])->default('active');
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->timestamp('trial_ends_at')->nullable();
                $table->timestamp('canceled_at')->nullable();
                $table->integer('listings_used')->default(0);
                $table->integer('listed_this_month')->default(0);
                $table->timestamp('month_reset_at')->nullable();
                $table->timestamps();
                
                $table->index(['user_id', 'status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
