<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('username');
            $table->foreignId('sender_id')->after('id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->after('sender_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_read')->default(false)->after('message');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['sender_id']);
            $table->dropForeign(['receiver_id']);
            $table->dropColumn(['sender_id', 'receiver_id', 'is_read']);
            $table->string('username');
        });
    }
};
