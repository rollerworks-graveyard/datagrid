<?php

declare(strict_types=1);

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Tests;

use PHPUnit\Framework\TestCase;
use Rollerworks\Component\Datagrid\DatagridConfiguratorInterface;
use Rollerworks\Component\Datagrid\DatagridRegistry;
use Rollerworks\Component\Datagrid\Exception\InvalidArgumentException;

class DatagridRegistryTest extends TestCase
{
    /** @test */
    public function it_loads_configurator_lazily()
    {
        $configurator = $this->createMock(DatagridConfiguratorInterface::class);
        $configurator2 = $this->createMock(DatagridConfiguratorInterface::class);

        $registry = new DatagridRegistry(
            [
                'grid' => function () use ($configurator) {
                    return $configurator;
                },
                'grid2' => function () use ($configurator2) {
                    return $configurator2;
                },
            ]
        );

        self::assertTrue($registry->hasConfigurator('grid'));
        self::assertTrue($registry->hasConfigurator('grid2'));

        self::assertSame($configurator, $registry->getConfigurator('grid'));
        self::assertSame($configurator2, $registry->getConfigurator('grid2'));

        // Ensure they still work, after initializing.
        self::assertFalse($registry->hasConfigurator('grid3'));
        self::assertTrue($registry->hasConfigurator('grid'));

        self::assertSame($configurator, $registry->getConfigurator('grid'));
        self::assertSame($configurator2, $registry->getConfigurator('grid2'));
    }

    /** @test */
    public function it_loads_configurator_by_fqcn()
    {
        $configurator = $this->createMock(DatagridConfiguratorInterface::class);
        $configurator2 = $this->createMock(DatagridConfiguratorInterface::class);

        $registry = new DatagridRegistry(
            [
                'grid' => function () use ($configurator) {
                    return $configurator;
                },
            ]
        );

        $name = get_class($configurator2);

        self::assertTrue($registry->hasConfigurator('grid'));
        self::assertTrue($registry->hasConfigurator($name));
        self::assertFalse($registry->hasConfigurator('grid2'));

        self::assertSame($configurator, $registry->getConfigurator('grid'));
        self::assertSame($name, get_class($registry->getConfigurator($name)));
    }

    /** @test */
    public function it_checks_registered_before_className()
    {
        $configurator = $this->createMock(DatagridConfiguratorInterface::class);
        $configurator2 = $this->createMock(DatagridConfiguratorInterface::class);
        $name = get_class($configurator2);

        $registry = new DatagridRegistry(
            [
                'grid' => function () use ($configurator) {
                    return $configurator;
                },
                $name => function () use ($configurator2) {
                    return $configurator2;
                },
            ]
        );

        $name = get_class($configurator2);

        self::assertTrue($registry->hasConfigurator('grid'));
        self::assertTrue($registry->hasConfigurator($name));
        self::assertFalse($registry->hasConfigurator('grid2'));

        self::assertSame($configurator, $registry->getConfigurator('grid'));
        self::assertSame($configurator2, $registry->getConfigurator($name));
    }

    /** @test */
    public function it_errors_when_configurator_is_not_registered_and_class_is_a_configurator()
    {
        $configurator = $this->createMock(DatagridConfiguratorInterface::class);
        $configurator2 = \stdClass::class;

        $registry = new DatagridRegistry(
            [
                'grid' => function () use ($configurator) {
                    return $configurator;
                },
            ]
        );

        self::assertTrue($registry->hasConfigurator('grid'));
        self::assertFalse($registry->hasConfigurator('grid2'));
        self::assertFalse($registry->hasConfigurator($configurator2));

        self::assertSame($configurator, $registry->getConfigurator('grid'));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Could not load datagrid configurator "stdClass"');

        $registry->getConfigurator($configurator2);
    }

    /** @test */
    public function it_errors_when_configurator_is_not_registered_class_does_not_exist()
    {
        $configurator = $this->createMock(DatagridConfiguratorInterface::class);
        $configurator2 = 'f4394832948_foobar_cow';

        $registry = new DatagridRegistry(
            [
                'grid' => function () use ($configurator) {
                    return $configurator;
                },
            ]
        );

        self::assertTrue($registry->hasConfigurator('grid'));
        self::assertFalse($registry->hasConfigurator('grid2'));
        self::assertFalse($registry->hasConfigurator($configurator2));

        self::assertSame($configurator, $registry->getConfigurator('grid'));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Could not load datagrid configurator "f4394832948_foobar_cow"');

        $registry->getConfigurator($configurator2);
    }
}
