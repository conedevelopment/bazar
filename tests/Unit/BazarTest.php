<?php

namespace Bazar\Tests\Unit;

use Bazar\Bazar;
use Bazar\Exceptions\InvalidCurrencyException;
use Bazar\Tests\TestCase;
use Illuminate\Http\UploadedFile;

class BazarTest extends TestCase
{
    /** @test */
    public function bazar_has_version()
    {
        $this->assertSame(Bazar::VERSION, Bazar::version());
    }

    /** @test */
    public function bazar_has_asset_version()
    {
        $this->assertNull(Bazar::assetVersion());

        $file = UploadedFile::fake()->createWithContent('mix.manifest.json', 'fake-content');

        $this->assertSame(
            md5('fake-content'), Bazar::assetVersion($file->getRealPath())
        );
    }

    /** @test */
    public function bazar_has_currencies()
    {
        $this->assertSame(
            config('bazar.currencies.available'), Bazar::currencies()
        );
    }

    /** @test */
    public function bazar_can_set_or_get_currency()
    {
        $this->assertSame(config('bazar.currencies.default'), Bazar::currency());

        $this->expectException(InvalidCurrencyException::class);
        Bazar::currency('fake');

        Bazar::currency('eur');
        $this->assertSame('eur', Bazar::currency());
    }
}
