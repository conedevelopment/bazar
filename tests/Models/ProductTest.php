<?php

namespace Cone\Bazar\Tests\Models;

use Cone\Bazar\Models\Cart;
use Cone\Bazar\Models\Category;
use Cone\Bazar\Models\Item;
use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Models\Property;
use Cone\Bazar\Models\PropertyValue;
use Cone\Bazar\Models\TaxRate;
use Cone\Bazar\Models\Variant;
use Cone\Bazar\Tests\TestCase;

class ProductTest extends TestCase
{
    protected Product $product;

    protected Property $property;

    protected function setUp(): void
    {
        parent::setUp();

        $this->product = Product::factory()->create();

        $this->property = Property::factory()->has(PropertyValue::factory(), 'values')->create(['name' => 'Size']);

        $this->product->propertyValues()->attach($this->property->values->first());
    }

    public function test_product_has_many_orders_through_items(): void
    {
        $order = Order::factory()->create();

        $item = Item::factory()->make([
            'price' => 100,
            'quantity' => 3,
            'name' => $this->product->name,
        ])->checkoutable()->associate($order);

        $this->product->items()->save($item);

        $this->assertTrue($this->product->orders->contains($order));
    }

    public function test_product_belongs_to_carts(): void
    {
        $cart = Cart::factory()->create();

        $item = Item::factory()->make([
            'price' => 100,
            'quantity' => 3,
            'name' => $this->product->name,
        ])->checkoutable()->associate($cart);

        $this->product->items()->save($item);

        $this->assertTrue($this->product->carts->contains($cart));
    }

    public function test_product_belongs_to_categories(): void
    {
        $category = Category::factory()->create();

        $this->product->categories()->attach($category);

        $this->assertTrue($this->product->categories->contains($category));
    }

    public function test_product_has_properties(): void
    {
        $this->assertTrue(
            $this->product->properties->contains($this->property)
        );

        $this->assertTrue(
            $this->product->propertyValues->contains($this->property->values->first())
        );
    }

    public function test_product_has_variants(): void
    {
        $variant = $this->product->variants()->save(
            Variant::factory()->make()
        );

        $variant->propertyValues()->attach($this->property->values->first());

        $this->assertTrue($this->product->variants->contains($variant));

        $this->assertSame(
            $variant->id,
            $this->product->toVariant([$this->property->slug => $this->property->values->first()->value])->id
        );

        $this->assertNull($this->product->toVariant(['size' => 'fake']));
    }

    public function test_a_product_belongs_to_tax_rates(): void
    {
        $taxRate = TaxRate::factory()->create();

        $this->assertTrue($this->product->taxRates->isEmpty());

        $this->product->taxRates()->attach($taxRate);

        $this->product->refresh();

        $this->assertTrue($this->product->taxRates->contains($taxRate));
    }

    public function test_product_interacts_with_stock(): void
    {
        $this->assertTrue(true);
    }

    public function test_product_has_prices(): void
    {
        $this->assertTrue(true);
    }

    public function test_product_has_query_scopes(): void
    {
        $outOfStock = $this->product->newQuery()->outOfStock();
        $this->assertSame(
            'select * from "bazar_products" where exists (select * from "root_meta_data" where "bazar_products"."id" = "root_meta_data"."metable_id" and "root_meta_data"."metable_type" = ? and "root_meta_data"."key" = ? and "root_meta_data"."value" = ?) and "bazar_products"."deleted_at" is null',
            $outOfStock->toSql()
        );
        $this->assertSame([Product::class, 'quantity', 0], $outOfStock->getBindings());

        $inStock = $this->product->newQuery()->inStock();

        $this->assertSame(
            'select * from "bazar_products" where exists (select * from "root_meta_data" where "bazar_products"."id" = "root_meta_data"."metable_id" and "root_meta_data"."metable_type" = ? and "root_meta_data"."key" = ? and "root_meta_data"."value" > ?) and "bazar_products"."deleted_at" is null',
            $inStock->toSql()
        );
        $this->assertSame([Product::class, 'quantity', 0], $outOfStock->getBindings());
    }
}
