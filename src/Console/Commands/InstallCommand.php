<?php

namespace Bazar\Console\Commands;

use Bazar\Database\Seeders\BazarSeeder;
use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The assets.
     *
     * @var array
     */
    protected $assets = [
        'public' => 'vendor/bazar',
        'resources/img' => 'vendor/bazar/img',
    ];

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

        foreach ($this->assets as $from => $to) {
            if (! file_exists(public_path($to))) {
                symlink(__DIR__.'/../../../'.$from, public_path($to));
            }
        }

        return $status;
    }
}
