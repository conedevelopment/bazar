<?php

namespace Cone\Bazar\Database\Seeders;

use Cone\Bazar\Models\Address;
use Cone\Bazar\Models\Category;
use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Models\Shipping;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Date;

class BazarTestDataSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->seedCategories();
        $this->seedProducts();
        $this->seedOrders();
    }

    /**
     * Seed the categories table.
     */
    protected function seedCategories(): void
    {
        $categories = ['Software', 'Sport', 'Cars', 'Food'];

        foreach ($categories as $name) {
            Category::factory()->create(compact('name'));
        }
    }

    /**
     * Seed the products table.
     */
    protected function seedProducts(): void
    {
        Product::factory()->count(4)->create()->each(function ($product) {
            $product->categories()->attach(Category::inRandomOrder()->take(2)->get());
        });
    }

    /**
     * Seed the orders table.
     */
    protected function seedOrders(): void
    {
        $orders = Order::factory()->count(15)->make();

        $orders->each(function ($order, $key) {
            $order->created_at = Date::now()->subDays(15 - $key);

            $order->save();

            Product::inRandomOrder()->take(mt_rand(1, 3))->get()->each(function ($product) use ($order) {
                $order->items()->create([
                    'buyable_id' => $product->id,
                    'buyable_type' => Product::class,
                    'quantity' => mt_rand(1, 2),
                    'price' => $product->price,
                    'tax' => $product->price * 0.27,
                    'name' => $product->name,
                ]);
            });

            $order->address()->save(Address::factory()->make());
            $order->shipping()->save(Shipping::factory()->make());
            $order->shipping->address()->save(Address::factory()->make());

            $order->transactions()->create([
                'driver' => 'cash',
                'type' => 'payment',
                'amount' => $order->getTotal(),
            ]);
        });
    }
}
