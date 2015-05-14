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

use Rollerworks\Component\Datagrid\DatagridFactory;

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
        $this->registry = $this->getMock('Rollerworks\Component\Datagrid\Column\ColumnRegistryInterface');
        $this->resolvedTypeFactory = $this->getMock('Rollerworks\Component\Datagrid\Column\ResolvedColumnTypeFactoryInterface');
        $this->dataMapper = $this->getMock('Rollerworks\Component\Datagrid\DataMapper\DataMapperInterface');

        $this->factory = new DatagridFactory($this->registry, $this->resolvedTypeFactory, $this->dataMapper);
    }

    public function testCreateGrids()
    {
        $grid = $this->factory->createDatagrid('grid');
        $this->assertInstanceOf('Rollerworks\Component\Datagrid\Datagrid', $grid);
        $this->assertEquals('grid', $grid->getName());

        $this->setExpectedException('Rollerworks\Component\Datagrid\Exception\DatagridException');
        $this->factory->createDatagrid('grid');
    }

    public function testCreateColumn()
    {
        $grid = $this->factory->createDatagrid('grid');

        $type = $this->getMock('Rollerworks\Component\Datagrid\Column\ResolvedColumnTypeInterface');
        $column = $this->getMockBuilder('Rollerworks\Component\Datagrid\Column\Column')->disableOriginalConstructor()->getMock();
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
        $this->assertInstanceOf('Rollerworks\Component\Datagrid\DataMapper\DataMapperInterface', $this->factory->getDataMapper());
    }
}
