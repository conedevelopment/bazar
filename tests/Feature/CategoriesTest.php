<?php

namespace Bazar\Tests\Feature;

use Bazar\Models\Category;
use Bazar\Tests\TestCase;

class CategoriesTest extends TestCase
{
    protected $category;

    public function setUp(): void
    {
        parent::setUp();

        $this->category = factory(Category::class)->create();
    }

    /** @test */
    public function an_admin_can_index_categories()
    {
        $this->actingAs($this->user)
            ->get(route('bazar.categories.index'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(route('bazar.categories.index'))
            ->assertOk()
            ->assertComponent('Categories/Index')
            ->assertPropValue('results', function ($results) {
                $this->assertEquals(
                    $results, Category::with('media')->paginate(25)->toArray()
                );
            });
    }

    /** @test */
    public function an_admin_can_create_category()
    {
        $this->actingAs($this->user)
            ->get(route('bazar.categories.create'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(route('bazar.categories.create'))
            ->assertOk()
            ->assertComponent('Categories/Create');
    }

    /** @test */
    public function an_admin_can_store_category()
    {
        $this->actingAs($this->user)
            ->post(route('bazar.categories.store'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->post(route('bazar.categories.store'), [])
            ->assertStatus(302)
            ->assertSessionHasErrors();

        $this->actingAs($this->admin)->post(
            route('bazar.categories.store'),
            factory(Category::class)->make(['name' => 'Test'])->toArray()
        )->assertRedirect(route('bazar.categories.show', Category::find(2)));

        $this->assertDatabaseHas('categories', ['name' => 'Test']);
    }

    /** @test */
    public function an_admin_can_show_category()
    {
        $this->actingAs($this->user)
            ->get(route('bazar.categories.show', $this->category))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(route('bazar.categories.show', $this->category))
            ->assertOk()
            ->assertComponent('Categories/Show')
            ->assertPropValue('category', function ($category) {
                $this->category->refresh()->loadMissing('media');

                $this->assertEquals($category, $this->category->toArray());
            });
    }

    /** @test */
    public function an_admin_can_update_category()
    {
        $this->actingAs($this->user)
            ->patch(route('bazar.categories.update', $this->category))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->patch(route('bazar.categories.update', $this->category), [])
            ->assertStatus(302)
            ->assertSessionHasErrors();

        $this->actingAs($this->admin)->patch(
            route('bazar.categories.update', $this->category),
            array_replace($this->category->toArray(), ['name' => 'Updated'])
        )->assertRedirect(route('bazar.categories.show', $this->category));

        $this->assertSame('Updated', $this->category->refresh()->name);
    }

    /** @test */
    public function an_admin_can_destroy_category()
    {
        $this->actingAs($this->user)
            ->delete(route('bazar.categories.destroy', $this->category))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->delete(route('bazar.categories.destroy', $this->category))
            ->assertStatus(302);

        $this->assertTrue($this->category->fresh()->trashed());

        $this->actingAs($this->admin)
            ->delete(route('bazar.categories.destroy', $this->category))
            ->assertStatus(302);

        $this->assertDatabaseMissing('categories', ['id' => $this->category->id]);
    }

    /** @test */
    public function an_admin_can_restore_category()
    {
        $this->category->delete();

        $this->actingAs($this->user)
            ->patch(route('bazar.categories.restore', $this->category))
            ->assertForbidden();

        $this->assertTrue($this->category->trashed());

        $this->actingAs($this->admin)
            ->patch(route('bazar.categories.restore', $this->category))
            ->assertStatus(302);

        $this->assertFalse($this->category->fresh()->trashed());
    }

    /** @test */
    public function an_admin_can_batch_update_categories()
    {
        $this->actingAs($this->user)
            ->patch(route('bazar.categories.batch-update'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->patch(route('bazar.categories.batch-update'), ['ids' => [$this->category->id], 'name' => 'Updated'])
            ->assertStatus(302);

        $this->assertEquals('Updated', $this->category->fresh()->name);
    }

    /** @test */
    public function an_admin_can_batch_destroy_categories()
    {
        $this->actingAs($this->user)
            ->delete(route('bazar.categories.batch-destroy'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->delete(route('bazar.categories.batch-destroy'), ['ids' => [$this->category->id]])
            ->assertStatus(302);

        $this->assertTrue($this->category->fresh()->trashed());

        $this->actingAs($this->admin)
            ->delete(route('bazar.categories.batch-destroy', ['force']), ['ids' => [$this->category->id]])
            ->assertStatus(302);

        $this->assertDatabaseMissing('categories', ['id' => $this->category->id]);
    }

    /** @test */
    public function an_admin_can_batch_restore_categories()
    {
        $this->category->delete();

        $this->actingAs($this->user)
            ->patch(route('bazar.categories.batch-restore'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->patch(route('bazar.categories.batch-restore'), ['ids' => [$this->category->id]])
            ->assertStatus(302);

        $this->assertFalse($this->category->fresh()->trashed());
    }
}
