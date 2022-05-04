<?php

namespace Zenstruck;

use Zenstruck\Metadata\Map;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class Alias
{
    /** @var class-string */
    private string $class;

    public function __construct(private string $value)
    {
    }

    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @param object|class-string $objectOrClass
     */
    public static function for(object|string $objectOrClass): ?self
    {
        if (\is_object($objectOrClass)) {
            $objectOrClass = $objectOrClass::class;
        }

        if (!$alias = Map::aliasFor($objectOrClass)) {
            return null;
        }

        $alias = new self($alias);
        $alias->class = $objectOrClass;

        return $alias;
    }

    /**
     * @return ?class-string
     */
    public static function classFor(string $alias): ?string
    {
        return Map::classFor($alias);
    }

    public function value(): string
    {
        return $this->value;
    }

    /**
     * @return class-string
     */
    public function class(): string
    {
        return $this->class ?? throw new \LogicException(\sprintf('Cannot access class for alias "%s" as "%s" was constructed in an unsupported way.', $this->value, self::class));
    }
}
