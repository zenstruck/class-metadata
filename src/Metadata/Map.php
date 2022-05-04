<?php

namespace Zenstruck\Metadata;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
final class Map
{
    /** @var array<class-string,string> */
    public array $classToAliasMap = [];

    /** @var array<string,class-string> */
    public array $aliasToClassMap = [];

    /** @var array<string|class-string,array<string,scalar>> */
    public array $metadataMap = [];

    /** @var array<string,class-string> */
    public array $metadataKeyToClassMap = [];

    public static function aliasFor(string $class): ?string
    {
        self::ensureMapGenerated();

        return GeneratedMap::CLASS_TO_ALIAS[$class] ?? null; // @phpstan-ignore-line
    }

    /**
     * @return ?class-string
     */
    public static function classFor(string $alias): ?string
    {
        self::ensureMapGenerated();

        return GeneratedMap::ALIAS_TO_CLASS[$alias] ?? null; // @phpstan-ignore-line
    }

    /**
     * @return array<string,scalar>
     */
    public static function metadataFor(object|string $value): array
    {
        self::ensureMapGenerated();

        if (\is_object($value)) {
            $value = $value::class;
        }

        return GeneratedMap::METADATA_MAP[$value] ?? []; // @phpstan-ignore-line
    }

    /**
     * @return class-string[]
     */
    public static function classesWith(string $key): array
    {
        self::ensureMapGenerated();

        return GeneratedMap::METADATA_KEY_TO_CLASS[$key] ?? []; // @phpstan-ignore-line
    }

    /**
     * @return class-string[]
     */
    public static function classes(): array
    {
        self::ensureMapGenerated();

        return \array_unique([
            ...\array_keys(GeneratedMap::CLASS_TO_ALIAS),
            ...\array_filter(\array_keys(GeneratedMap::METADATA_MAP), static fn(string $v) => \class_exists($v)),
        ]);
    }

    private static function ensureMapGenerated(): void
    {
        if (!\class_exists(GeneratedMap::class)) {
            throw new \RuntimeException('You must run composer dump-autoload with plugins enabled to generate the required metadata map.');
        }
    }
}
