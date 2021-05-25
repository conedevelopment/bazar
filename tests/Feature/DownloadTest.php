<?php

namespace Bazar\Tests\Feature;

use Bazar\Models\Medium;
use Bazar\Models\Order;
use Bazar\Models\Product;
use Bazar\Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class DownloadTest extends TestCase
{
    protected $order;

    public function setUp(): void
    {
        parent::setUp();

        $medium = Medium::factory()->create();
        Storage::disk($medium->disk)->put($medium->path(), 'fake content');

        $this->order = Order::factory()->create();

        $product = Product::factory()->create([
            'inventory' => [
                'downloadable' => true,
                'files' => [
                    ['name' => 'Valid', 'url' => $medium->fullPath(), 'expiration' => 7],
                    ['name' => 'Expired', 'url' => $medium->fullPath(), 'expiration' => 1],
                ],
            ],
        ]);

        $this->order->items()->create([
            'buyable_id' => $product->id,
            'buyable_type' => Product::class,
            'quantity' => 1,
            'tax' => 0,
            'price' => $product->price,
            'name' => $product->name,
        ]);
    }

    /** @test */
    public function an_order_has_downloads()
    {
        $this->assertEquals(2, $this->order->getDownloads()->count());
    }

    /** @test */
    public function a_user_can_download_files()
    {
        $this->travel(2)->days();

        $valid = $this->order->getDownloads()->firstWhere('name', 'Valid');
        $response = $this->get($valid['url'])
            ->assertOk()
            ->assertHeader('Content-Disposition');

        $this->assertSame('fake content', $response->streamedContent());

        $expired = $this->order->getDownloads()->firstWhere('name', 'Expired');
        $this->get($expired['url'])->assertForbidden();

        $invalid = URL::signedRoute('bazar.download', ['url' => 'fake_url'], 7);
        $this->get($invalid)->assertNotFound();
    }
}
