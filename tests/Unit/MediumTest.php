<?php

namespace Bazar\Tests\Unit;

use Bazar\Models\Medium;
use Bazar\Support\Facades\Conversion;
use Bazar\Tests\TestCase;
use Illuminate\Support\Facades\Storage;

class MediumTest extends TestCase
{
    protected $medium;

    public function setUp(): void
    {
        parent::setUp();

        $this->medium = Medium::factory()->create();

        $this->app['config']->set('filesystems.disks.fake', [
            'driver' => 'local',
            'root' => Storage::disk('local')->getAdapter()->getPathPrefix(),
        ]);
    }

    /** @test */
    public function it_can_perform_conversions()
    {
        Conversion::partialMock()
                ->shouldReceive('perform')
                ->once()
                ->andReturn($this->medium);

        $medium = $this->medium->convert();

        $this->assertSame($medium, $this->medium);
    }

    /** @test */
    public function it_can_determine_if_image()
    {
        $this->medium->update(['mime_type' => 'image/jpg']);
        $this->assertTrue($this->medium->isImage);

        $this->medium->update(['mime_type' => 'application/pdf']);
        $this->assertFalse($this->medium->isImage);
    }

    /** @test */
    public function it_has_urls()
    {
        $this->assertEquals(
            $this->medium->isImage ? ['original', 'thumb', 'medium'] : ['original'],
            array_keys($this->medium->urls)
        );

        $this->assertStringContainsString('-thumb', $this->medium->getUrl('thumb'));
    }

    /** @test */
    public function it_has_path()
    {
        $this->assertStringContainsString("{$this->medium->id}/{$this->medium->name}", $this->medium->getPath());
        $this->assertStringContainsString("{$this->medium->id}/{$this->medium->name}", $this->medium->getFullPath());

        $this->medium->disk = 'fake';

        $this->assertSame($this->medium->getUrl(), $this->medium->getFullPath());
    }

    /** @test */
    public function it_has_query_scopes()
    {
        $this->assertSame(
            $this->medium->newQuery()->where('bazar_media.name', 'like', 'test%')->toSql(),
            $this->medium->newQuery()->search('test')->toSql()
        );

        $this->assertSame(
            $this->medium->newQuery()->where('bazar_media.mime_type', 'not like', 'image%')->toSql(),
            $this->medium->newQuery()->type('file')->toSql()
        );

        $this->assertSame(
            $this->medium->newQuery()->where('bazar_media.mime_type', 'like', 'image%')->toSql(),
            $this->medium->newQuery()->type('image')->toSql()
        );

        $this->assertSame(
            $this->medium->newQuery()->toSql(),
            $this->medium->newQuery()->type('fake')->toSql()
        );
    }
}
