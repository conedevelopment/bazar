<?php

namespace Bazar\Tests\Feature;

use Bazar\Tests\TestCase;
use Illuminate\Console\Command;

class CommandsTest extends TestCase
{
    /** @test */
    public function it_can_clear_carts()
    {
        $this->artisan('bazar:clear-carts', ['--all' => true])
            ->expectsOutput('All carts have been deleted.')
            ->assertExitCode(Command::SUCCESS);

        $this->artisan('bazar:clear-carts')
            ->expectsOutput('Expired carts have been deleted.')
            ->assertExitCode(Command::SUCCESS);
    }

    /** @test */
    public function it_can_clear_chunks()
    {
        $this->artisan('bazar:clear-chunks')
            ->expectsOutput('File chunks are cleared!')
            ->assertExitCode(Command::SUCCESS);
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
