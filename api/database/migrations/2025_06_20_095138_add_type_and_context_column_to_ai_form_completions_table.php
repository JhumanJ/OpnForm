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
        Schema::table('ai_form_completions', function (Blueprint $table) {
            $table->enum('type', ['form', 'fields'])->default('form');
            $table->json('context')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_form_completions', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('context');
        });
    }
};
