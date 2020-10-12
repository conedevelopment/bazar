<?php

namespace Bazar\Tests\Feature;

use Bazar\Database\Factories\ProductFactory;
use Bazar\Database\Factories\VariationFactory;
use Bazar\Models\Variation;
use Bazar\Tests\TestCase;
use Illuminate\Support\Facades\URL;

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
            ->get(URL::route('bazar.products.variations.index', $this->product))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(URL::route('bazar.products.variations.index', $this->product))
            ->assertOk()
            ->assertViewHas('page.props.results');
    }

    /** @test */
    public function an_admin_can_create_variation()
    {
        $this->actingAs($this->user)
            ->get(URL::route('bazar.products.variations.create', $this->product))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(URL::route('bazar.products.variations.create', $this->product))
            ->assertOk()
            ->assertViewHas('page.props.variation');
    }

    /** @test */
    public function an_admin_can_store_variation()
    {
        $this->actingAs($this->user)
            ->post(URL::route('bazar.products.variations.store', $this->product))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->post(URL::route('bazar.products.variations.store', $this->product), [])
            ->assertStatus(302)
            ->assertSessionHasErrors();

        $this->actingAs($this->admin)->post(
            URL::route('bazar.products.variations.store', $this->product),
            VariationFactory::new()->make(['option' => ['Size' => 'M']])->toArray()
        )->assertRedirect(URL::route('bazar.products.variations.show', [$this->product, Variation::find(2)]));

        $this->assertDatabaseHas('variations', ['option->Size' => 'M']);
    }

    /** @test */
    public function an_admin_can_show_variation()
    {
        $this->actingAs($this->user)
            ->get(URL::route('bazar.products.variations.show', [$this->product, $this->variation]))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(URL::route('bazar.products.variations.show', [$this->product, $this->variation]))
            ->assertOk()
            ->assertViewHas('page.props.variation');
    }

    /** @test */
    public function an_admin_can_update_variation()
    {
        $this->actingAs($this->user)
            ->patch(URL::route('bazar.products.variations.update', [$this->product, $this->variation]))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->patch(URL::route('bazar.products.variations.update', [$this->product, $this->variation]), [])
            ->assertStatus(302)
            ->assertSessionHasErrors();

        $this->actingAs($this->admin)->patch(
            URL::route('bazar.products.variations.update', [$this->product, $this->variation]),
            array_replace_recursive($this->variation->toArray(), ['option' => ['Size' => 'L']])
        )->assertRedirect(URL::route('bazar.products.variations.show', [$this->product, $this->variation]));

        $this->assertDatabaseHas('variations', ['option->Size' => 'L']);
    }

    /** @test */
    public function an_admin_can_destroy_variation()
    {
        $this->actingAs($this->user)
            ->delete(URL::route('bazar.products.variations.destroy', [$this->product, $this->variation]))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->delete(URL::route('bazar.products.variations.destroy', [$this->product, $this->variation]))
            ->assertStatus(302);

        $this->assertTrue($this->variation->fresh()->trashed());

        $this->actingAs($this->admin)
            ->delete(URL::route('bazar.products.variations.destroy', [$this->product, $this->variation]))
            ->assertStatus(302);

        $this->assertDatabaseMissing('variations', ['id' => $this->variation->id]);
    }

    /** @test */
    public function an_admin_can_restore_variation()
    {
        $this->variation->delete();

        $this->actingAs($this->user)
            ->patch(URL::route('bazar.products.variations.restore', [$this->product, $this->variation]))
            ->assertForbidden();

        $this->assertTrue($this->variation->trashed());

        $this->actingAs($this->admin)
            ->patch(URL::route('bazar.products.variations.restore', [$this->product, $this->variation]))
            ->assertStatus(302);

        $this->assertFalse($this->variation->fresh()->trashed());
    }

    /** @test */
    public function an_admin_can_batch_update_variations()
    {
        $this->actingAs($this->user)
            ->patch(URL::route('bazar.products.variations.batch-update', $this->product))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->patch(
                URL::route('bazar.products.variations.batch-update', $this->product),
                ['ids' => [$this->variation->id], 'option' => ['Size' => 'L']]
            )->assertStatus(302);

        $this->assertEquals('L', $this->variation->fresh()->option['Size']);
    }

    /** @test */
    public function an_admin_can_batch_destroy_variations()
    {
        $this->actingAs($this->user)
            ->delete(URL::route('bazar.products.variations.batch-destroy', $this->product))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->delete(URL::route('bazar.products.variations.batch-destroy', $this->product), ['ids' => [$this->variation->id]])
            ->assertStatus(302);

        $this->assertTrue($this->variation->fresh()->trashed());

        $this->actingAs($this->admin)
            ->delete(
                URL::route('bazar.products.variations.batch-destroy', [$this->product, 'force']),
                ['ids' => [$this->variation->id]]
            )->assertStatus(302);

        $this->assertDatabaseMissing('variations', ['id' => $this->variation->id]);
    }

    /** @test */
    public function an_admin_can_batch_restore_variations()
    {
        $this->variation->delete();

        $this->actingAs($this->user)
            ->patch(URL::route('bazar.products.variations.batch-restore', $this->product))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->patch(URL::route('bazar.products.variations.batch-restore', $this->product), ['ids' => [$this->variation->id]])
            ->assertStatus(302);

        $this->assertFalse($this->variation->fresh()->trashed());
    }
}
