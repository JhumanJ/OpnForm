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

        Schema::table('templates', function (Blueprint $table) use ($driver) {
            $table->boolean('publicly_listed')->default(false);

            if ($driver === 'mysql') {
                $table->jsonb('industries')->default(new Expression('(JSON_ARRAY())'));
            } else {
                $table->jsonb('industries')->default('[]');
            }

            if ($driver === 'mysql') {
                $table->jsonb('types')->default(new Expression('(JSON_ARRAY())'));
            } else {
                $table->jsonb('types')->default('[]');
            }

            $table->string('short_description')->nullable();

            if ($driver === 'mysql') {
                $table->jsonb('related_templates')->default(new Expression('(JSON_ARRAY())'));
            } else {
                $table->jsonb('related_templates')->default('[]');
            }

            $table->string('image_url', 500)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->dropColumn(['publicly_listed', 'industries', 'types', 'short_description', 'related_templates']);
        });
    }
};
