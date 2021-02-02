<?php

namespace Bazar\Console\Commands;

use Bazar\Support\Facades\Asset;
use Illuminate\Console\Command;

class LinkAssetsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bazar:link-assets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Link the registered assets';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        Asset::link();

        return Command::SUCCESS;
    }
}
