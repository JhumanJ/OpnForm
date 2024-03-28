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

        Schema::create('form_integrations_events', function (Blueprint $table) use ($driver) {
            $table->id();
            $table->foreignIdFor(\App\Models\Integration\FormIntegration::class, 'integration_id');
            $table->string('status');
            if ($driver === 'mysql') {
                $table->jsonb('data')->default(new Expression("(JSON_OBJECT())"));
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
        Schema::dropIfExists('form_integrations_events');
    }
};
