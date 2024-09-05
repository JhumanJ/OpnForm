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
        Schema::table('workspaces', function (Blueprint $table) use ($driver) {
            if ($driver === 'mysql') {
                $table->json('settings')->default(new Expression('(JSON_OBJECT())'))->nullable(true);
            } else {
                $table->json('settings')->default('{}')->nullable(true);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workspaces', function (Blueprint $table) {
            $table->dropColumn(['settings']);
        });
    }
};
