<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests\Models;

use Cone\Bazar\Models\Product;
use Cone\Bazar\Models\Property;
use Cone\Bazar\Models\PropertyValue;
use Cone\Bazar\Tests\TestCase;

class PropertyTest extends TestCase
{
    protected Property $property;

    protected function setUp(): void
    {
        parent::setUp();

        $this->property = Property::factory()->create(['name' => 'Size', 'slug' => 'size']);
    }

    public function test_property_has_values(): void
    {
        $value = PropertyValue::factory()->create([
            'property_id' => $this->property->id,
            'value' => 'L',
        ]);

        $this->assertTrue($this->property->values->contains($value));
    }

    public function test_property_deletes_values_on_delete(): void
    {
        $value = PropertyValue::factory()->create([
            'property_id' => $this->property->id,
            'value' => 'M',
        ]);

        $valueId = $value->id;

        $this->property->delete();

        $this->assertNull(PropertyValue::find($valueId));
    }

    public function test_property_has_fillable_attributes(): void
    {
        $this->property->fill([
            'name' => 'Color',
            'slug' => 'color',
            'description' => 'Product color',
        ]);

        $this->assertSame('Color', $this->property->name);
        $this->assertSame('color', $this->property->slug);
        $this->assertSame('Product color', $this->property->description);
    }
}
