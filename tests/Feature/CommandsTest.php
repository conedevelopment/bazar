<?php

namespace Bazar\Tests\Feature;

use Bazar\Tests\TestCase;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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
        Storage::disk('local')->put(
            'chunks/test.chunk',
            UploadedFile::fake()->create('test.chunk')
        );

        $this->artisan('bazar:clear-chunks')
            ->expectsOutput('File chunks are cleared!')
            ->assertExitCode(Command::SUCCESS);
    }

    /** @test */
    public function it_can_install_bazar()
    {
        $this->artisan('bazar:install')
            ->assertExitCode(Command::SUCCESS);
    }

    /** @test */
    public function it_can_install_bazar_with_seed()
    {
        $this->artisan('bazar:install', ['--seed' => true])
            ->assertExitCode(Command::SUCCESS);
    }

    /** @test */
    public function it_can_publish_assets()
    {
        $this->artisan('bazar:publish')
            ->assertExitCode(Command::SUCCESS);
    }
}
