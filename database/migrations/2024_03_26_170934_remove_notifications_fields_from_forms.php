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
        Schema::table('forms', function (Blueprint $table) {
            $table->dropColumn([
                'notifies',
                'notification_emails',
                'send_submission_confirmation',
                'notification_sender',
                'notification_subject',
                'notification_body',
                'notifications_include_submission',
                'slack_webhook_url',
                'discord_webhook_url',
                'webhook_url',
                'notification_settings'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $driver = DB::getDriverName();

        Schema::table('forms', function (Blueprint $table) use ($driver) {
            $table->boolean('notifies')->default(false);
            $table->text('notification_emails')->nullable();
            $table->boolean('send_submission_confirmation')->default(false);
            $table->string('notification_sender')->default('OpenForm');
            $table->string('notification_subject')->default('We saved your answers');
            $table->text('notification_body')->default(new Expression("('<p>Hello there 👋 <br>This is a confirmation that your submission was successfully saved.</p>')"));
            $table->boolean('notifications_include_submission')->default(true);
            $table->string('slack_webhook_url')->nullable();
            $table->string('discord_webhook_url')->nullable();
            $table->string('webhook_url')->nullable();
            if ($driver === 'mysql') {
                $table->json('notification_settings')->default(new Expression('(JSON_OBJECT())'))->nullable(true);
            } else {
                $table->json('notification_settings')->default('{}')->nullable(true);
            }
        });
    }
};
