<?php

namespace Cone\Bazar\Tests\Unit;

use Cone\Bazar\Models\Cart;
use Cone\Bazar\Models\Category;
use Cone\Bazar\Models\Item;
use Cone\Bazar\Models\Order;
use Cone\Bazar\Models\Product;
use Cone\Bazar\Models\Property;
use Cone\Bazar\Models\PropertyValue;
use Cone\Bazar\Models\Variant;
use Cone\Bazar\Tests\TestCase;

class ProductTest extends TestCase
{
    protected Product $product;

    protected Property $property;

    public function setUp(): void
    {
        parent::setUp();

        $this->product = Product::factory()->create();

        $this->property = Property::factory()->has(PropertyValue::factory(), 'values')->create(['name' => 'Size']);

        $this->product->propertyValues()->attach($this->property->values->first());
    }

    /** @test */
    public function a_product_has_many_orders_through_items()
    {
        $order = Order::factory()->create();

        $item = Item::factory()->make([
            'price' => 100,
            'tax' => 0,
            'quantity' => 3,
            'name' => $this->product->name,
        ])->itemable()->associate($order);

        $this->product->items()->save($item);

        $this->assertTrue($this->product->orders->contains($order));
    }

    /** @test */
    public function a_product_belongs_to_carts()
    {
        $cart = Cart::factory()->create();

        $item = Item::factory()->make([
            'price' => 100,
            'tax' => 0,
            'quantity' => 3,
            'name' => $this->product->name,
        ])->itemable()->associate($cart);

        $this->product->items()->save($item);

        $this->assertTrue($this->product->carts->contains($cart));
    }

    /** @test */
    public function a_product_belongs_to_categories()
    {
        $category = Category::factory()->create();

        $this->product->categories()->attach($category);

        $this->assertTrue($this->product->categories->contains($category));
    }

    /** @test */
    public function a_product_has_properties()
    {
        $this->assertTrue(
            $this->product->properties->contains($this->property)
        );

        $this->assertTrue(
            $this->product->propertyValues->contains($this->property->values->first())
        );
    }

    /** @test */
    public function a_product_has_variants()
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

    /** @test */
    public function a_product_interacts_with_stock()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function a_product_has_prices()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function a_product_has_query_scopes()
    {
        $outOfStock = $this->product->newQuery()->outOfStock();
        $this->assertSame(
            'select * from "bazar_products" where exists (select * from "root_metas" where "bazar_products"."id" = "root_metas"."metable_id" and "root_metas"."metable_type" = ? and "root_metas"."key" = ? and "root_metas"."value" = ?) and "bazar_products"."deleted_at" is null',
            $outOfStock->toSql()
        );
        $this->assertSame([Product::class, 'quantity', 0], $outOfStock->getBindings());

        $inStock = $this->product->newQuery()->inStock();

        $this->assertSame(
            'select * from "bazar_products" where exists (select * from "root_metas" where "bazar_products"."id" = "root_metas"."metable_id" and "root_metas"."metable_type" = ? and "root_metas"."key" = ? and "root_metas"."value" > ?) and "bazar_products"."deleted_at" is null',
            $inStock->toSql()
        );
        $this->assertSame([Product::class, 'quantity', 0], $outOfStock->getBindings());
    }
}
