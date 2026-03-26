<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->timestamp('edited_at')->nullable()->after('is_read');

            // Reply (quote)
            $table->unsignedBigInteger('reply_to_message_id')->nullable()->after('edited_at');
            $table->text('reply_to_message')->nullable()->after('reply_to_message_id');

            // Forward (original reference)
            $table->unsignedBigInteger('forwarded_from_message_id')->nullable()->after('reply_to_message');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn([
                'edited_at',
                'reply_to_message_id',
                'reply_to_message',
                'forwarded_from_message_id',
            ]);
        });
    }
};

