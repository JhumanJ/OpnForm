<?php

use App\Models\Forms\FormSubmission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('form_submissions', function (Blueprint $table) {
            $table->enum('status', [FormSubmission::STATUS_PARTIAL, FormSubmission::STATUS_COMPLETED])
                ->default(FormSubmission::STATUS_COMPLETED)
                ->index();
        });

        Schema::table('forms', function (Blueprint $table) {
            $table->boolean('enable_partial_submissions')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('form_submissions', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('forms', function (Blueprint $table) {
            $table->dropColumn('enable_partial_submissions');
        });
    }
};
