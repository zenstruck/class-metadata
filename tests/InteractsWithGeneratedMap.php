<?php

namespace Zenstruck\Metadata\Tests;

use Zenstruck\Metadata\MapGenerator;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @runTestsInSeparateProcesses
 */
trait InteractsWithGeneratedMap
{
    /**
     * @before
     * @after
     */
    public static function removeGeneratedMap(): void
    {
        MapGenerator::removeFile();
    }

    private static function generateMap(array $paths = [__DIR__.'/Fixture/Classes'], array $mapping = []): void
    {
        MapGenerator::generate($paths, $mapping);
    }
}
