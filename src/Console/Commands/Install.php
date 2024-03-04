<?php

namespace Cone\Bazar\Console\Commands;

use Illuminate\Console\Command;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bazar:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Bazar';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->call('migrate');
    }
}
