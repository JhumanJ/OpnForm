<?php

use Illuminate\Database\Migrations\Migration;
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
        Schema::create('form_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Forms\Form::class, 'form_id');
            // To make this migration fully compatible with both databases, you should
            // ensure that the data you intend to store in the data column is valid JSON.
            $table->json('data');
            $table->date('date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_statistics');
    }
};
