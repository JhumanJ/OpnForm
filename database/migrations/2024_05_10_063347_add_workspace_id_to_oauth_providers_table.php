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
            $table->dropForeign('oauth_providers_user_id_foreign');
            $table->dropColumn('user_id');
        });

        Schema::table('oauth_providers', function (Blueprint $table) {
            $table->foreignId('workspace_id')
                ->nullable()
                ->after('id')
                ->constrained('workspaces')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('email')->after('provider_user_id')->nullable();
            $table->string('name')->after('email')->nullable();
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
            $table->dropForeign('oauth_providers_workspace_id_foreign');
            $table->dropColumn('workspace_id');

            $table->dropColumn('email');
            $table->dropColumn('name');
        });

        Schema::table('oauth_providers', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }
};
