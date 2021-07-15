<?php

namespace Cone\Bazar\Tests\Unit;

use Cone\Bazar\Support\Facades\Menu;
use Cone\Bazar\Tests\TestCase;

class MenuRepositoryTest extends TestCase
{
    /** @test */
    public function it_can_register_menu_items()
    {
        $this->assertArrayNotHasKey('Test', Menu::items());

        Menu::register('fake/url', 'Fake Label', [
            'group' => 'Test',
            'icon' => 'fake',
        ]);

        $this->assertEquals([
            'fake/url' => [
                'items' => [],
                'icon' => 'fake',
                'group' => 'Test',
                'label' => 'Fake Label',
            ],
        ], Menu::items()['Test']);
    }

    /** @test */
    public function it_can_register_resource_menu_items()
    {
        $this->assertArrayNotHasKey('Test', Menu::items());

        Menu::resource('fake/resource', 'Resources', [
            'group' => 'Test',
        ]);

        $this->assertEquals([
            'fake/resource' => [
                'items' => [
                    'fake/resource' => 'All Resources',
                    'fake/resource/create' => 'Create Resource',
                ],
                'icon' => 'dashboard',
                'group' => 'Test',
                'label' => 'Resources',
            ],
        ], Menu::items()['Test']);
    }
}
