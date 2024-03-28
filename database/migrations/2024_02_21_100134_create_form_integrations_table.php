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

        Schema::create('form_integrations', function (Blueprint $table) use ($driver) {
            $table->id();
            $table->foreignIdFor(\App\Models\Forms\Form::class, 'form_id');
            $table->string('status');
            $table->string('integration_id');
            if ($driver === 'mysql') {
                $table->jsonb('logic')->default(new Expression("(JSON_OBJECT())"));
                $table->jsonb('data')->default(new Expression("(JSON_OBJECT())"));
            } else {
                $table->jsonb('logic')->default('{}');
                $table->jsonb('data')->default('{}');
            }
            $table->string('oauth_id')->nullable();
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
        Schema::dropIfExists('form_integrations');
    }
};
