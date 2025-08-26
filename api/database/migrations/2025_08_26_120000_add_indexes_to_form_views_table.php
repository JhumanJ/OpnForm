<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('form_views', function (Blueprint $table) {
            // Speed up lookups by form_id for analytics queries
            $table->index('form_id');

            // Beneficial for date-based cleanup/aggregation tasks
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('form_views', function (Blueprint $table) {
            $table->dropIndex(['form_id']);
            $table->dropIndex(['created_at']);
        });
    }
};
