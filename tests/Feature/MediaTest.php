<?php

namespace Bazar\Tests\Feature;

use Bazar\Jobs\MoveFile;
use Bazar\Jobs\PerformConversions;
use Bazar\Models\Medium;
use Bazar\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class MediaTest extends TestCase
{
    protected $medium;

    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        Queue::fake();

        $this->medium = Medium::factory()->create();

        Storage::disk($this->medium->disk)->put($this->medium->getPath(), 'fake content');
    }

    /** @test */
    public function an_admin_can_index_media()
    {
        $this->actingAs($this->user)
            ->get(URL::route('bazar.media.index'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(URL::route('bazar.media.index'))
            ->assertOk()
            ->assertJson(Medium::paginate()->toArray());
    }

    /** @test */
    public function an_admin_can_show_medium()
    {
        $this->actingAs($this->user)
            ->get(URL::route('bazar.media.show', $this->medium))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(URL::route('bazar.media.show', $this->medium))
            ->assertOk()
            ->assertJson($this->medium->toArray());
    }

    /** @test */
    public function an_admin_can_store_medium_as_image()
    {
        $this->actingAs($this->user)
            ->post(URL::route('bazar.media.store'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->post(URL::route('bazar.media.store'), [
                'file' => UploadedFile::fake()->image('test.png.chunk'),
            ])
            ->assertCreated()
            ->assertJson(['name' => 'test']);

        $this->assertDatabaseHas('bazar_media', ['name' => 'test']);

        Queue::assertPushedWithChain(MoveFile::class, [
            PerformConversions::class,
        ]);
    }

    /** @test */
    public function an_admin_can_store_medium_as_file()
    {
        $this->actingAs($this->admin)
            ->post(URL::route('bazar.media.store'), [
                'file' => UploadedFile::fake()->create('test.pdf.chunk'),
            ])
            ->assertCreated()
            ->assertJson(['name' => 'test']);

        $this->assertDatabaseHas('bazar_media', ['name' => 'test']);

        Queue::assertPushedWithoutChain(MoveFile::class);
    }

    /** @test */
    public function an_admin_can_update_medium()
    {
        $this->actingAs($this->user)
            ->patch(URL::route('bazar.media.update', $this->medium))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->patch(URL::route('bazar.media.update', $this->medium), [])
            ->assertOk()
            ->assertExactJson(['updated' => true]);
    }

    /** @test */
    public function an_admin_can_destroy_medium()
    {
        Storage::disk($this->medium->disk)->assertExists($this->medium->getPath());

        $this->actingAs($this->user)
            ->delete(URL::route('bazar.media.destroy', $this->medium))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->delete(URL::route('bazar.media.destroy', $this->medium))
            ->assertOk()
            ->assertExactJson(['deleted' => true]);

        Storage::disk($this->medium->disk)->assertMissing($this->medium->getPath());

        $this->assertDatabaseMissing('bazar_media', ['id' => $this->medium->id]);
    }

    /** @test */
    public function an_admin_can_batch_destroy_media()
    {
        Storage::disk($this->medium->disk)->assertExists($this->medium->getPath());

        $this->actingAs($this->user)
            ->delete(URL::route('bazar.media.batch-destroy'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->delete(URL::route('bazar.media.batch-destroy'), ['id' => [$this->medium->id]])
            ->assertOk()
            ->assertExactJson(['deleted' => true]);

        Storage::disk($this->medium->disk)->assertMissing($this->medium->getPath());

        $this->assertDatabaseMissing('bazar_media', ['id' => $this->medium->id]);
    }
}
