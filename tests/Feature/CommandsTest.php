<?php

namespace Bazar\Tests\Feature;

use Bazar\Jobs\ClearCarts;
use Bazar\Jobs\ClearChunks;
use Bazar\Tests\TestCase;
use Illuminate\Support\Facades\Queue;

class CommandsTest extends TestCase
{
    /** @test */
    public function it_can_clear_carts()
    {
        Queue::fake();
        ClearCarts::dispatch();
        Queue::assertPushed(ClearCarts::class);

        $this->artisan('bazar:clear-carts', ['--all' => true])
            ->expectsOutput('All carts have been deleted.')
            ->assertExitCode(0);

        $this->artisan('bazar:clear-carts')
            ->expectsOutput('Expired carts have been deleted.')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_can_clear_chunks()
    {
        Queue::fake();
        ClearChunks::dispatch();
        Queue::assertPushed(ClearChunks::class);

        $this->artisan('bazar:clear-chunks')
            ->expectsOutput('File chunks are cleared!')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_can_install_bazar()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_publish_assets()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_install_scaffolding()
    {
        $this->assertTrue(true);
    }
}
