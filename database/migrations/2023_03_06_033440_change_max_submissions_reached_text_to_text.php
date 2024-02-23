<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->text('max_submissions_reached_text')->nullable()->default('This form has now reached the maximum number of allowed submissions and is now closed.')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->string('max_submissions_reached_text')->nullable()->default('This form has now reached the maximum number of allowed submissions and is now closed.')->change();
        });
    }
};
