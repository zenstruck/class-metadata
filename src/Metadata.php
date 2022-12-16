<?php

/*
 * This file is part of the zenstruck/class-metadata package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     * Retrieve the metadata for a class/object/alias.
     *
     * @param object|class-string|string $objectOrClassOrAlias
     *
     * @return array<string,scalar>
     */
    public static function for(object|string $objectOrClassOrAlias): array
    {
        return Map::metadataFor($objectOrClassOrAlias);
    }

    /**
     * Retrieve a specific metadata item by key for a class/object/alias
     * or null if key does not exist.
     *
     * @param object|class-string|string $objectOrClassOrAlias
     */
    public static function get(object|string $objectOrClassOrAlias, string $key): string|bool|int|float|null
    {
        return self::for($objectOrClassOrAlias)[$key] ?? null;
    }

    /**
     * Retrieve the first matching metadata item for a list of keys for a
     * class/object/alias or null if no keys exist.
     *
     * @param object|class-string|string $objectOrClassOrAlias
     */
    public static function first(object|string $objectOrClassOrAlias, string ...$keys): string|bool|int|float|null
    {
        $metadata = self::for($objectOrClassOrAlias);

        foreach ($keys as $key) {
            if (isset($metadata[$key])) {
                return $metadata[$key];
            }
        }

        return null;
    }

    /**
     * Retrieve all classes with a metadata key.
     *
     * @return class-string[]
     */
    public static function classesWith(string $key): array
    {
        return Map::classesWith($key);
    }
}
