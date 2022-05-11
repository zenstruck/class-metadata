<?php

namespace Zenstruck;

use Zenstruck\Metadata\Map;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class Alias
{
    public function __construct(
        /** @readonly */
        public string $value
    ) {
    }

    /**
     * Retrieve the alias for a class/object or null if none.
     *
     * @param object|class-string $objectOrClass
     */
    public static function for(object|string $objectOrClass): ?string
    {
        return Map::aliasFor(\is_object($objectOrClass) ? $objectOrClass::class : $objectOrClass);
    }

    /**
     * Retrieve the class for an alias or null if none.
     *
     * @return ?class-string
     */
    public static function classFor(string $alias): ?string
    {
        return Map::classFor($alias);
    }
}
