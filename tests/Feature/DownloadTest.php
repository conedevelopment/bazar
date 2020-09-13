<?php

namespace Bazar\Tests\Feature;

use Bazar\Database\Factories\MediumFactory;
use Bazar\Database\Factories\OrderFactory;
use Bazar\Database\Factories\ProductFactory;
use Bazar\Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class DownloadTest extends TestCase
{
    protected $order;

    public function setUp(): void
    {
        parent::setUp();

        $medium = MediumFactory::new()->create();
        Storage::disk($medium->disk)->put($medium->path(), 'fake content');

        $product = ProductFactory::new()->create([
            'inventory' => [
                'downloadable' => true,
                'files' => [
                    ['name' => 'Valid', 'url' => $medium->url(), 'expiration' => 7],
                    ['name' => 'Expired', 'url' => $medium->url(), 'expiration' => 1],
                ],
            ],
        ]);

        $this->order = OrderFactory::new()->create();
        $this->order->products()->attach($product, ['quantity' => 1, 'tax' => 0, 'price' => $product->price]);
    }

    /** @test */
    public function an_order_has_downloads()
    {
        $this->assertEquals(2, $this->order->downloads()->count());
    }

    /** @test */
    public function a_user_can_download_files()
    {
        $this->travel(2)->days();

        $valid = $this->order->downloads()->firstWhere('name', 'Valid');
        $this->get($valid['url'])
            ->assertOk()
            ->assertHeader('Content-Disposition');

        $expired = $this->order->downloads()->firstWhere('name', 'Expired');
        $this->get($expired['url'])->assertForbidden();

        $invalid = URL::signedRoute('bazar.download', ['url' => 'fake_url'], 7);
        $this->get($invalid)->assertNotFound();
    }
}
