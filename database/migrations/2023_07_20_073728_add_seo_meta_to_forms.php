<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $driver = DB::getDriverName();

        Schema::table('forms', function (Blueprint $table) use ($driver) {
            if ($driver === 'mysql') {
                $table->json('seo_meta')->default(new Expression('(JSON_OBJECT())'));
            } else {
                $table->json('seo_meta')->default('{}');
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
            $table->dropColumn('seo_meta');
        });
    }
};
