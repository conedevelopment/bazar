<?php

namespace Cone\Bazar\Console\Commands;

use Cone\Bazar\Database\Seeders\BazarTestDataSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bazar:install {--seed : Seed the database with fake data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Bazar';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $status = $this->call('migrate');

        if ($this->option('seed')) {
            $status = $this->call('db:seed', ['--class' => BazarTestDataSeeder::class]);
        }

        $status = $this->call('vendor:publish', ['--tag' => 'bazar-provider']);

        $this->registerServiceProvider();

        return $status;
    }

    /**
     * Register the Bazar service provider in the application configuration file.
     */
    protected function registerServiceProvider(): void
    {
        $namespace = Str::replaceLast('\\', '', $this->laravel->getNamespace());

        $appConfig = file_get_contents($this->laravel->configPath('app.php'));

        if (str_contains($appConfig, $namespace.'\\Providers\\BazarServiceProvider::class')) {
            return;
        }

        file_put_contents($this->laravel->configPath('app.php'), str_replace(
            "{$namespace}\\Providers\\RootServiceProvider::class,",
            sprintf(
                '%1$s\\Providers\\BazarServiceProvider::class,%2$s%3$s%1$s\\Providers\\RootServiceProvider::class,',
                $namespace, PHP_EOL, str_repeat(' ', 8)
            ),
            $appConfig
        ));

        file_put_contents($this->laravel->path('Providers/BazarServiceProvider.php'), str_replace(
            ['namespace App\\Providers;'],
            ["namespace {$namespace}\\Providers;"],
            file_get_contents($this->laravel->path('Providers/BazarServiceProvider.php'))
        ));
    }
}
