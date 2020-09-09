<?php

namespace Bazar\Console\Commands;

use Bazar\Support\Scaffold;
use Illuminate\Console\Command;
use Throwable;

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
    protected $description = 'Applying the Bazar scaffolding';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->warn('Applying the Bazar scaffolding.');

        try {
            Scaffold::install();

            $this->info('Bazar scaffolding has been applied.');

            $this->warn('Please run "npm install && npm run dev".');
        } catch (Throwable $e) {
            $this->error('Bazar scaffolding has been failed.');
        }

        return 0;
    }
}
