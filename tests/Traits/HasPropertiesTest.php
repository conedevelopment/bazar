<?php

declare(strict_types=1);

namespace Cone\Bazar\Tests\Traits;

use Cone\Bazar\Models\Product;
use Cone\Bazar\Models\Property;
use Cone\Bazar\Models\PropertyValue;
use Cone\Bazar\Tests\TestCase;

class HasPropertiesTest extends TestCase
{
    protected Product $product;

    protected Property $property;

    protected PropertyValue $propertyValue;

    protected function setUp(): void
    {
        parent::setUp();

        $this->product = Product::factory()->create();
        $this->property = Property::factory()->create(['name' => 'Color', 'slug' => 'color']);
        $this->propertyValue = PropertyValue::factory()->create([
            'property_id' => $this->property->id,
            'value' => 'Red',
        ]);
    }

    public function test_model_has_property_values(): void
    {
        $this->product->propertyValues()->attach($this->propertyValue);

        $this->product->refresh();

        $this->assertTrue($this->product->propertyValues->contains($this->propertyValue));
    }

    public function test_model_has_properties(): void
    {
        $this->product->propertyValues()->attach($this->propertyValue);

        $this->product->refresh();

        $this->assertTrue($this->product->properties->contains($this->property));
    }

    public function test_model_can_have_multiple_property_values(): void
    {
        $value2 = PropertyValue::factory()->create([
            'property_id' => $this->property->id,
            'value' => 'Blue',
        ]);

        $this->product->propertyValues()->attach([$this->propertyValue->id, $value2->id]);

        $this->product->refresh();

        $this->assertCount(2, $this->product->propertyValues);
    }

    public function test_model_property_values_are_morph_to_many(): void
    {
        $this->product->propertyValues()->attach($this->propertyValue);

        $pivot = $this->product->propertyValues()->first()->pivot;

        $this->assertEquals($this->product->id, $pivot->buyable_id);
        $this->assertEquals(Product::class, $pivot->buyable_type);
    }
}
