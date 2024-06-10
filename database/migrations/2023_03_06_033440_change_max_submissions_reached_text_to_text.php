<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('forms', function (Blueprint $table) {
            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                $table->text('max_submissions_reached_text')->nullable()->default(new Expression("('This form has now reached the maximum number of allowed submissions and is now closed.')"))->change();
            } else {
                $table->text('max_submissions_reached_text')->nullable()->default('This form has now reached the maximum number of allowed submissions and is now closed.')->change();
            }
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
