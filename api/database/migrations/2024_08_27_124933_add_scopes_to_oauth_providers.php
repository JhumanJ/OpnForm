<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        Schema::table('oauth_providers', function (Blueprint $table) use ($driver) {
            if ($driver === 'mysql') {
                $table->json('scopes')->default(new Expression('(JSON_OBJECT())'));
            } else {
                $table->json('scopes')->default('{}');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oauth_providers', function (Blueprint $table) {
            $table->dropColumn('scopes');
        });
    }
};
