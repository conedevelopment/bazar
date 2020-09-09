<?php

namespace Bazar\Console\Commands;

use Bazar\Support\Scaffold;
use Illuminate\Console\Command;

class ScaffoldCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bazar:scaffold';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Bazar scaffolding';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        Scaffold::install();

        $this->info('Bazar scaffolding has been installed.');

        return 0;
    }
}
