<?php

namespace Cone\Bazar\Database\Seeders;

use Cone\Bazar\Models\Address;
use Cone\Bazar\Models\Category;
use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Models\Shipping;
use Cone\Bazar\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;

class BazarSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->seedUsers();
        $this->seedCategories();
        $this->seedProducts();
        $this->seedOrders();
    }

    /**
     * Seed the users table.
     *
     * @return void
     */
    protected function seedUsers(): void
    {
        User::create([
            'name' => 'Bazar Admin',
            'email' => 'admin@bazar.test',
            'email_verified_at' => Date::now(),
            'password' => Hash::make('secret'),
        ]);

        User::create([
            'name' => 'Bazar User',
            'email' => 'user@bazar.test',
            'email_verified_at' => Date::now(),
            'password' => Hash::make('secret'),
        ]);
    }

    /**
     * Seed the categories table.
     *
     * @return void
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
     *
     * @return void
     */
    protected function seedProducts(): void
    {
        Product::factory()->count(4)->create()->each(function ($product) {
            $product->categories()->attach(Category::inRandomOrder()->take(2)->get());
        });
    }

    /**
     * Seed the orders table.
     *
     * @return void
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
