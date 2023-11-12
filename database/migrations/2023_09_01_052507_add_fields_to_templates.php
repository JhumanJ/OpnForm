<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->boolean('publicly_listed')->default(false);
            $table->jsonb('industries')->default(new Expression("(JSON_ARRAY())"));
            $table->jsonb('types')->default(new Expression("(JSON_ARRAY())"));
            $table->string('short_description')->nullable();
            $table->jsonb('related_templates')->default(new Expression("(JSON_ARRAY())"));
            $table->string('image_url',500)->nullable()->change();
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
