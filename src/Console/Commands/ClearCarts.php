<?php

namespace Cone\Bazar\Console\Commands;

use Cone\Bazar\Models\Cart;
use Cone\Bazar\Models\Item;
use Cone\Bazar\Models\Shipping;
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
            Cart::proxy()->newQuery()->truncate();
            Item::proxy()->newQuery()->where('itemable_type', Cart::getProxiedClass())->delete();
            Shipping::proxy()->newQuery()->where('shippable_type', Cart::getProxiedClass())->delete();

            $this->info('All carts have been deleted.');
        } else {
            Item::proxy()
                ->newQuery()
                ->where('itemable_type', Cart::getProxiedClass())
                ->whereIn('itemable_id', Cart::proxy()->newQuery()->expired()->select('id'))
                ->delete();

            Shipping::proxy()
                ->newQuery()
                ->where('shippable_type', Cart::getProxiedClass())
                ->whereIn('shippable_id', Cart::proxy()->newQuery()->expired()->select('id'))
                ->delete();

            Cart::proxy()->newQuery()->expired()->delete();

            $this->info('Expired carts have been deleted.');
        }
    }
}
