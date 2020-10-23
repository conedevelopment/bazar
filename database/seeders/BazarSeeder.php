<?php

namespace Bazar\Database\Seeders;

use Bazar\Database\Factories\AddressFactory;
use Bazar\Database\Factories\CategoryFactory;
use Bazar\Database\Factories\OrderFactory;
use Bazar\Database\Factories\ProductFactory;
use Bazar\Database\Factories\ShippingFactory;
use Bazar\Jobs\MoveFile;
use Bazar\Jobs\PerformConversions;
use Bazar\Models\Category;
use Bazar\Models\Medium;
use Bazar\Models\Product;
use Bazar\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
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
        $this->seedMedia();
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
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('secret'),
        ]);

        User::create([
            'name' => 'Bazar User',
            'email' => 'user@bazar.test',
            'email_verified_at' => Carbon::now(),
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
            CategoryFactory::new()->create(compact('name'));
        }
    }

    /**
     * Seed the products table.
     *
     * @return void
     */
    protected function seedProducts(): void
    {
        ProductFactory::new()->count(4)->create()->each(function ($product) {
            $product->categories()->attach(Category::inRandomOrder()->take(2)->get());
            $product->media()->attach(Medium::inRandomOrder()->first());
        });
    }

    /**
     * Seed the orders table.
     *
     * @return void
     */
    protected function seedOrders(): void
    {
        $orders = OrderFactory::new()->count(15)->make();

        $orders->each(function ($order, $key) {
            $order->created_at = Carbon::now()->subDays(15 - $key);

            $order->save();

            $data = Product::inRandomOrder()->take(mt_rand(1, 3))->get()->mapWithKeys(function ($product) {
                return [$product->id => [
                    'quantity' => mt_rand(1, 2),
                    'price' => $product->price,
                    'tax' => $product->price * 0.27,
                ]];
            })->all();

            $order->products()->attach($data);
            $order->address()->save(AddressFactory::new()->make());
            $order->shipping()->save(ShippingFactory::new()->make());
            $order->shipping->address()->save(AddressFactory::new()->make());

            $order->transactions()->create([
                'driver' => 'cash',
                'type' => 'payment',
                'amount' => $order->total(),
            ]);
        });
    }

    /**
     * Seed the media table.
     *
     * @return void
     */
    protected function seedMedia(): void
    {
        foreach (range(1, 4) as $key) {
            $path = __DIR__."/../../resources/stubs/photo-0{$key}.jpg";

            $medium = Medium::createFrom($path);

            MoveFile::withChain([
                new PerformConversions($medium),
            ])->dispatch($medium, $path, true);
        }
    }
}
