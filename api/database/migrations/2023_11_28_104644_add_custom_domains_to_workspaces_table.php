<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
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
        $driver = DB::getDriverName();
        Schema::table('workspaces', function (Blueprint $table) use ($driver) {
            if ($driver === 'mysql') {
                $table->json('custom_domains')->default(new Expression('(JSON_OBJECT())'))->nullable(true);
            } else {
                $table->json('custom_domains')->default('{}')->nullable(true);
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
        Schema::table('workspaces', function (Blueprint $table) {
            $table->dropColumn('custom_domains');
        });
    }
};
