<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests\Http\Controllers;

use Cone\Bazar\Http\Controllers\Controller;
use Cone\Bazar\Tests\TestCase;

class ControllerTest extends TestCase
{
    public function test_controller_extends_base_controller(): void
    {
        $controller = new ConcreteController();

        $this->assertInstanceOf(\Illuminate\Routing\Controller::class, $controller);
    }

    public function test_controller_can_be_instantiated(): void
    {
        $controller = new ConcreteController();

        $this->assertInstanceOf(Controller::class, $controller);
    }
}

class ConcreteController extends Controller
{
    //
}
