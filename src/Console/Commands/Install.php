<?php

namespace Cone\Bazar\Console\Commands;

use Cone\Bazar\Database\Seeders\BazarSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

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
     *
     * @return int
     */
    public function handle(): int
    {
        $status = $this->call('migrate');

        if ($this->option('seed') && $this->laravel->environment(['local', 'testing'])) {
            $status = $this->call('db:seed', ['--class' => BazarSeeder::class]);
        }

        if (! File::exists($path = $this->laravel->basePath('app/Providers/BazarServiceProvider.php'))) {
            File::copy(
                __DIR__.'/../../../resources/stubs/BazarServiceProvider.php', $path
            );
        }

        return $status;
    }
}
