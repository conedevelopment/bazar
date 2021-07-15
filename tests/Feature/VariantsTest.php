<?php

namespace Cone\Bazar\Tests\Feature;

use Cone\Bazar\Models\Product;
use Cone\Bazar\Models\Variant;
use Cone\Bazar\Tests\TestCase;
use Illuminate\Support\Facades\URL;

class VariantsTest extends TestCase
{
    protected $product, $variant;

    public function setUp(): void
    {
        parent::setUp();

        $this->product = Product::factory()->create();
        $this->variant = $this->product->variants()->save(Variant::factory()->make());
    }

    /** @test */
    public function an_admin_can_index_variants()
    {
        $this->actingAs($this->user)
            ->get(URL::route('bazar.products.variants.index', $this->product))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(URL::route('bazar.products.variants.index', $this->product))
            ->assertOk()
            ->assertViewHas('page.props.response');
    }

    /** @test */
    public function an_admin_can_create_variant()
    {
        $this->actingAs($this->user)
            ->get(URL::route('bazar.products.variants.create', $this->product))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(URL::route('bazar.products.variants.create', $this->product))
            ->assertOk()
            ->assertViewHas('page.props.variant');
    }

    /** @test */
    public function an_admin_can_store_variant()
    {
        $this->actingAs($this->user)
            ->post(URL::route('bazar.products.variants.store', $this->product))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->post(URL::route('bazar.products.variants.store', $this->product), [])
            ->assertStatus(302)
            ->assertSessionHasErrors();

        $this->actingAs($this->admin)->post(
            URL::route('bazar.products.variants.store', $this->product),
            Variant::factory()->make(['variation' => ['Size' => 'M']])->toArray()
        )->assertRedirect(URL::route('bazar.products.variants.show', [$this->product, Variant::find(2)]));

        $this->assertDatabaseHas('bazar_variants', ['variation->Size' => 'M']);
    }

    /** @test */
    public function an_admin_can_show_variant()
    {
        $this->actingAs($this->user)
            ->get(URL::route('bazar.products.variants.show', [$this->product, $this->variant]))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(URL::route('bazar.products.variants.show', [$this->product, $this->variant]))
            ->assertOk()
            ->assertViewHas('page.props.variant');
    }

    /** @test */
    public function an_admin_can_update_variant()
    {
        $this->actingAs($this->user)
            ->patch(URL::route('bazar.products.variants.update', [$this->product, $this->variant]))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->patch(URL::route('bazar.products.variants.update', [$this->product, $this->variant]), [])
            ->assertStatus(302)
            ->assertSessionHasErrors();

        $this->actingAs($this->admin)->patch(
            URL::route('bazar.products.variants.update', [$this->product, $this->variant]),
            array_replace_recursive($this->variant->toArray(), ['variation' => ['Size' => 'L']])
        )->assertRedirect(URL::route('bazar.products.variants.show', [$this->product, $this->variant]));

        $this->assertDatabaseHas('bazar_variants', ['variation->Size' => 'L']);
    }

    /** @test */
    public function an_admin_can_destroy_variant()
    {
        $this->actingAs($this->user)
            ->delete(URL::route('bazar.products.variants.destroy', [$this->product, $this->variant]))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->delete(URL::route('bazar.products.variants.destroy', [$this->product, $this->variant]))
            ->assertStatus(302);

        $this->assertTrue($this->variant->fresh()->trashed());

        $this->actingAs($this->admin)
            ->delete(URL::route('bazar.products.variants.destroy', [$this->product, $this->variant]))
            ->assertStatus(302);

        $this->assertDatabaseMissing('bazar_variants', ['id' => $this->variant->id]);
    }

    /** @test */
    public function an_admin_can_restore_variant()
    {
        $this->variant->delete();

        $this->actingAs($this->user)
            ->patch(URL::route('bazar.products.variants.restore', [$this->product, $this->variant]))
            ->assertForbidden();

        $this->assertTrue($this->variant->trashed());

        $this->actingAs($this->admin)
            ->patch(URL::route('bazar.products.variants.restore', [$this->product, $this->variant]))
            ->assertStatus(302);

        $this->assertFalse($this->variant->fresh()->trashed());
    }

    /** @test */
    public function an_admin_can_batch_update_variants()
    {
        $this->actingAs($this->user)
            ->patch(URL::route('bazar.products.variants.batch-update', $this->product))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->patch(
                URL::route('bazar.products.variants.batch-update', $this->product),
                ['id' => [$this->variant->id], 'variation' => ['Size' => 'L']]
            )->assertStatus(302);

        $this->assertEquals('L', $this->variant->fresh()->variation['Size']);
    }

    /** @test */
    public function an_admin_can_batch_destroy_variants()
    {
        $this->actingAs($this->user)
            ->delete(URL::route('bazar.products.variants.batch-destroy', $this->product))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->delete(URL::route('bazar.products.variants.batch-destroy', $this->product), ['id' => [$this->variant->id]])
            ->assertStatus(302);

        $this->assertTrue($this->variant->fresh()->trashed());

        $this->actingAs($this->admin)
            ->delete(
                URL::route('bazar.products.variants.batch-destroy', [$this->product, 'force']),
                ['id' => [$this->variant->id]]
            )->assertStatus(302);

        $this->assertDatabaseMissing('bazar_variants', ['id' => $this->variant->id]);
    }

    /** @test */
    public function an_admin_can_batch_restore_variants()
    {
        $this->variant->delete();

        $this->actingAs($this->user)
            ->patch(URL::route('bazar.products.variants.batch-restore', $this->product))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->patch(URL::route('bazar.products.variants.batch-restore', $this->product), ['id' => [$this->variant->id]])
            ->assertStatus(302);

        $this->assertFalse($this->variant->fresh()->trashed());
    }
}
