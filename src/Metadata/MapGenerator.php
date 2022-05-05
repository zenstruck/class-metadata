<?php

namespace Zenstruck\Metadata;

use HaydenPierce\ClassFinder\ClassFinder;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
final class MapGenerator
{
    private const FILE = __DIR__.'/GeneratedMap.php';
    private const TEMPLATE = <<<EOT
        <?php

        namespace Zenstruck\\Metadata;

        /**
         * @generated by zenstruck/class-alias
         *
         * @internal
         */
        final class GeneratedMap
        {
            public const CLASS_TO_ALIAS = %s;
            public const ALIAS_TO_CLASS = %s;
            public const METADATA_MAP = %s;
            public const METADATA_KEY_TO_CLASS = %s;
        }

        EOT;

    /**
     * @param array<string,string>                            $namespaces
     * @param array<class-string,string|array<string,scalar>> $mapping
     */
    public static function generate(array $namespaces, array $mapping): void
    {
        $map = new Map();

        foreach ($mapping as $class => $config) {
            $map->addFromConfig($class, $config);
        }

        foreach ($namespaces as $namespace) {
            foreach (ClassFinder::getClassesInNamespace(\trim($namespace, '\\'), ClassFinder::RECURSIVE_MODE) as $class) {
                $map->addFromClass($class); // @phpstan-ignore-line
            }
        }

        self::createFile($map);
    }

    public static function generateStub(): void
    {
        self::createFile(new Map());
    }

    public static function removeFile(): void
    {
        if (\file_exists(self::FILE)) {
            \unlink(self::FILE);
        }
    }

    private static function createFile(Map $map): void
    {
        \file_put_contents(self::FILE, \sprintf(
            self::TEMPLATE,
            \var_export($map->classToAliasMap, true),
            \var_export($map->aliasToClassMap, true),
            \var_export($map->metadataMap, true),
            \var_export($map->metadataKeyToClassMap, true),
        ));
    }
}