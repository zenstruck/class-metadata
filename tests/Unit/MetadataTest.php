<?php

namespace Zenstruck\Metadata\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Zenstruck\Metadata;
use Zenstruck\Metadata\Tests\Fixture\Classes\Class1;
use Zenstruck\Metadata\Tests\Fixture\Classes\Class2;
use Zenstruck\Metadata\Tests\Fixture\Classes\Class4;
use Zenstruck\Metadata\Tests\Fixture\Classes\Sub\Class5;
use Zenstruck\Metadata\Tests\InteractsWithGeneratedMap;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class MetadataTest extends TestCase
{
    use InteractsWithGeneratedMap;

    /**
     * @test
     */
    public function metadata_generated_for_namespace(): void
    {
        self::generateMap();

        $this->assertSame([], Metadata::for(Class1::class));
        $this->assertSame([], Metadata::for(new Class1()));
        $this->assertSame([], Metadata::for('class1'));
        $this->assertSame(['key1' => 'class2-value1', 'key2' => 2], Metadata::for(Class2::class));
        $this->assertSame(['key1' => 'class2-value1', 'key2' => 2], Metadata::for(new Class2()));
        $this->assertSame(['key1' => 'class2-value1', 'key2' => 2], Metadata::for('class2'));
        $this->assertSame(['key1' => 'class4-value1'], Metadata::for(Class4::class));
        $this->assertSame(['key1' => 'class5-value1'], Metadata::for(Class5::class));

        $this->assertSame([], Metadata::classesWith('foo'));
        $this->assertSame([Class2::class, Class4::class, Class5::class], Metadata::classesWith('key1'));
    }

    /**
     * @test
     */
    public function metadata_generated_for_namespace_and_mapping_config(): void
    {
        self::generateMap(mapping: [
            Class1::class => ['alias' => 'class1', 'key1' => 'class1-value1'],
            Class2::class => ['key1' => 'override-value', 'key3' => 3],
        ]);

        $this->assertSame(['key1' => 'class1-value1'], Metadata::for(Class1::class));
        $this->assertSame(['key1' => 'class1-value1'], Metadata::for(new Class1()));
        $this->assertSame(['key1' => 'class1-value1'], Metadata::for('class1'));
        $this->assertSame(['key1' => 'override-value', 'key2' => 2, 'key3' => 3], Metadata::for(Class2::class));
        $this->assertSame(['key1' => 'override-value', 'key2' => 2, 'key3' => 3], Metadata::for(new Class2()));
        $this->assertSame(['key1' => 'override-value', 'key2' => 2, 'key3' => 3], Metadata::for('class2'));
        $this->assertSame(['key1' => 'class4-value1'], Metadata::for(Class4::class));
        $this->assertSame(['key1' => 'class5-value1'], Metadata::for(Class5::class));

        $this->assertSame([], Metadata::classesWith('foo'));
        $this->assertSame([Class2::class, Class4::class, Class5::class, Class1::class], Metadata::classesWith('key1'));
    }

    /**
     * @test
     */
    public function mapping_config_class_must_exist(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot map metadata for "invalid" - this class does not exist.');

        self::generateMap(mapping: [
            'invalid' => ['alias' => 'class1', 'key1' => 'class1-value1'],
        ]);
    }

    /**
     * @test
     */
    public function mapping_config_metadata_must_be_scalar(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf('Metadata values must be scalar, "null" given for "key1" on class "%s".', Class1::class));

        self::generateMap(mapping: [
            Class1::class => ['key1' => null],
        ]);
    }
}
