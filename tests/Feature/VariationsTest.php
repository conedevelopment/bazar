<?php

namespace Bazar\Tests\Feature;

use Bazar\Database\Factories\ProductFactory;
use Bazar\Database\Factories\VariationFactory;
use Bazar\Models\Variation;
use Bazar\Tests\TestCase;

class VariationsTest extends TestCase
{
    protected $product, $variation;

    public function setUp(): void
    {
        parent::setUp();

        $this->product = ProductFactory::new()->create();
        $this->variation = $this->product->variations()->save(VariationFactory::new()->make());
    }

    /** @test */
    public function an_admin_can_index_variations()
    {
        $this->actingAs($this->user)
            ->get(route('bazar.products.variations.index', $this->product))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(route('bazar.products.variations.index', $this->product))
            ->assertOk()
            ->assertComponent('Variations/Index')
            ->assertPropValue('results', function ($results) {
                $variations = $this->product->refresh()->variations()->with('media')->paginate(25);

                $variations->getCollection()->each(function (Variation $variation) {
                    $variation->setRelation('product', $this->product->withoutRelations()->makeHidden('variations'));
                });

                $this->assertEquals($results, $variations->toArray());
            });
    }

    /** @test */
    public function an_admin_can_create_variation()
    {
        $this->actingAs($this->user)
            ->get(route('bazar.products.variations.create', $this->product))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(route('bazar.products.variations.create', $this->product))
            ->assertOk()
            ->assertComponent('Variations/Create');
    }

    /** @test */
    public function an_admin_can_store_variation()
    {
        $this->actingAs($this->user)
            ->post(route('bazar.products.variations.store', $this->product))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->post(route('bazar.products.variations.store', $this->product), [])
            ->assertStatus(302)
            ->assertSessionHasErrors();

        $this->actingAs($this->admin)->post(
            route('bazar.products.variations.store', $this->product),
            VariationFactory::new()->make(['option' => ['Size' => 'M']])->toArray()
        )->assertRedirect(route('bazar.products.variations.show', [$this->product, Variation::find(2)]));

        $this->assertDatabaseHas('variations', ['option->Size' => 'M']);
    }

    /** @test */
    public function an_admin_can_show_variation()
    {
        $this->actingAs($this->user)
            ->get(route('bazar.products.variations.show', [$this->product, $this->variation]))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(route('bazar.products.variations.show', [$this->product, $this->variation]))
            ->assertOk()
            ->assertComponent('Variations/Show')
            ->assertPropValue('variation', function ($variation) {
                $this->variation->refresh()->setRelation(
                    'product', $this->product->refresh()->withoutRelations()->makeHidden('variation')
                )->loadMissing('media');

                $this->assertEquals($variation, $this->variation->toArray());
            });
    }

    /** @test */
    public function an_admin_can_update_variation()
    {
        $this->actingAs($this->user)
            ->patch(route('bazar.products.variations.update', [$this->product, $this->variation]))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->patch(route('bazar.products.variations.update', [$this->product, $this->variation]), [])
            ->assertStatus(302)
            ->assertSessionHasErrors();

        $this->actingAs($this->admin)->patch(
            route('bazar.products.variations.update', [$this->product, $this->variation]),
            array_replace_recursive($this->variation->toArray(), ['option' => ['Size' => 'L']])
        )->assertRedirect(route('bazar.products.variations.show', [$this->product, $this->variation]));

        $this->assertDatabaseHas('variations', ['option->Size' => 'L']);
    }

    /** @test */
    public function an_admin_can_destroy_variation()
    {
        $this->actingAs($this->user)
            ->delete(route('bazar.products.variations.destroy', [$this->product, $this->variation]))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->delete(route('bazar.products.variations.destroy', [$this->product, $this->variation]))
            ->assertStatus(302);

        $this->assertTrue($this->variation->fresh()->trashed());

        $this->actingAs($this->admin)
            ->delete(route('bazar.products.variations.destroy', [$this->product, $this->variation]))
            ->assertStatus(302);

        $this->assertDatabaseMissing('variations', ['id' => $this->variation->id]);
    }

    /** @test */
    public function an_admin_can_restore_variation()
    {
        $this->variation->delete();

        $this->actingAs($this->user)
            ->patch(route('bazar.products.variations.restore', [$this->product, $this->variation]))
            ->assertForbidden();

        $this->assertTrue($this->variation->trashed());

        $this->actingAs($this->admin)
            ->patch(route('bazar.products.variations.restore', [$this->product, $this->variation]))
            ->assertStatus(302);

        $this->assertFalse($this->variation->fresh()->trashed());
    }

    /** @test */
    public function an_admin_can_batch_update_variations()
    {
        $this->actingAs($this->user)
            ->patch(route('bazar.products.variations.batch-update', $this->product))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->patch(
                route('bazar.products.variations.batch-update', $this->product),
                ['ids' => [$this->variation->id], 'option' => ['Size' => 'L']]
            )->assertStatus(302);

        $this->assertEquals('L', $this->variation->fresh()->option['Size']);
    }

    /** @test */
    public function an_admin_can_batch_destroy_variations()
    {
        $this->actingAs($this->user)
            ->delete(route('bazar.products.variations.batch-destroy', $this->product))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->delete(route('bazar.products.variations.batch-destroy', $this->product), ['ids' => [$this->variation->id]])
            ->assertStatus(302);

        $this->assertTrue($this->variation->fresh()->trashed());

        $this->actingAs($this->admin)
            ->delete(
                route('bazar.products.variations.batch-destroy', [$this->product, 'force']),
                ['ids' => [$this->variation->id]]
            )->assertStatus(302);

        $this->assertDatabaseMissing('variations', ['id' => $this->variation->id]);
    }

    /** @test */
    public function an_admin_can_batch_restore_variations()
    {
        $this->variation->delete();

        $this->actingAs($this->user)
            ->patch(route('bazar.products.variations.batch-restore', $this->product))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->patch(route('bazar.products.variations.batch-restore', $this->product), ['ids' => [$this->variation->id]])
            ->assertStatus(302);

        $this->assertFalse($this->variation->fresh()->trashed());
    }
}
