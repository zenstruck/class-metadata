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
use Zenstruck\Alias;
use Zenstruck\Metadata\Tests\Fixture\Classes\Class1;
use Zenstruck\Metadata\Tests\Fixture\Classes\Class2;
use Zenstruck\Metadata\Tests\Fixture\Classes\Class3;
use Zenstruck\Metadata\Tests\Fixture\Classes\Sub\Class5;
use Zenstruck\Metadata\Tests\InteractsWithGeneratedMap;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class AliasTest extends TestCase
{
    use InteractsWithGeneratedMap;

    /**
     * @test
     */
    public function alias_for_generated_from_path(): void
    {
        self::generateMap();

        $this->assertNull(Alias::for(Class1::class));
        $this->assertNull(Alias::for(new Class1()));
        $this->assertSame('class2', Alias::for(Class2::class));
        $this->assertSame('class2', Alias::for(new Class2()));
        $this->assertNull(Alias::for(Class3::class));
        $this->assertNull(Alias::for(new Class3()));
        $this->assertSame('class5', Alias::for(Class5::class));
        $this->assertSame('class5', Alias::for(new Class5()));

        $this->assertNull(Alias::classFor('class1'));
        $this->assertSame(Class2::class, Alias::classFor('class2'));
        $this->assertNull(Alias::classFor('class3'));
        $this->assertSame(Class5::class, Alias::classFor('class5'));
    }

    /**
     * @test
     * @dataProvider validConfigMappingProvider
     */
    public function alias_class_for_generated_from_path_and_mapping_config(array $mapping): void
    {
        self::generateMap(mapping: $mapping);

        $this->assertSame('class1', Alias::for(Class1::class));
        $this->assertSame('class1', Alias::for(new Class1()));
        $this->assertSame('class2', Alias::for(Class2::class));
        $this->assertSame('class2', Alias::for(new Class2()));
        $this->assertNull(Alias::for(Class3::class));
        $this->assertNull(Alias::for(new Class3()));
        $this->assertSame('class5', Alias::for(Class5::class));
        $this->assertSame('class5', Alias::for(new Class5()));

        $this->assertSame(Class1::class, Alias::classFor('class1'));
        $this->assertSame(Class2::class, Alias::classFor('class2'));
        $this->assertNull(Alias::classFor('class3'));
        $this->assertSame(Class5::class, Alias::classFor('class5'));
    }

    public static function validConfigMappingProvider(): iterable
    {
        yield [[Class1::class => 'class1']];
        yield [[Class1::class => ['alias' => 'class1']]];
    }

    /**
     * @test
     */
    public function mapping_config_alias_must_be_string(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf('Alias for "%s" must be a string. %s (%s) given.', Class1::class, 6, 'int'));

        self::generateMap(mapping: [Class1::class => ['alias' => 6]]);
    }

    /**
     * @test
     */
    public function cannot_get_alias_if_map_not_generated(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('You must run composer dump-autoload with plugins enabled');

        Alias::for(Class2::class);
    }

    /**
     * @test
     */
    public function cannot_duplicate_class_alias(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(\sprintf('Alias "class1" for class "%s" is already used by "%s".', Class2::class, Class1::class));

        self::generateMap([], [Class1::class => 'class1', Class2::class => 'class1']);
    }

    /**
     * @test
     */
    public function cannot_duplicate_alias_class(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(\sprintf('Class "%s" is already aliased with "class2".', Class2::class));

        self::generateMap(mapping: [Class2::class => 'class1']);
    }

    /**
     * @test
     */
    public function alias_cannot_be_empty(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(\sprintf('Alias cannot be blank for class "%s".', Class1::class));

        self::generateMap([], [Class1::class => '  ']);
    }
}
