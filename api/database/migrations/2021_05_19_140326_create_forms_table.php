<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $driver = DB::getDriverName();

        Schema::create('forms', function (Blueprint $table) use ($driver) {
            $table->id();
            $table->foreignIdFor(\App\Models\Workspace::class, 'workspace_id');
            $table->string('title');
            $table->string('slug');
            $table->json('properties');
            $table->timestamps();
            $table->boolean('notifies')->default(false);
            $table->text('description')->nullable();
            $table->text('submit_button_text')->default(new Expression("('Submit')"));
            $table->boolean('re_fillable')->default(false);
            $table->text('re_fill_button_text')->default(new Expression("('Fill Again')"));
            $table->string('color')->default('#3B82F6');
            $table->boolean('uppercase_labels')->default(true);
            $table->boolean('no_branding')->default(false);
            $table->boolean('hide_title')->default(false);
            $table->text('submitted_text')->default(new Expression("('Amazing, we saved your answers. Thank you for your time and have a great day!')"));
            $table->string('dark_mode')->default('auto');
            $table->string('webhook_url')->nullable();
            $table->boolean('send_submission_confirmation')->default(false);
            $table->string('logo_picture')->nullable();
            $table->string('cover_picture')->nullable();
            $table->string('redirect_url')->nullable();
            $table->text('custom_code')->nullable();
            $table->text('notification_emails')->nullable();
            $table->string('theme')->default('default');
            $table->jsonb('database_fields_update')->nullable();
            $table->string('width')->default('centered');
            $table->boolean('transparent_background')->default(false);
            $table->timestamp('closes_at')->nullable();
            $table->text('closed_text')->nullable();
            $table->string('notification_subject')->default('We saved your answers');
            $table->text('notification_body')->default(new Expression("('<p>Hello there 👋 <br>This is a confirmation that your submission was successfully saved.</p>')"));
            $table->boolean('notifications_include_submission')->default(true);
            $table->boolean('use_captcha')->default(false);
            $table->boolean('can_be_indexed')->default(true);
            $table->string('password')->nullable()->default(null);
            $table->string('notification_sender')->default('OpenForm');
            if ($driver === 'mysql') {
                $table->jsonb('tags')->default(new Expression('(JSON_ARRAY())'));
            } else {
                $table->jsonb('tags')->default('[]')->nullable();
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
        Schema::dropIfExists('forms');
    }
}
