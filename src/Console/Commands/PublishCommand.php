<?php

namespace Bazar\Console\Commands;

use Bazar\BazarServiceProvider;
use Illuminate\Console\Command;

class PublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bazar:publish {--force : Overwrite existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish the Bazar assets';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        return $this->call('vendor:publish', [
            '--provider' => BazarServiceProvider::class,
        ]);
    }
}
