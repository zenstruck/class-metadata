<?php

/*
 * This file is part of the zenstruck/class-metadata package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\Metadata\Tests\Unit\Bridge\Doctrine;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Zenstruck\Metadata\Bridge\Doctrine\AliasManagerRegistry;
use Zenstruck\Metadata\Tests\Fixture\Classes\Class2;
use Zenstruck\Metadata\Tests\InteractsWithGeneratedMap;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class AliasManagerRegistryTest extends TestCase
{
    use InteractsWithGeneratedMap;

    /**
     * @test
     */
    public function can_get_manager_for_alias(): void
    {
        self::generateMap();

        $inner = $this->createMock(ManagerRegistry::class);
        $inner->expects($this->once())
            ->method('getManagerForClass')
            ->with(Class2::class)
            ->willReturn($this->createMock(ObjectManager::class))
        ;

        $registry = new AliasManagerRegistry($inner);

        $registry->getManagerForClass('class2');
    }

    /**
     * @test
     */
    public function can_get_manager_for_class(): void
    {
        self::generateMap();

        $inner = $this->createMock(ManagerRegistry::class);
        $inner->expects($this->once())
            ->method('getManagerForClass')
            ->with(Class2::class)
            ->willReturn($this->createMock(ObjectManager::class))
        ;

        $registry = new AliasManagerRegistry($inner);

        $registry->getManagerForClass(Class2::class);
    }

    /**
     * @test
     */
    public function can_get_repository_for_alias(): void
    {
        self::generateMap();

        $inner = $this->createMock(ManagerRegistry::class);
        $inner->expects($this->once())
            ->method('getRepository')
            ->with(Class2::class)
            ->willReturn($this->createMock(ObjectRepository::class))
        ;

        $registry = new AliasManagerRegistry($inner);

        $registry->getRepository('class2');
    }

    /**
     * @test
     */
    public function can_get_repository_for_class(): void
    {
        self::generateMap();

        $inner = $this->createMock(ManagerRegistry::class);
        $inner->expects($this->once())
            ->method('getRepository')
            ->with(Class2::class)
            ->willReturn($this->createMock(ObjectRepository::class))
        ;

        $registry = new AliasManagerRegistry($inner);

        $registry->getRepository(Class2::class);
    }

    /**
     * @test
     */
    public function decorated_methods(): void
    {
        $inner = $this->createMock(ManagerRegistry::class);
        $inner->expects($this->once())->method('getDefaultConnectionName')->willReturn('foo');
        $inner->expects($this->once())->method('getConnection')->willReturn(new \stdClass());
        $inner->expects($this->once())->method('getConnections')->willReturn([]);
        $inner->expects($this->once())->method('getConnectionNames')->willReturn([]);
        $inner->expects($this->once())->method('getDefaultManagerName')->willReturn('foo');
        $inner->expects($this->once())->method('getManager')->willReturn($this->createMock(ObjectManager::class));
        $inner->expects($this->once())->method('getManagers')->willReturn([]);
        $inner->expects($this->once())->method('resetManager')->willReturn($this->createMock(ObjectManager::class));
        $inner->expects($this->once())->method('getManagerNames')->willReturn([]);

        $registry = new AliasManagerRegistry($inner);

        $registry->getDefaultConnectionName();
        $registry->getConnection();
        $registry->getConnections();
        $registry->getConnectionNames();
        $registry->getDefaultManagerName();
        $registry->getManager();
        $registry->getManagers();
        $registry->resetManager();
        $registry->getManagerNames();
    }
}
