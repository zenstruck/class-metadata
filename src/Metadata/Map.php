<?php

namespace Zenstruck\Metadata;

use Zenstruck\Alias;
use Zenstruck\Metadata;

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

    /** @var array<string,class-string[]> */
    public array $metadataKeyToClassMap = [];

    /**
     * @param class-string $class
     */
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

        return \array_values(\array_unique([ // @phpstan-ignore-line
            ...\array_keys(GeneratedMap::CLASS_TO_ALIAS), // @phpstan-ignore-line
            ...\array_filter(\array_keys(GeneratedMap::METADATA_MAP), static fn(string $v) => \class_exists($v)), // @phpstan-ignore-line
        ]));
    }

    /**
     * @param class-string                $class
     * @param string|array<string,scalar> $config
     */
    public function addFromConfig(string $class, string|array $config): void
    {
        $alias = \is_string($config) ? $config : $config['alias'] ?? null;

        if (null !== $alias && !\is_string($alias)) {
            throw new \InvalidArgumentException(\sprintf('Alias for "%s" must be a string. %s (%s) given.', $class, $alias, \get_debug_type($alias)));
        }

        if ($alias) {
            $this->addAlias($class, $alias);
        }

        if (!\is_array($config)) {
            return;
        }

        unset($config['alias']);

        foreach ($config as $key => $value) {
            try {
                $this->addMetadata($class, $alias, new Metadata($key, $value));
            } catch (\TypeError) {
                throw new \InvalidArgumentException(\sprintf('Metadata values must be scalar, "%s" given for "%s" on class "%s".', \get_debug_type($value), $key, $class));
            }
        }
    }

    /**
     * @param class-string $class
     */
    public function addFromClass(string $class): void
    {
        $refClass = new \ReflectionClass($class);

        if ($alias = ($refClass->getAttributes(Alias::class)[0] ?? null)?->newInstance()->value) {
            $this->addAlias($class, $alias);
        }

        foreach ($refClass->getAttributes(Metadata::class) as $attribute) {
            $this->addMetadata($class, $alias, $attribute->newInstance());
        }
    }

    private static function ensureMapGenerated(): void
    {
        if (!\file_exists(MapGenerator::FILE)) {
            throw new \RuntimeException('You must run composer dump-autoload with plugins enabled to generate the required metadata map.');
        }
    }

    /**
     * @param class-string $class
     */
    private function addAlias(string $class, string $alias): void
    {
        if (isset($this->aliasToClassMap[$alias])) {
            throw new \LogicException(\sprintf('Alias "%s" for class "%s" is already used by "%s".', $alias, $class, $this->aliasToClassMap[$alias]));
        }

        if (isset($this->classToAliasMap[$class])) {
            throw new \LogicException(\sprintf('Class "%s" is already aliased with "%s".', $class, $this->classToAliasMap[$class]));
        }

        if (empty(\trim($alias))) {
            throw new \LogicException(\sprintf('Alias cannot be blank for class "%s".', $class));
        }

        $this->classToAliasMap[$class] = $alias;
        $this->aliasToClassMap[$alias] = $class;
    }

    /**
     * @param class-string $class
     */
    private function addMetadata(string $class, ?string $alias, Metadata $metadata): void
    {
        $this->metadataMap[$class][$metadata->key] = $metadata->value;

        if (!$alias) {
            $alias = $this->classToAliasMap[$class] ?? null;
        }

        if ($alias) {
            $this->metadataMap[$alias][$metadata->key] = $metadata->value;
        }

        $this->metadataKeyToClassMap[$metadata->key][] = $class;

        // ensure no duplicates
        $this->metadataKeyToClassMap[$metadata->key] = \array_unique($this->metadataKeyToClassMap[$metadata->key]);
    }
}
