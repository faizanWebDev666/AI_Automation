<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');

            $table->string('image_path');
            $table->string('image_hash', 64); // SHA-256
            $table->boolean('is_live_photo')->default(false);
            $table->boolean('is_watermarked')->default(true);
            $table->boolean('is_duplicate')->default(false);
            $table->boolean('is_from_internet')->default(false);
            $table->tinyInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index('image_hash');
            $table->index('property_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_images');
    }
};
