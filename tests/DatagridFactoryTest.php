<?php

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
use Rollerworks\Component\Datagrid\DatagridBuilderInterface;
use Rollerworks\Component\Datagrid\DatagridFactory;
use Rollerworks\Component\Datagrid\DatagridInterface;
use Rollerworks\Component\Datagrid\DataMapper\DataMapperInterface;

class DatagridFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DatagridFactory
     */
    private $factory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $registry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $dataMapper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $resolvedTypeFactory;

    protected function setUp()
    {
        $this->registry = $this->getMock(ColumnTypeRegistryInterface::class);
        $this->resolvedTypeFactory = $this->getMock(ResolvedColumnTypeFactoryInterface::class);
        $this->dataMapper = $this->getMock(DataMapperInterface::class);

        $this->factory = new DatagridFactory($this->registry, $this->resolvedTypeFactory, $this->dataMapper);
    }

    public function testCreateGrid()
    {
        $grid = $this->factory->createDatagrid('grid');

        $this->assertInstanceOf(DatagridInterface::class, $grid);
        $this->assertEquals('grid', $grid->getName());
    }

    public function testCreateGridBuilder()
    {
        $this->assertInstanceOf(DatagridBuilderInterface::class, $this->factory->createDatagridBuilder('grid'));
        $this->assertInstanceOf(DatagridBuilderInterface::class, $this->factory->createDatagridBuilder('grid2'));
    }

    public function testCreateColumn()
    {
        $grid = $this->factory->createDatagrid('grid');

        $type = $this->getMock(ResolvedColumnTypeInterface::class);

        $column = $this->getMock(ColumnInterface::class);
        $column->expects($this->once())
                ->method('getOptions')
                ->will($this->returnValue(['foo' => 'bar']));

        $type->expects($this->once())
                ->method('createColumn')
                ->with('id', $grid, ['foo' => 'bar'])
                ->will($this->returnValue($column));

        $type->expects($this->once())
                ->method('buildType')
                ->with($column, ['foo' => 'bar']);

        $this->registry
            ->expects($this->once())
            ->method('getType')->with('text')
            ->will($this->returnValue($type));

        $this->assertEquals($column, $this->factory->createColumn('id', 'text', $grid, ['foo' => 'bar']));
    }

    public function testGetDataMapper()
    {
        $this->assertInstanceOf(DataMapperInterface::class, $this->factory->getDataMapper());
    }
}
