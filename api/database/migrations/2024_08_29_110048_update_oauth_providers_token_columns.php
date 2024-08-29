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
        Schema::table('oauth_providers', function (Blueprint $table) {
            $table->text('access_token')->change();
            $table->text('refresh_token')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('oauth_providers', function (Blueprint $table) {
            $table->string('access_token')->change();
            $table->string('refresh_token')->change();
        });
    }
};
