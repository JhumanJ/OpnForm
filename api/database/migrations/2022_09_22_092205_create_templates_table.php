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

        Schema::create('templates', function (Blueprint $table) use ($driver) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->string('slug');
            $table->text('description');
            $table->string('image_url');
            if ($driver === 'mysql') {
                $table->jsonb('structure')->default(new Expression('(JSON_OBJECT())'));
            } else {
                $table->jsonb('structure')->default('{}');
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
        Schema::dropIfExists('templates');
    }
};
