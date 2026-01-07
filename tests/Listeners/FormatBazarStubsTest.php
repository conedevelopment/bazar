<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests\Listeners;

use Cone\Bazar\Listeners\FormatBazarStubs;
use Cone\Bazar\Tests\TestCase;
use Illuminate\Foundation\Events\VendorTagPublished;
use Illuminate\Support\Facades\File;

class FormatBazarStubsTest extends TestCase
{
    protected FormatBazarStubs $listener;

    protected function setUp(): void
    {
        parent::setUp();

        $this->listener = new FormatBazarStubs();
    }

    public function test_listener_formats_bazar_stubs(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'stub_');
        file_put_contents($tempFile, 'namespace {{ namespace }};');

        $event = new VendorTagPublished('bazar-stubs', [$tempFile => $tempFile]);

        $this->listener->handle($event);

        $contents = file_get_contents($tempFile);

        $this->assertStringNotContainsString('{{ namespace }}', $contents);
        $this->assertStringContainsString($this->app->getNamespace(), $contents);

        unlink($tempFile);
    }

    public function test_listener_ignores_non_bazar_stubs_tags(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'stub_');
        file_put_contents($tempFile, 'namespace {{ namespace }};');

        $event = new VendorTagPublished('other-tag', [$tempFile => $tempFile]);

        $this->listener->handle($event);

        $contents = file_get_contents($tempFile);

        $this->assertStringContainsString('{{ namespace }}', $contents);

        unlink($tempFile);
    }
}
