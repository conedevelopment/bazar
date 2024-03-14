<?php

namespace Cone\Bazar\Listeners;

use Illuminate\Foundation\Events\VendorTagPublished;
use Illuminate\Support\Facades\App;

class FormatBazarStubs
{
    /**
     * Handle the event.
     */
    public function handle(VendorTagPublished $event): void
    {
        if ($event->tag === 'bazar-stubs') {
            foreach ($event->paths as $from => $to) {
                $contents = file_get_contents($to);

                $contents = str_replace('{{ namespace }}', App::getNamespace(), $contents);

                file_put_contents($to, $contents);
            }
        }
    }
}
