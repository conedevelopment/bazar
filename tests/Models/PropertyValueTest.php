<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests\Models;

use Cone\Bazar\Models\Property;
use Cone\Bazar\Models\PropertyValue;
use Cone\Bazar\Tests\TestCase;

class PropertyValueTest extends TestCase
{
    protected PropertyValue $propertyValue;

    protected Property $property;

    protected function setUp(): void
    {
        parent::setUp();

        $this->property = Property::factory()->create(['name' => 'Size']);

        $this->propertyValue = PropertyValue::factory()->create([
            'property_id' => $this->property->id,
            'value' => 'Large',
            'name' => 'L',
        ]);
    }

    public function test_property_value_belongs_to_property(): void
    {
        $this->assertInstanceOf(Property::class, $this->propertyValue->property);
        $this->assertSame($this->property->id, $this->propertyValue->property->id);
    }

    public function test_property_value_has_fillable_attributes(): void
    {
        $this->propertyValue->fill([
            'name' => 'XL',
            'value' => 'Extra Large',
        ]);

        $this->assertSame('XL', $this->propertyValue->name);
        $this->assertSame('Extra Large', $this->propertyValue->value);
    }
}
