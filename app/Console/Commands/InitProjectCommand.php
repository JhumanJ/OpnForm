<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InitProjectCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:init-project';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (config('app.self_hosted')) {
            Artisan::call('migrate:fresh');
            User::create([
                'name' => 'Admin',
                'email' => 'admin@opnform.com',
                'password' => bcrypt('password'),
            ]);
        }
    }
}
