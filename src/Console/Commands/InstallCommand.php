<?php

namespace Bazar\Console\Commands;

use Bazar\Database\Seeders\BazarSeeder;
use Illuminate\Console\Command;

class InstallCommand extends Command
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

        if ($this->option('seed') && $this->laravel->environment('local')) {
            $status = $this->call('db:seed', ['--class' => BazarSeeder::class]);
        }

        $status = $this->call('bazar:publish');

        // Symlinking...
        // ln -s /.../packages/conedevelopment/bazar/public/app.js /.../public/vendor/bazar/app.js
        // ln -s /.../packages/conedevelopment/bazar/public/app.css /.../public/vendor/bazar/app.css
        // ln -s /.../packages/conedevelopment/bazar/resources/img /.../public/vendor/bazar/

        return $status;
    }
}
