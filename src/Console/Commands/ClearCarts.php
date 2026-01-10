<?php

declare(strict_types=1);

namespace Cone\Bazar\Console\Commands;

use Cone\Bazar\Models\Cart;
use Illuminate\Console\Command;

class ClearCarts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bazar:clear-carts {--all : Clear all the carts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear the expired carts';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        if ($this->option('all')) {
            Cart::proxy()->newQuery()->cursor()->each(static function (Cart $cart): void {
                $cart->delete();
            });

            Cart::proxy()->newQuery()->truncate();

            $this->info('All carts have been deleted.');
        } else {
            Cart::proxy()->newQuery()->expired()->cursor()->each(static function (Cart $cart): void {
                $cart->delete();
            });

            $this->info('Expired carts have been deleted.');
        }
    }
}
