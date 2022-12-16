<?php

/*
 * This file is part of the zenstruck/class-metadata package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
    public function metadata_generated_for_path(): void
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

        $this->assertNull(Metadata::get(Class1::class, 'key1'));
        $this->assertNull(Metadata::get(new Class1(), 'key1'));
        $this->assertNull(Metadata::get('class1', 'key1'));
        $this->assertSame(2, Metadata::get(Class2::class, 'key2'));
        $this->assertSame(2, Metadata::get(new Class2(), 'key2'));
        $this->assertSame(2, Metadata::get('class2', 'key2'));
        $this->assertNull(Metadata::get(Class2::class, 'invalid'));
        $this->assertNull(Metadata::get(new Class2(), 'invalid'));
        $this->assertNull(Metadata::get('class2', 'invalid'));

        $this->assertNull(Metadata::first(Class1::class, 'key1'));
        $this->assertNull(Metadata::first(new Class1(), 'key1'));
        $this->assertNull(Metadata::first('class1', 'key1', 'foo'));
        $this->assertSame(2, Metadata::first(Class2::class, 'key2'));
        $this->assertSame(2, Metadata::first(new Class2(), 'key2'));
        $this->assertSame(2, Metadata::first('class2', 'key2'));
        $this->assertSame(2, Metadata::first(Class2::class, 'key2', 'invalid'));
        $this->assertSame(2, Metadata::first(new Class2(), 'invalid', 'key2'));
        $this->assertSame(2, Metadata::first('class2', 'invalid1', 'invalid2', 'key2'));
        $this->assertNull(Metadata::first(Class2::class, 'invalid', 'foo'));
        $this->assertNull(Metadata::first(new Class2(), 'invalid', 'foo'));
        $this->assertNull(Metadata::first('class2', 'invalid', 'foo'));

        $this->assertSame([], Metadata::classesWith('foo'));
        $this->assertSame([Class2::class, Class4::class, Class5::class], Metadata::classesWith('key1'));
    }

    /**
     * @test
     */
    public function metadata_generated_for_path_and_mapping_config(): void
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
