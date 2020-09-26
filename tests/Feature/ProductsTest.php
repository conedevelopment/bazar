<?php

namespace Bazar\Tests\Feature;

use Bazar\Database\Factories\ProductFactory;
use Bazar\Models\Product;
use Bazar\Tests\TestCase;

class ProductsTest extends TestCase
{
    protected $product;

    public function setUp(): void
    {
        parent::setUp();

        $this->product = ProductFactory::new()->create();
    }

    /** @test */
    public function an_admin_can_index_products()
    {
        $this->actingAs($this->user)
            ->get(route('bazar.products.index'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(route('bazar.products.index'))
            ->assertOk()
            ->assertComponent('Products/Index')
            ->assertPropValue('results', function ($results) {
                $this->assertEquals(
                    $results, Product::with('media')->paginate()->toArray()
                );
            });
    }

    /** @test */
    public function an_admin_can_create_product()
    {
        $this->actingAs($this->user)
            ->get(route('bazar.products.create'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(route('bazar.products.create'))
            ->assertOk()
            ->assertComponent('Products/Create');
    }

    /** @test */
    public function an_admin_can_store_product()
    {
        $this->actingAs($this->user)
            ->post(route('bazar.products.store'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->post(route('bazar.products.store'), [])
            ->assertStatus(302)
            ->assertSessionHasErrors();

        $this->actingAs($this->admin)->post(
            route('bazar.products.store'),
            ProductFactory::new()->make(['name' => 'Test'])->toArray()
        )->assertRedirect(route('bazar.products.show', Product::find(2)));

        $this->assertDatabaseHas('products', ['name' => 'Test']);
    }

    /** @test */
    public function an_admin_can_show_product()
    {
        $this->actingAs($this->user)
            ->get(route('bazar.products.show', $this->product))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(route('bazar.products.show', $this->product))
            ->assertOk()
            ->assertComponent('Products/Show')
            ->assertPropValue('product', function ($product) {
                $this->product->refresh()->loadMissing(['media', 'categories:categories.id,categories.name']);

                $this->assertEquals($product, $this->product->toArray());
            });
    }

    /** @test */
    public function an_admin_can_update_product()
    {
        $this->actingAs($this->user)
            ->patch(route('bazar.products.update', $this->product))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->patch(route('bazar.products.update', $this->product), [])
            ->assertStatus(302)
            ->assertSessionHasErrors();

        $this->actingAs($this->admin)->patch(
            route('bazar.products.update', $this->product),
            array_replace($this->product->toArray(), ['name' => 'Updated'])
        )->assertRedirect(route('bazar.products.show', $this->product));

        $this->assertSame('Updated', $this->product->refresh()->name);
    }

    /** @test */
    public function an_admin_can_destroy_product()
    {
        $this->actingAs($this->user)
            ->delete(route('bazar.products.destroy', $this->product))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->delete(route('bazar.products.destroy', $this->product))
            ->assertStatus(302);

        $this->assertTrue($this->product->fresh()->trashed());

        $this->actingAs($this->admin)
            ->delete(route('bazar.products.destroy', $this->product))
            ->assertStatus(302);

        $this->assertDatabaseMissing('products', ['id' => $this->product->id]);
    }

    /** @test */
    public function an_admin_can_restore_product()
    {
        $this->product->delete();

        $this->actingAs($this->user)
            ->patch(route('bazar.products.restore', $this->product))
            ->assertForbidden();

        $this->assertTrue($this->product->trashed());

        $this->actingAs($this->admin)
            ->patch(route('bazar.products.restore', $this->product))
            ->assertStatus(302);

        $this->assertFalse($this->product->fresh()->trashed());
    }

    /** @test */
    public function an_admin_can_batch_update_products()
    {
        $this->actingAs($this->user)
            ->patch(route('bazar.products.batch-update'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->patch(route('bazar.products.batch-update'), ['ids' => [$this->product->id], 'name' => 'Cat'])
            ->assertStatus(302);

        $this->assertEquals('Cat', $this->product->fresh()->name);
    }

    /** @test */
    public function an_admin_can_batch_destroy_products()
    {
        $this->actingAs($this->user)
            ->delete(route('bazar.products.batch-destroy'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->delete(route('bazar.products.batch-destroy'), ['ids' => [$this->product->id]])
            ->assertStatus(302);

        $this->assertTrue($this->product->fresh()->trashed());

        $this->actingAs($this->admin)
            ->delete(route('bazar.products.batch-destroy', ['force']), ['ids' => [$this->product->id]])
            ->assertStatus(302);

        $this->assertDatabaseMissing('products', ['id' => $this->product->id]);
    }

    /** @test */
    public function an_admin_can_batch_restore_products()
    {
        $this->product->delete();

        $this->actingAs($this->user)
            ->patch(route('bazar.products.batch-restore'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->patch(route('bazar.products.batch-restore'), ['ids' => [$this->product->id]])
            ->assertStatus(302);

        $this->assertFalse($this->product->fresh()->trashed());
    }
}
