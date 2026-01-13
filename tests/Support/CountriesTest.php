<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests\Support;

use Cone\Bazar\Support\Countries;
use Cone\Bazar\Tests\TestCase;

class CountriesTest extends TestCase
{
    public function test_countries_returns_all_african_countries(): void
    {
        $africa = Countries::africa();

        $this->assertIsArray($africa);
        $this->assertNotEmpty($africa);
        $this->assertArrayHasKey('ZA', $africa);
    }

    public function test_countries_returns_all_antarctican_countries(): void
    {
        $antarctica = Countries::antarctica();

        $this->assertIsArray($antarctica);
        $this->assertNotEmpty($antarctica);
        $this->assertArrayHasKey('AQ', $antarctica);
    }

    public function test_countries_returns_all_asian_countries(): void
    {
        $asia = Countries::asia();

        $this->assertIsArray($asia);
        $this->assertNotEmpty($asia);
        $this->assertArrayHasKey('JP', $asia);
    }

    public function test_countries_returns_all_european_countries(): void
    {
        $europe = Countries::europe();

        $this->assertIsArray($europe);
        $this->assertNotEmpty($europe);
        $this->assertArrayHasKey('GB', $europe);
    }

    public function test_countries_returns_all_north_american_countries(): void
    {
        $northAmerica = Countries::northAmerica();

        $this->assertIsArray($northAmerica);
        $this->assertNotEmpty($northAmerica);
        $this->assertArrayHasKey('US', $northAmerica);
    }

    public function test_countries_returns_all_south_american_countries(): void
    {
        $southAmerica = Countries::southAmerica();

        $this->assertIsArray($southAmerica);
        $this->assertNotEmpty($southAmerica);
        $this->assertArrayHasKey('BR', $southAmerica);
    }

    public function test_countries_returns_all_oceanian_countries(): void
    {
        $oceania = Countries::oceania();

        $this->assertIsArray($oceania);
        $this->assertNotEmpty($oceania);
        $this->assertArrayHasKey('AU', $oceania);
    }

    public function test_countries_returns_all_countries(): void
    {
        $all = Countries::all();

        $this->assertIsArray($all);
        $this->assertNotEmpty($all);
        $this->assertGreaterThan(200, count($all));
    }

    public function test_countries_returns_all_countries_by_continent(): void
    {
        $byContinent = Countries::allByContient();

        $this->assertIsArray($byContinent);
        $this->assertNotEmpty($byContinent);
        $this->assertArrayHasKey('Africa', $byContinent);
        $this->assertArrayHasKey('Europe', $byContinent);
    }

    public function test_countries_returns_country_name_by_code(): void
    {
        $name = Countries::name('US');

        $this->assertIsString($name);
        $this->assertNotEmpty($name);
    }

    public function test_countries_returns_code_when_country_not_found(): void
    {
        $name = Countries::name('XX');

        $this->assertSame('XX', $name);
    }
}
