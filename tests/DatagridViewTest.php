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
use Rollerworks\Component\Datagrid\Column\HeaderView;
use Rollerworks\Component\Datagrid\Column\ResolvedColumnTypeInterface;
use Rollerworks\Component\Datagrid\DatagridInterface;
use Rollerworks\Component\Datagrid\DatagridView;
use Rollerworks\Component\Datagrid\DataRowset;
use Rollerworks\Component\Datagrid\Tests\Fixtures\Entity;

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

    protected function setUp()
    {
        $type = $this->getMock(ResolvedColumnTypeInterface::class);

        $datagrid = $this->getMock(DatagridInterface::class);
        $column = $this->getMock(ColumnInterface::class);

        $column->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($type));

        $this->rowset = new DataRowset([
            'e1' => new Entity('entity1'),
            'e2' => new Entity('entity2'),
        ]);

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

    public function testGetCanGetVariablesWhenNoneWereSet()
    {
        $this->assertEquals([], $this->gridView->getVars());
        $this->assertNull($this->gridView->getVar('foo'));
        $this->assertEquals('bar', $this->gridView->getVar('foo', 'bar'));
    }

    public function testGetGetVariablesWhenSet()
    {
        $this->gridView->setVar('foo', 'bar');
        $this->gridView->setVar('name', 'test');
        $this->gridView->setVar('empty', null);

        $this->assertEquals(['foo' => 'bar', 'name' => 'test', 'empty' => null], $this->gridView->getVars());
        $this->assertEquals('bar', $this->gridView->getVar('foo'));
        $this->assertEquals('test', $this->gridView->getVar('name'));
        $this->assertEquals('test', $this->gridView->getVar('name'));
        $this->assertNull($this->gridView->getVar('bar'));

        // This ensures null values are checked properly.
        $this->assertNull($this->gridView->getVar('empty', 'overwrite'));
    }

    public function testCount()
    {
        $this->assertCount(2, $this->gridView);
    }
}
