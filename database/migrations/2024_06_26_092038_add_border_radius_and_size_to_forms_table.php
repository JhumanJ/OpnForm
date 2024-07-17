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
        Schema::table('forms', function (Blueprint $table) {
            $table->string('size')->default('md');
            $table->string('border_radius')->default('small');
        });

        // Then for each form with "Simple" theme on, disable border radius
        \App\Models\Forms\Form::whereTheme('simple')->update(['border_radius' => 'none']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->dropColumn(['size', 'border_radius']);
        });
    }
};
