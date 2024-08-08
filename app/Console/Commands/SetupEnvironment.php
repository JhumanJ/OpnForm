<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SetupEnvironment extends Command
{
    protected $signature = 'setup:env {--docker : Use the .env.docker file as the template}';

    protected $description = 'Setup the .env files for the application';

    public function handle()
    {
        if (File::exists(base_path('.env'))) {
            $this->info('.env file already exists. Skipping setup.');
            return;
        }

        $this->info('Setting up .env files...');

        // Determine the source .env file based on the --docker flag
        $sourceEnv = $this->option('docker') ? '.env.docker' : '.env.example';

        // Generate shared client secret
        $sharedSecret = Str::random(40);

        // Create .env file from the template
        $envPath = base_path('.env');
        File::copy(base_path($sourceEnv), $envPath);

        // Set the environment values
        $this->setEnvValue($envPath, 'FRONT_API_SECRET', $sharedSecret);

        // Generate APP_KEY and JWT_SECRET
        Artisan::call('key:generate');
        Artisan::call('jwt:secret -f');

        $clientEnvPath = base_path('client/.env');
        if (!File::exists($clientEnvPath)) {
            $clientSourceEnv = $this->option('docker') ? 'client/.env.docker' : 'client/.env.example';
            File::copy(base_path($clientSourceEnv), $clientEnvPath);
            $this->setEnvValue($clientEnvPath, 'NUXT_API_SECRET', $sharedSecret);
        }

        $this->info('.env and client/.env file has been set up successfully.');
    }

    protected function setEnvValue($filePath, $key, $value)
    {
        $envContents = File::get($filePath);
        $keyPattern = $this->keyReplacementPattern($key);
        $replacement = "{$key}={$value}";

        if (Str::contains($envContents, $key)) {
            $envContents = preg_replace($keyPattern, $replacement, $envContents);
        } else {
            $envContents .= PHP_EOL . "{$key}={$value}";
        }

        File::put($filePath, $envContents);
    }

    protected function keyReplacementPattern($key)
    {
        $escaped = preg_quote('=' . env($key), '/');
        return "/^{$key}{$escaped}/m";
    }
}
