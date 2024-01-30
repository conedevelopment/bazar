<?php

namespace Cone\Bazar\Tests\Console;

use Cone\Bazar\Tests\TestCase;
use Illuminate\Support\Facades\Queue;

class CommandsTest extends TestCase
{
    public function test_it_can_clear_carts(): void
    {
        $this->artisan('bazar:clear-carts', ['--all' => true])
            ->expectsOutput('All carts have been deleted.')
            ->assertSuccessful();

        $this->artisan('bazar:clear-carts')
            ->expectsOutput('Expired carts have been deleted.')
            ->assertSuccessful();
    }

    public function test_it_can_install_bazar(): void
    {
        Queue::fake();

        $this->artisan('bazar:install')
            ->assertSuccessful();

        $this->artisan('bazar:install', ['--seed' => true])
            ->assertSuccessful();
    }
}
