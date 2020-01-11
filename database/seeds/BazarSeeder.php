<?php

use Bazar\Jobs\MoveFile;
use Bazar\Jobs\PerformConversions;
use Bazar\Models\Address;
use Bazar\Models\Category;
use Bazar\Models\Medium;
use Bazar\Models\Order;
use Bazar\Models\Product;
use Bazar\Models\Shipping;
use Bazar\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;

class BazarSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment('local')) {
            $this->seedUsers();
            $this->seedCategories();
            $this->seedMedia();
            $this->seedProducts();
            $this->seedOrders();
        }
    }

    /**
     * Seed the users table.
     *
     * @return void
     */
    protected function seedUsers()
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
    protected function seedCategories()
    {
        $categories = ['Software', 'Sport', 'Cars', 'Food'];

        foreach ($categories as $name) {
            factory(Category::class)->create(compact('name'));
        }
    }

    /**
     * Seed the products table.
     *
     * @return void
     */
    protected function seedProducts()
    {
        factory(Product::class, 4)->create()->each(function ($product) {
            $product->categories()->attach(Category::inRandomOrder()->take(2)->get());
            $product->media()->attach(Medium::inRandomOrder()->first());
        });
    }

    /**
     * Seed the orders table.
     *
     * @return void
     */
    public function seedOrders()
    {
        $orders = factory(Order::class, 15)->make();

        $orders->each(function ($order) {
            $order->created_at = Carbon::now()->subDays(mt_rand(0, 20));

            $order->save();

            $data = Product::inRandomOrder()->take(mt_rand(1, 3))->get()->mapWithKeys(function ($product) {
                return [$product->id => [
                    'quantity' => mt_rand(1, 2),
                    'price' => $product->price,
                    'tax' => $product->price * 0.27,
                ]];
            })->all();

            $order->products()->attach($data);

            $order->address()->save(factory(Address::class)->make());

            $order->shipping()->save(factory(Shipping::class)->make());
            $order->shipping->address()->save(factory(Address::class)->make());

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
    public function seedMedia()
    {
        foreach (range(1, 4) as $key) {
            $path = __DIR__."/../../resources/img/photo-0{$key}.jpg";

            $medium = Medium::createFrom($path);

            MoveFile::withChain([
                new PerformConversions($medium),
            ])->dispatch($medium, $path, true);
        }
    }
}
