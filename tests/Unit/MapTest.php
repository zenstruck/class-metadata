<?php

namespace Zenstruck\Metadata\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Zenstruck\Metadata\Map;
use Zenstruck\Metadata\Tests\Fixture\Classes\Class2;
use Zenstruck\Metadata\Tests\Fixture\Classes\Class4;
use Zenstruck\Metadata\Tests\Fixture\Classes\Sub\Class5;
use Zenstruck\Metadata\Tests\InteractsWithGeneratedMap;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class MapTest extends TestCase
{
    use InteractsWithGeneratedMap;

    /**
     * @test
     */
    public function can_list_classes(): void
    {
        self::generateMap();

        $this->assertSame([Class2::class, Class5::class, Class4::class], Map::classes());
    }

    /**
     * @test
     */
    public function does_not_duplicate_classes_for_metadata_key(): void
    {
        self::generateMap(mapping: [
            Class2::class => ['key1' => 'override'],
        ]);

        $this->assertSame([Class2::class, Class5::class, Class4::class], Map::classes());
    }
}
