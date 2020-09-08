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
        $status = $this->call('publish');

        if ($this->option('seed')) {
            $status = $this->call('db:seed', ['--class' => BazarSeeder::class]);
        }

        $status = $this->call('bazar:publish');

        return $status;
    }
}
