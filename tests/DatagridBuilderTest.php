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

use Prophecy\Argument;
use Rollerworks\Component\Datagrid\Column\ColumnInterface;
use Rollerworks\Component\Datagrid\DatagridBuilder;
use Rollerworks\Component\Datagrid\DatagridFactoryInterface;
use Rollerworks\Component\Datagrid\DatagridInterface;
use Rollerworks\Component\Datagrid\DataMapper\DataMapperInterface;

final class DatagridBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy
     */
    private $factory;

    /**
     * @var \Prophecy\Prophecy\ObjectProphecy
     */
    private $dataMapper;

    protected function setUp()
    {
        $this->dataMapper = $this->prophesize(DataMapperInterface::class)->reveal();
        $this->factory = $this->prophesize(DatagridFactoryInterface::class);
    }

    public function testCreateDatagridWithUnresolvedColumns()
    {
        $test = $this;

        $columnCreator = function ($args) use ($test) {
            $column = $test->prophesize(ColumnInterface::class);
            $column->getName()->willReturn($args[0]);

            return $column->reveal();
        };

        $this->factory->createColumn('id', 'number', Argument::any(), [])->will($columnCreator)->shouldBeCalled();
        $this->factory->createColumn('name', 'text', Argument::any(), ['format' => '%s'])->will($columnCreator)->shouldBeCalled();

        $grid = new DatagridBuilder($this->factory->reveal(), 'grid', $this->dataMapper);
        $grid->add('id', 'number');
        $grid->add('name', 'text', ['format' => '%s']);

        $this->assertInstanceOf(DatagridInterface::class, $grid->getDatagrid());
    }
}
