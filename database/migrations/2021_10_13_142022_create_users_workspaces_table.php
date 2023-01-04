<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUsersWorkspacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_workspace', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->references('id')->on('workspaces')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('user_id')->references('id')->on('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unique(['workspace_id', 'user_id']);
            $table->boolean('is_owner')->default(false);
            $table->string('access_token')->nullable();
            $table->timestamps();
        });

        // Add creator id to forms table
        Schema::table('forms', function (Blueprint $table) {
            $table->foreignId('creator_id')->references('id')->on('users')->cascadeOnUpdate()->cascadeOnDelete();
        });

        // Create relation - can't use models because of new methods clash
        DB::table('workspaces')
            ->orderBy('id')
            ->chunk(100, function ($workspaces) {
                foreach ($workspaces as $workspace) {
                    echo '.';

                    // Make sure user wasn't deleted
                    if (!DB::table('users')->where('id',
                        $workspace->user_id)->exists()) {
                        continue;
                    }

                    // Create relation
                    $now = now();
                    \Illuminate\Support\Facades\DB::table('user_workspace')->insert([
                        'workspace_id' => $workspace->id,
                        'access_token' => $workspace->access_token,
                        'user_id' => $workspace->user_id,
                        'is_owner' => true,
                        'created_at' => $now,
                        'updated_at' => $now
                    ]);

                    // Set form creator id
                    foreach (\App\Models\Forms\Form::withTrashed()->where('workspace_id',$workspace->id)->get() as $form) {
                        $form->update(['creator_id'=>$workspace->user_id]);
                    }
                }
            });

        // Drop column
        Schema::table('workspaces', function (Blueprint $table) {
            $table->dropColumn(['user_id','access_token']);
        });

        Schema::table('user_workspace', function (Blueprint $table) {
            $table->string('access_token')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Illuminate\Support\Facades\DB::transaction(function () {
            Schema::table('workspaces', function (Blueprint $table) {
                $table->integer('user_id')->unsigned()->nullable();
                $table->string('access_token')->nullable();
                $table->foreign('user_id')->references('id')->on('users')
                    ->onDelete('cascade')->onUpdate('cascade');
            });

            // Remove relation
            \App\Models\Workspace::orderBy('id')
                ->chunk(100, function ($workspaces) {
                    foreach ($workspaces as $workspace) {
                        if ($workspace->users()->count() == 0) {
                            $workspace->delete();
                        } else {
                            $workspace->user_id = $workspace->users->first()->id;
                            $workspace->access_token = $workspace->users()->withPivot('access_token')->first()->pivot->access_token;
                            $workspace->save();
                        }
                    }
                });

            // Add creator id to forms table
            Schema::table('forms', function (Blueprint $table) {
                $table->dropColumn('creator_id');
            });
            Schema::dropIfExists('user_workspace');
        });
    }
}
