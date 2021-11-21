<?php

namespace Cone\Bazar\Tests\Feature;

use Cone\Bazar\Tests\TestCase;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;

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
    public function it_can_install_bazar()
    {
        Queue::fake();

        $this->artisan('bazar:install')
            ->assertExitCode(Command::SUCCESS);

        $this->artisan('bazar:install', ['--seed' => true])
            ->assertExitCode(Command::SUCCESS);
    }

    /** @test */
    public function it_can_publish_assets()
    {
        $this->artisan('bazar:publish')
            ->assertExitCode(Command::SUCCESS);

        $this->artisan('bazar:publish', ['--mix' => true])
            ->assertExitCode(Command::SUCCESS);

        $script = file_get_contents(__DIR__.'/../../resources/stubs/webpack.mix.js');

        $this->assertTrue(
            Str::contains(file_get_contents(App::basePath('webpack.mix.js')), $script)
        );
    }
}
