<?php

namespace Bazar\Tests\Feature;

use Bazar\Database\Factories\CategoryFactory;
use Bazar\Models\Category;
use Bazar\Tests\TestCase;
use Illuminate\Support\Facades\URL;

class CategoriesTest extends TestCase
{
    protected $category;

    public function setUp(): void
    {
        parent::setUp();

        $this->category = CategoryFactory::new()->create();
    }

    /** @test */
    public function an_admin_can_index_categories()
    {
        $this->actingAs($this->user)
            ->get(URL::route('bazar.categories.index'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(URL::route('bazar.categories.index'))
            ->assertOk()
            ->assertViewHas(
                'page.props.results',
                Category::query()->with('media')->paginate()->toArray()
            );
    }

    /** @test */
    public function an_admin_can_create_category()
    {
        $this->actingAs($this->user)
            ->get(URL::route('bazar.categories.create'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(URL::route('bazar.categories.create'))
            ->assertOk()
            ->assertViewHas('page.props.category');
    }

    /** @test */
    public function an_admin_can_store_category()
    {
        $this->actingAs($this->user)
            ->post(URL::route('bazar.categories.store'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->post(URL::route('bazar.categories.store'), [])
            ->assertStatus(302)
            ->assertSessionHasErrors();

        $this->actingAs($this->admin)->post(
            URL::route('bazar.categories.store'),
            CategoryFactory::new()->make(['name' => 'Test'])->toArray()
        )->assertRedirect(URL::route('bazar.categories.show', Category::find(2)));

        $this->assertDatabaseHas('categories', ['name' => 'Test']);
    }

    /** @test */
    public function an_admin_can_show_category()
    {
        $this->actingAs($this->user)
            ->get(URL::route('bazar.categories.show', $this->category))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(URL::route('bazar.categories.show', $this->category))
            ->assertOk()
            ->assertViewHas(
                'page.props.category',
                $this->category->refresh()->loadMissing('media')->toArray()
            );
    }

    /** @test */
    public function an_admin_can_update_category()
    {
        $this->actingAs($this->user)
            ->patch(URL::route('bazar.categories.update', $this->category))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->patch(URL::route('bazar.categories.update', $this->category), [])
            ->assertStatus(302)
            ->assertSessionHasErrors();

        $this->actingAs($this->admin)->patch(
            URL::route('bazar.categories.update', $this->category),
            array_replace($this->category->toArray(), ['name' => 'Updated'])
        )->assertRedirect(URL::route('bazar.categories.show', $this->category));

        $this->assertSame('Updated', $this->category->refresh()->name);
    }

    /** @test */
    public function an_admin_can_destroy_category()
    {
        $this->actingAs($this->user)
            ->delete(URL::route('bazar.categories.destroy', $this->category))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->delete(URL::route('bazar.categories.destroy', $this->category))
            ->assertStatus(302);

        $this->assertTrue($this->category->fresh()->trashed());

        $this->actingAs($this->admin)
            ->delete(URL::route('bazar.categories.destroy', $this->category))
            ->assertStatus(302);

        $this->assertDatabaseMissing('categories', ['id' => $this->category->id]);
    }

    /** @test */
    public function an_admin_can_restore_category()
    {
        $this->category->delete();

        $this->actingAs($this->user)
            ->patch(URL::route('bazar.categories.restore', $this->category))
            ->assertForbidden();

        $this->assertTrue($this->category->trashed());

        $this->actingAs($this->admin)
            ->patch(URL::route('bazar.categories.restore', $this->category))
            ->assertStatus(302);

        $this->assertFalse($this->category->fresh()->trashed());
    }

    /** @test */
    public function an_admin_can_batch_update_categories()
    {
        $this->actingAs($this->user)
            ->patch(URL::route('bazar.categories.batch-update'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->patch(URL::route('bazar.categories.batch-update'), ['ids' => [$this->category->id], 'name' => 'Updated'])
            ->assertStatus(302);

        $this->assertEquals('Updated', $this->category->fresh()->name);
    }

    /** @test */
    public function an_admin_can_batch_destroy_categories()
    {
        $this->actingAs($this->user)
            ->delete(URL::route('bazar.categories.batch-destroy'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->delete(URL::route('bazar.categories.batch-destroy'), ['ids' => [$this->category->id]])
            ->assertStatus(302);

        $this->assertTrue($this->category->fresh()->trashed());

        $this->actingAs($this->admin)
            ->delete(URL::route('bazar.categories.batch-destroy', ['force']), ['ids' => [$this->category->id]])
            ->assertStatus(302);

        $this->assertDatabaseMissing('categories', ['id' => $this->category->id]);
    }

    /** @test */
    public function an_admin_can_batch_restore_categories()
    {
        $this->category->delete();

        $this->actingAs($this->user)
            ->patch(URL::route('bazar.categories.batch-restore'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->patch(URL::route('bazar.categories.batch-restore'), ['ids' => [$this->category->id]])
            ->assertStatus(302);

        $this->assertFalse($this->category->fresh()->trashed());
    }
}
