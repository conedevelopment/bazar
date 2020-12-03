<?php

namespace Bazar\Tests\Unit;

use Bazar\Database\Factories\MetaFactory;
use Bazar\Database\Factories\ProductFactory;
use Bazar\Tests\TestCase;
use DateTimeInterface;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Carbon;

class MetaTest extends TestCase
{
    protected $meta, $product;

    public function setUp(): void
    {
        parent::setUp();

        $this->product = ProductFactory::new()->create();
        $this->meta = MetaFactory::new()->make([
            'key' => 'test',
            'value' => 'fake',
        ]);
        $this->meta->parent()->associate($this->product)->save();
    }

    /** @test */
    public function it_morphs_parent()
    {
        $this->assertSame(
            [$this->product->id, get_class($this->product)],
            [$this->meta->parent_id, $this->meta->parent_type]
        );
    }

    /** @test */
    public function it_auto_casts_value()
    {
        $dateMeta = $this->product->metas()->create([
            'key' => 'date', 'value' => $date = Carbon::now(),
        ]);
        $this->assertDatabaseHas('bazar_metas', ['key' => 'date', 'value' => $date->format('Y-m-d H:i:s')]);
        $this->assertInstanceOf(DateTimeInterface::class, $dateMeta->value);

        $intMeta = $this->product->metas()->create([
            'key' => 'int', 'value' => 1,
        ]);
        $this->assertDatabaseHas('bazar_metas', ['key' => 'int', 'value' => 1]);
        $this->assertSame(1, $intMeta->value);

        $floatMeta = $this->product->metas()->create([
            'key' => 'float', 'value' => 1.01,
        ]);
        $this->assertDatabaseHas('bazar_metas', ['key' => 'float', 'value' => 1.01]);
        $this->assertSame(1.01, $floatMeta->value);

        $arrayMeta = $this->product->metas()->create([
            'key' => 'array', 'value' => ['foo' => 'bar'],
        ]);
        $this->assertDatabaseHas('bazar_metas', ['key' => 'array', 'value' => json_encode(['foo' => 'bar'])]);
        $this->assertSame(['foo' => 'bar'], $arrayMeta->value);

        $arrayableMeta = $this->product->metas()->create([
            'key' => 'arrayable', 'value' => $arrayable = new ArrayableValue,
        ]);
        $this->assertDatabaseHas('bazar_metas', ['key' => 'arrayable', 'value' => json_encode($arrayable->toArray())]);
        $this->assertSame(['foo' => 'bar'], $arrayableMeta->value);

        $jsonMeta = $this->product->metas()->create([
            'key' => 'json', 'value' => $json = new JsonableValue,
        ]);
        $this->assertDatabaseHas('bazar_metas', ['key' => 'json', 'value' => $json->toJson()]);
        $this->assertSame(['foo' => 'bar'], $jsonMeta->value);

        $stringMeta = $this->product->metas()->create([
            'key' => 'string', 'value' => 'string',
        ]);
        $this->assertDatabaseHas('bazar_metas', ['key' => 'string', 'value' => 'string']);
        $this->assertSame('string', $stringMeta->value);

        $nullMeta = $this->product->metas()->create([
            'key' => 'null', 'value' => null,
        ]);
        $this->assertDatabaseHas('bazar_metas', ['key' => 'null', 'value' => null]);
        $this->assertNull($nullMeta->value);
    }

    /** @test */
    public function it_casts_its_value_by_the_saved_type()
    {
        $jsonMeta = $this->product->metas()->create([
            'key' => 'json', 'value' => ['foo' => 'bar'], 'type' => 'json',
        ]);
        $this->assertDatabaseHas('bazar_metas', ['key' => 'json', 'value' => json_encode(['foo' => 'bar'])]);
        $this->assertSame(['foo' => 'bar'], $jsonMeta->value);

        $stringMeta = $this->product->metas()->create([
            'key' => 'string', 'value' => ['foo' => 'bar'], 'type' => 'string',
        ]);
        $this->assertDatabaseHas('bazar_metas', ['key' => 'string', 'value' => json_encode(['foo' => 'bar'])]);
        $this->assertSame(json_encode(['foo' => 'bar']), $stringMeta->value);

        $customCastedMeta = $this->product->metas()->create([
            'key' => 'custom', 'value' => 1, 'type' => CustomCast::class,
        ]);

        $this->assertDatabaseHas('bazar_metas', ['key' => 'custom', 'value' => 1.00]);
        $this->assertSame(1.00, $customCastedMeta->value);
    }
}

class ArrayableValue implements Arrayable
{
    public function toArray()
    {
        return ['foo' => 'bar'];
    }
}

class JsonableValue implements Jsonable
{
    public function toJson($options = 0)
    {
        return json_encode(['foo' => 'bar'], $options);
    }
}

class CustomCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        return (float) $value;
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return (float) $value;
    }
}
