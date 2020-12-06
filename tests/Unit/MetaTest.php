<?php

namespace Bazar\Tests\Unit;

use Bazar\Database\Factories\MetaFactory;
use Bazar\Database\Factories\ProductFactory;
use Bazar\Tests\TestCase;
use DateTimeInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Carbon;
use JsonSerializable;
use Serializable;
use stdClass;

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
    public function it_casts_date_values()
    {
        $meta = MetaFactory::new()->make(['value' => $value = Carbon::now()]);
        $this->assertSame('date', $meta->type);
        $this->assertInstanceOf(DateTimeInterface::class, $meta->value);
        $this->assertSame($value->format('Y-m-d H:i:s'), $meta->getRaw());
    }

    /** @test */
    public function it_casts_array_values()
    {
        $meta = MetaFactory::new()->make(['value' => $value = [1, 2, 3]]);
        $this->assertSame('array', $meta->type);
        $this->assertSame($value, $meta->value);
        $this->assertSame(json_encode($value), $meta->getRaw());

        $meta = MetaFactory::new()->make(['value' => $value = new ArrayableValue]);
        $this->assertSame('array', $meta->type);
        $this->assertSame($value->toArray(), $meta->value);
        $this->assertSame(json_encode($value->toArray()), $meta->getRaw());

        $meta = MetaFactory::new()->make(['value' => $value = new JsonSerializableValue]);
        $this->assertSame('array', $meta->type);
        $this->assertSame($value->jsonSerialize(), $meta->value);
        $this->assertSame(json_encode($value), $meta->getRaw());
    }

    /** @test */
    public function it_casts_json_values()
    {
        $meta = MetaFactory::new()->make(['value' => $value = new JsonableValue]);
        $this->assertSame('json', $meta->type);
        $this->assertSame(json_decode($value->toJson(), true), $meta->value);
        $this->assertSame($value->toJson(), $meta->getRaw());
    }

    /** @test */
    public function it_casts_serializable_values()
    {
        $meta = MetaFactory::new()->make(['value' => $value = new SerializableValue]);
        $this->assertSame('serializable', $meta->type);
        $serialized = serialize($value);
        $this->assertEquals(unserialize($serialized), $meta->value);
        $this->assertSame($serialized, $meta->getRaw());
    }

    /** @test */
    public function it_casts_object_values()
    {
        $meta = MetaFactory::new()->make(['value' => $value = new stdClass]);
        $this->assertSame('object', $meta->type);
        $this->assertEquals($value, $meta->value);
        $this->assertSame(json_encode($value), $meta->getRaw());
    }

    /** @test */
    public function it_casts_numeric_values()
    {
        $meta = MetaFactory::new()->make(['value' => $value = 1]);
        $this->assertSame('int', $meta->type);
        $this->assertSame($value, $meta->value);
        $this->assertSame($value, $meta->getRaw());

        $meta = MetaFactory::new()->make(['value' => $value = 1.00]);
        $this->assertSame('float', $meta->type);
        $this->assertSame($value, $meta->value);
        $this->assertSame($value, $meta->getRaw());
    }

    /** @test */
    public function it_casts_bool_values()
    {
        $meta = MetaFactory::new()->make(['value' => $value = true]);
        $this->assertSame('bool', $meta->type);
        $this->assertSame($value, $meta->value);
        $this->assertSame(1, $meta->getRaw());

        $meta = MetaFactory::new()->make(['value' => $value = false]);
        $this->assertSame('bool', $meta->type);
        $this->assertSame($value, $meta->value);
        $this->assertSame(0, $meta->getRaw());
    }

    /** @test */
    public function it_casts_string_values()
    {
        $meta = MetaFactory::new()->make(['value' => $value = 'foo']);
        $this->assertSame('string', $meta->type);
        $this->assertSame($value, $meta->value);
        $this->assertSame($value, $meta->getRaw());
    }

    /** @test */
    public function it_casts_null_values()
    {
        $meta = MetaFactory::new()->make(['value' => $value = null]);
        $this->assertSame(null, $meta->type);
        $this->assertSame($value, $meta->value);
        $this->assertSame($value, $meta->getRaw());
    }
}

class JsonSerializableValue implements JsonSerializable
{
    public function jsonSerialize()
    {
        return ['foo' => 'bar'];
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

class SerializableValue implements Serializable
{
    public $data = ['foo' => 'bar'];

    public function serialize()
    {
        return serialize($this->data);
    }

    public function unserialize($data)
    {
        $this->data = unserialize($data);
    }
}
