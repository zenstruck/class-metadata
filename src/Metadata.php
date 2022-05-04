<?php

namespace Zenstruck;

use Zenstruck\Metadata\Map;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class Metadata
{
    public function __construct(
        /** @readonly */
        public string $key,

        /** @readonly */
        public string|bool|int|float $value,
    ) {
    }

    /**
     * @param object|class-string|string $objectOrClassOrAlias
     *
     * @return array<string,scalar>
     */
    public function for(object|string $objectOrClassOrAlias): array
    {
        return Map::metadataFor($objectOrClassOrAlias);
    }

    /**
     * @return class-string[]
     */
    public function classesWith(string $key): array
    {
        return Map::classesWith($key);
    }
}
