<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateFormSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $driver = DB::getDriverName();

        Schema::create('form_submissions', function (Blueprint $table) use ($driver) {
            $table->id();
            $table->foreignIdFor(\App\Models\Forms\Form::class, 'form_id');
            if ($driver === 'mysql') {
                $table->jsonb('data')->default(new Expression('(JSON_OBJECT())'));
            } else {
                $table->jsonb('data')->default('{}');
            }
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_submissions');
    }
}
