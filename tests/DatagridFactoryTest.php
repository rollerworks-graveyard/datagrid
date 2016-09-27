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

use Rollerworks\Component\Datagrid\Column\ColumnInterface;
use Rollerworks\Component\Datagrid\Column\ColumnTypeRegistryInterface;
use Rollerworks\Component\Datagrid\Column\ResolvedColumnTypeFactoryInterface;
use Rollerworks\Component\Datagrid\Column\ResolvedColumnTypeInterface;
use Rollerworks\Component\Datagrid\DatagridConfiguratorInterface;
use Rollerworks\Component\Datagrid\DatagridFactory;
use Rollerworks\Component\Datagrid\DatagridRegistryInterface;
use Rollerworks\Component\Datagrid\Extension\Core\Type\TextType;
use Rollerworks\Component\Datagrid\Tests\Fixtures\UsersDatagrid;

class DatagridFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DatagridFactory
     */
    private $factory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $typeRegistry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $resolvedTypeFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $datagridRegistry;

    protected function setUp()
    {
        $this->typeRegistry = $this->createMock(ColumnTypeRegistryInterface::class);
        $this->datagridRegistry = $this->createMock(DatagridRegistryInterface::class);
        $this->resolvedTypeFactory = $this->createMock(ResolvedColumnTypeFactoryInterface::class);

        $this->factory = new DatagridFactory($this->typeRegistry, $this->datagridRegistry);
    }

    public function testCreateGrid()
    {
        $configurator = $this->createMock(DatagridConfiguratorInterface::class);
        $configurator
            ->expects(self::once())
            ->method('buildDatagrid')
            ->with(self::anything(), self::equalTo(['foo' => 'bar']))
        ;

        $configurator2 = $this->createMock(DatagridConfiguratorInterface::class);
        $configurator2
            ->expects(self::once())
            ->method('buildDatagrid')
            ->with(self::anything(), self::equalTo([]))
        ;

        $this->datagridRegistry
            ->expects(self::at(0))
            ->method('getConfigurator')
            ->with('grid')
            ->willReturn($configurator)
        ;

        $this->datagridRegistry
            ->expects(self::at(1))
            ->method('getConfigurator')
            ->with(UsersDatagrid::class)
            ->willReturn(new UsersDatagrid())
        ;

        self::assertEquals('my_grid', $this->factory->createDatagrid('grid', 'my_grid', ['foo' => 'bar'])->getName());
        self::assertEquals('users_grid', $this->factory->createDatagrid($configurator2, 'users_grid', [])->getName());
        self::assertEquals('users_datagrid', $this->factory->createDatagrid(UsersDatagrid::class)->getName());
    }

    public function testCreateColumn()
    {
        $type = $this->createMock(ResolvedColumnTypeInterface::class);

        $column = $this->createMock(ColumnInterface::class);
        $column->expects($this->once())
                ->method('getOptions')
                ->will($this->returnValue(['foo' => 'bar']));

        $type->expects($this->once())
                ->method('createColumn')
                ->with('id', ['foo' => 'bar'])
                ->will($this->returnValue($column));

        $type->expects($this->once())
                ->method('buildType')
                ->with($column, ['foo' => 'bar']);

        $this->typeRegistry
            ->expects($this->once())
            ->method('getType')->with(TextType::class)
            ->will($this->returnValue($type));

        $this->assertEquals($column, $this->factory->createColumn('id', TextType::class, ['foo' => 'bar']));
    }
}
