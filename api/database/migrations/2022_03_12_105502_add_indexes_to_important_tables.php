<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToImportantTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('form_submissions', function (Blueprint $table) {
            $table->index('form_id');
        });

        Schema::table('forms', function (Blueprint $table) {
            $table->index('workspace_id');
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('form_submissions', function (Blueprint $table) {
            $table->dropIndex('form_id');
        });

        Schema::table('forms', function (Blueprint $table) {
            $table->dropIndex('workspace_id');
            $table->dropIndex('slug');
        });
    }
}
