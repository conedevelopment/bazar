<?php

namespace Bazar\Tests\Unit;

use Bazar\Database\Factories\MediumFactory;
use Bazar\Tests\TestCase;

class MediumTest extends TestCase
{
    protected $medium;

    public function setUp(): void
    {
        parent::setUp();

        $this->medium = MediumFactory::new()->create();
    }

    /** @test */
    public function a_medium_can_determine_if_image()
    {
        $this->medium->update(['mime_type' => 'image/jpg']);
        $this->assertTrue($this->medium->isImage);

        $this->medium->update(['mime_type' => 'application/pdf']);
        $this->assertFalse($this->medium->isImage);
    }

    /** @test */
    public function a_medium_has_urls()
    {
        $this->assertEquals(
            $this->medium->isImage ? ['full', 'thumb', 'medium'] : ['full'],
            array_keys($this->medium->urls)
        );

        $this->assertStringContainsString('-thumb.', $this->medium->url('thumb'));
    }
}
