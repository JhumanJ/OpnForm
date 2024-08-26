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
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->string('license_key');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('license_provider');
            $table->string('status');
            $table->json('meta');
            $table->timestamps();

            $table->index(['license_key', 'license_provider']);
            $table->index(['user_id', 'license_provider']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('licenses');
    }
};
