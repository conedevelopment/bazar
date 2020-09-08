<?php

namespace Bazar\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use SplFileInfo;

class ClearFileChunksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bazar:clear-chunks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear the file chunks';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $now = time();

        foreach (Storage::disk('local')->allFiles('chunks') as $file) {
            $info = new SplFileInfo(Storage::disk('local')->path($file));

            if ($now - $info->getMTime() >= config('bazar.media.chunk_expiration')) {
                Storage::disk('local')->delete($file);
            }
        }

        $this->info('File chunks are cleared!');

        return 0;
    }
}
