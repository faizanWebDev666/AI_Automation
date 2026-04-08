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
        if (!Schema::hasTable('subscription_transactions')) {
            Schema::create('subscription_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('subscription_id')->constrained('subscriptions')->onDelete('cascade');
                $table->string('stripe_invoice_id')->unique()->nullable();
                $table->decimal('amount', 10, 2);
                $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
                $table->timestamp('paid_at')->nullable();
                $table->string('description')->nullable();
                $table->json('stripe_response')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_transactions');
    }
};
