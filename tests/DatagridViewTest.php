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

use Rollerworks\Component\Datagrid\Column\HeaderView;
use Rollerworks\Component\Datagrid\DatagridView;

class DatagridViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $rowset;

    /**
     * @var DatagridView
     */
    private $gridView;

    public function testHasColumn()
    {
        $this->assertTrue($this->gridView->hasColumn('foo'));
        $this->assertFalse($this->gridView->hasColumn('bar'));

        $this->assertInstanceOf('Rollerworks\Component\Datagrid\Column\HeaderView', $this->gridView->getColumn('foo'));
    }

    public function testGetColumn()
    {
        $this->assertInstanceOf('Rollerworks\Component\Datagrid\Column\HeaderView', $this->gridView->getColumn('foo'));

        $this->setExpectedException('Rollerworks\Component\Datagrid\Exception\InvalidArgumentException');
        $this->gridView->getColumn('bar');
    }

    public function testAddColumn()
    {
        $type = $this->getMock('Rollerworks\Component\Datagrid\Column\ResolvedColumnTypeInterface');
        $type->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('text'));

        $column = $this->getMock('Rollerworks\Component\Datagrid\Column\ColumnInterface');
        $column->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('bar'));

        $column->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($type));

        $columnHeader = new HeaderView($column, $this->gridView, 'bar');
        $this->gridView->addColumn($columnHeader);

        $this->assertTrue($this->gridView->hasColumn('foo'));
        $this->assertTrue($this->gridView->hasColumn('bar'));
        $this->assertFalse($this->gridView->hasColumn('bla'));
    }

    public function testRemoveColumn()
    {
        $type = $this->getMock('Rollerworks\Component\Datagrid\Column\ResolvedColumnTypeInterface');
        $type->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('text'));

        $column = $this->getMock('Rollerworks\Component\Datagrid\Column\ColumnInterface');
        $column->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('bar'));

        $column->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($type));

        $columnHeader = new HeaderView($column, $this->gridView, 'bar');
        $this->gridView->addColumn($columnHeader);

        $this->gridView->removeColumn('foo');

        $this->assertFalse($this->gridView->hasColumn('foo'));
        $this->assertTrue($this->gridView->hasColumn('bar'));
    }

    public function testClearColumns()
    {
        $type = $this->getMock('Rollerworks\Component\Datagrid\Column\ResolvedColumnTypeInterface');
        $type->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('text'));

        $column = $this->getMock('Rollerworks\Component\Datagrid\Column\ColumnInterface');
        $column->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('bar'));

        $column->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($type));

        $columnHeader = new HeaderView($column, $this->gridView, 'bar');
        $this->gridView->addColumn($columnHeader);

        $this->gridView->clearColumns();

        $this->assertFalse($this->gridView->hasColumn('foo'));
        $this->assertFalse($this->gridView->hasColumn('bar'));
    }

    public function testCount()
    {
        $this->rowset->expects($this->any())
            ->method('count')
            ->will($this->returnValue(2));

        $this->assertCount(2, $this->gridView);
    }

    protected function setUp()
    {
        $type = $this->getMock('Rollerworks\Component\Datagrid\Column\ResolvedColumnTypeInterface');
        $type->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('text'));

        $datagrid = $this->getMock('Rollerworks\Component\Datagrid\DatagridInterface');
        $column = $this->getMock('Rollerworks\Component\Datagrid\Column\ColumnInterface');

        $column->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($type));

        $this->rowset = $this->getMock('Rollerworks\Component\Datagrid\DataRowsetInterface');
        $this->gridView = new DatagridView($datagrid, [$column], $this->rowset);

        $column = $this->getMock('Rollerworks\Component\Datagrid\Column\ColumnInterface');
        $column->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($type));

        $columnHeader = new HeaderView($column, $this->gridView, 'foo');

        $column->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('foo'));

        $column->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($type));

        $this->gridView->setColumns([$columnHeader]);
    }
}
