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
use Rollerworks\Component\Datagrid\Datagrid;
use Rollerworks\Component\Datagrid\DatagridRowView;
use Rollerworks\Component\Datagrid\Tests\Fixtures\Entity;

class DatagridTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $factory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $dataMapper;

    /**
     * @var Datagrid
     */
    private $datagrid;

    protected function setUp()
    {
        $this->factory = $this->getMock('Rollerworks\Component\Datagrid\DatagridFactoryInterface');

        $this->dataMapper = $this->getMock('Rollerworks\Component\Datagrid\DataMapper\DataMapperInterface');
        $this->dataMapper->expects($this->any())
            ->method('getData')
            ->will($this->returnCallback(function ($field, Entity $object) {
                switch ($field) {
                    case 'name':
                        return $object->getName();
                }

                return;
            }));

        $this->dataMapper->expects($this->any())
            ->method('setData')
            ->will($this->returnCallback(function ($field, Entity $object, $value) {
                switch ($field) {
                    case 'name':
                       return $object->setName($value);
                }

                return;
            }));

        $this->datagrid = new Datagrid('grid', $this->factory, $this->dataMapper);
    }

    public function testGetName()
    {
        $this->assertSame('grid', $this->datagrid->getName());
    }

    public function testHasColumn()
    {
        $type = $this->getMock('Rollerworks\Component\Datagrid\Column\ResolvedColumnTypeInterface');
        $type->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('text'));

        $column = $this->getMock('Rollerworks\Component\Datagrid\Column\ColumnInterface');
        $column->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('foo1'));

        $column->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($type));

        $this->factory->expects($this->once())
            ->method('createColumn')
            ->with('foo1', 'text', $this->datagrid, [])
            ->will($this->returnValue($column));

        $this->datagrid->addColumn('foo1', 'text');

        $this->assertTrue($this->datagrid->hasColumn('foo1'));
        $this->assertTrue($this->datagrid->hasColumnType('text'));

        $this->assertFalse($this->datagrid->hasColumn('foo2'));
        $this->assertFalse($this->datagrid->hasColumnType('this_type_cant_exists'));

        $this->assertInstanceOf('Rollerworks\Component\Datagrid\Column\ColumnInterface', $this->datagrid->getColumn('foo1'));
    }

    public function testRemoveColumn()
    {
        $column = $this->getMock('Rollerworks\Component\Datagrid\Column\ColumnInterface');
        $column->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('foo1'));

        $column2 = $this->getMock('Rollerworks\Component\Datagrid\Column\ColumnInterface');
        $column2->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('foo2'));

        $this->factory->expects($this->any())
            ->method('createColumn')
            ->will($this->returnValueMap([
                ['foo1', 'text', $this->datagrid, [], $column],
                ['foo2', 'text', $this->datagrid, [], $column2],
            ]));

        $this->datagrid->addColumn('foo1', 'text');
        $this->datagrid->addColumn('foo2', 'text');

        $this->assertTrue($this->datagrid->hasColumn('foo1'));
        $this->assertTrue($this->datagrid->hasColumn('foo2'));

        $this->datagrid->removeColumn('foo1');

        $this->assertFalse($this->datagrid->hasColumn('foo1'));
        $this->assertTrue($this->datagrid->hasColumn('foo2'));
    }

    public function testGetDataMapper()
    {
        $this->assertInstanceOf('Rollerworks\Component\Datagrid\DataMapper\DataMapperInterface', $this->datagrid->getDataMapper());
    }

    public function testSetData()
    {
        $gridData = [
            new Entity('entity1'),
            new Entity('entity2'),
        ];

        $this->datagrid->setData($gridData);
        $this->assertCount(2, $this->datagrid->createView());

        $gridData = [
            ['some', 'data'],
            ['next', 'data'],
        ];

        $this->datagrid->setData($gridData);
        $this->assertCount(2, $this->datagrid->createView());
    }

    public function testSetDataForArray()
    {
        $gridData = [
            ['one'],
            ['two'],
            ['three'],
            ['four'],
            ['bazinga!'],
            ['five'],
        ];

        $this->datagrid->setData($gridData);
        $view = $this->datagrid->createView();

        $keys = [];
        foreach ($view as $row) {
            /* @var DatagridRowView $row */
            $keys[] = $row->getIndex();
        }

        $this->assertEquals(array_keys($gridData), $keys);
    }

    public function testBindArrayData()
    {
        $this->datagrid->bindData([]);
    }

    public function testBindArrayIteratorData()
    {
        $this->datagrid->bindData(new \ArrayIterator([]));
    }

    public function testBindInvalidData()
    {
        $this->setExpectedException('Rollerworks\Component\Datagrid\Exception\UnexpectedTypeException', 'Expected argument of type "array", "ArrayIterator", "boolean" given');
        $this->datagrid->bindData(false);
    }

    public function testCreateView()
    {
        $type = $this->getMock('Rollerworks\Component\Datagrid\Column\ResolvedColumnTypeInterface');
        $type->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('text'));

        $column = $this->getMock('Rollerworks\Component\Datagrid\Column\ColumnInterface');
        $column->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('foo1'));

        $column->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($type));

        $column->expects($this->any())
            ->method('createHeaderView')
            ->will($this->returnCallback(function ($datagrid) use ($column) {
                return new HeaderView($column, $datagrid, 'foo1');
            }));

        $this->factory->expects($this->once())
                ->method('createColumn')
                ->with('foo1', 'text', $this->datagrid, ['foo' => 'bar'])
                ->will($this->returnValue($column));

        $this->datagrid->addColumn('foo1', 'text', ['foo' => 'bar']);
        $gridData = [
            new Entity('entity1'),
            new Entity('entity2'),
        ];

        $this->datagrid->setData($gridData);
        $datagridView = $this->datagrid->createView();

        $this->assertInstanceOf('Rollerworks\Component\Datagrid\DatagridViewInterface', $datagridView);
        $this->assertTrue($datagridView->hasColumn('foo1'));
    }
}
