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
                $table->json('notification_settings')->default(new Expression('(JSON_OBJECT())'))->nullable(true);
            } else {
                $table->json('notification_settings')->default('{}')->nullable(true);
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
            $table->dropColumn('notification_settings');
        });
    }
};
