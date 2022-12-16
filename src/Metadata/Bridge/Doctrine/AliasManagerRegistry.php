<?php

/*
 * This file is part of the zenstruck/class-metadata package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\Metadata\Bridge\Doctrine;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Zenstruck\Alias;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class AliasManagerRegistry implements ManagerRegistry
{
    public function __construct(private ManagerRegistry $inner)
    {
    }

    public function getDefaultConnectionName(): string
    {
        return $this->inner->getDefaultConnectionName();
    }

    public function getConnection($name = null): object
    {
        return $this->inner->getConnection($name);
    }

    public function getConnections(): array
    {
        return $this->inner->getConnections();
    }

    public function getConnectionNames(): array
    {
        return $this->inner->getConnectionNames();
    }

    public function getDefaultManagerName(): string
    {
        return $this->inner->getDefaultManagerName();
    }

    public function getManager($name = null): ObjectManager
    {
        return $this->inner->getManager($name);
    }

    public function getManagers(): array
    {
        return $this->inner->getManagers();
    }

    public function resetManager($name = null): ObjectManager
    {
        return $this->inner->resetManager($name);
    }

    public function getManagerNames(): array
    {
        return $this->inner->getManagerNames();
    }

    public function getRepository($persistentObject, $persistentManagerName = null): ObjectRepository
    {
        return $this->inner->getRepository(Alias::classFor($persistentObject) ?? $persistentObject, $persistentManagerName); // @phpstan-ignore-line
    }

    public function getManagerForClass($class): ?ObjectManager
    {
        return $this->inner->getManagerForClass(Alias::classFor($class) ?? $class);
    }

    /**
     * @deprecated
     *
     * @param string $alias
     */
    public function getAliasNamespace($alias): string
    {
        if (\method_exists($this->inner, 'getAliasNamespace')) {
            return $this->inner->getAliasNamespace($alias);
        }

        throw new \BadMethodCallException();
    }
}
