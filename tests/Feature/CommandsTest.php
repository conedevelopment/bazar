<?php

namespace Bazar\Tests\Feature;

use Bazar\Tests\TestCase;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
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

        $this->artisan('bazar:install', ['--seed' => true])
            ->assertExitCode(Command::SUCCESS);
    }

    /** @test */
    public function it_can_publish_assets()
    {
        $this->artisan('bazar:publish')
            ->assertExitCode(Command::SUCCESS);

        $this->artisan('bazar:publish', ['--packages' => true])
            ->assertExitCode(Command::SUCCESS);

        $bazarPackages = json_decode(file_get_contents(__DIR__.'/../../package.json'), true);
        $packages = json_decode(file_get_contents(App::basePath('package.json')), true);

        $this->assertEmpty(array_diff_key($bazarPackages['devDependencies'], $packages['devDependencies']));

        $this->artisan('bazar:publish', ['--mix' => true])
            ->assertExitCode(Command::SUCCESS);

        $script = file_get_contents(__DIR__.'/../../resources/stubs/webpack.mix.js');

        $this->assertTrue(
            Str::contains(file_get_contents(App::basePath('webpack.mix.js')), $script)
        );
    }
}
