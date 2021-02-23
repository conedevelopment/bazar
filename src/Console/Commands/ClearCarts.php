<?php

namespace Bazar\Console\Commands;

use Bazar\Models\Item;
use Bazar\Proxies\Cart as CartProxy;
use Bazar\Proxies\Shipping as ShippingProxy;
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
     *
     * @return int
     */
    public function handle(): int
    {
        if ($this->option('all')) {
            CartProxy::query()->truncate();
            Item::query()->where('itemable_type', CartProxy::getProxiedClass())->delete();
            ShippingProxy::query()->where('shippable_type', CartProxy::getProxiedClass())->delete();

            $this->info('All carts have been deleted.');
        } else {
            Item::query()
                ->where('itemable_type', CartProxy::getProxiedClass())
                ->whereIn('itemable_id', CartProxy::query()->expired()->select('id'))
                ->delete();

            ShippingProxy::query()
                ->where('shippable_type', CartProxy::getProxiedClass())
                ->whereIn('shippable_id', CartProxy::query()->expired()->select('id'))
                ->delete();

            CartProxy::query()->expired()->delete();

            $this->info('Expired carts have been deleted.');
        }

        return Command::SUCCESS;
    }
}
