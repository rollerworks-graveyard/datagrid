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
use Rollerworks\Component\Datagrid\Column\HeaderView;
use Rollerworks\Component\Datagrid\Column\ResolvedColumnTypeInterface;
use Rollerworks\Component\Datagrid\Datagrid;
use Rollerworks\Component\Datagrid\DatagridViewInterface;
use Rollerworks\Component\Datagrid\Extension\Core\Type\TextType;
use Rollerworks\Component\Datagrid\Tests\Fixtures\Entity;
use Rollerworks\Component\Datagrid\Util\StringUtil;

class DatagridTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Datagrid
     */
    private $datagrid;

    protected function setUp()
    {
        $this->datagrid = new Datagrid('grid');
    }

    /**
     * @param string $name
     * @param string $typeName
     * @param bool   $reveal
     *
     * @return ResolvedColumnTypeInterface|\Prophecy\Prophecy\ObjectProphecy
     */
    private function createColumn($name = 'foo1', $typeName = TextType::class, $reveal = true)
    {
        $type = $this->prophesize(ResolvedColumnTypeInterface::class);
        $type->getInnerType()->willReturn(new $typeName());
        $type->getBlockPrefix()->willReturn(StringUtil::fqcnToBlockPrefix($typeName));

        $column = $this->prophesize(ColumnInterface::class);
        $column->getName()->willReturn($name);
        $column->getType()->willReturn($type->reveal());

        if (!$reveal) {
            return $column;
        }

        return $column->reveal();
    }

    public function testGetName()
    {
        $this->assertSame('grid', $this->datagrid->getName());
    }

    public function testHasColumn()
    {
        $this->datagrid->addColumn($this->createColumn());

        $this->assertTrue($this->datagrid->hasColumn('foo1'));
        $this->assertTrue($this->datagrid->hasColumnType(TextType::class));

        $this->assertFalse($this->datagrid->hasColumn('foo2'));
        $this->assertFalse($this->datagrid->hasColumnType('this_type_cant_exists'));

        $this->assertInstanceOf(ColumnInterface::class, $this->datagrid->getColumn('foo1'));
    }

    public function testRemoveColumn()
    {
        $this->datagrid->addColumn($this->createColumn());
        $this->datagrid->addColumn($this->createColumn('foo2'));

        $this->assertTrue($this->datagrid->hasColumn('foo1'));
        $this->assertTrue($this->datagrid->hasColumn('foo2'));

        $this->datagrid->removeColumn('foo1');

        $this->assertFalse($this->datagrid->hasColumn('foo1'));
        $this->assertTrue($this->datagrid->hasColumn('foo2'));
    }

    public function testSetData()
    {
        $column = $this->createColumn('foo1', TextType::class, false);
        $column->createHeaderView(Argument::any(), Argument::any())->will(
            function ($args) use ($column) {
                return new HeaderView($column->reveal(), $args[0], 'foo1');
            }
        );

        $this->datagrid->addColumn($column->reveal());

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

    public function testSetDataWithArray()
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
            $keys[] = $row->getIndex();
        }

        $this->assertEquals(array_keys($gridData), $keys);
    }

    public function testCreateView()
    {
        $column = $this->createColumn('foo1', TextType::class, false);
        $column->createHeaderView(Argument::any(), Argument::any())->will(
            function ($args) use ($column) {
                return new HeaderView($column->reveal(), $args[0], 'foo1');
            }
        );

        $this->datagrid->addColumn($column->reveal());

        $gridData = [
            new Entity('entity1'),
            new Entity('entity2'),
        ];

        $this->datagrid->setData($gridData);

        $datagridView = $this->datagrid->createView();

        $this->assertInstanceOf(DatagridViewInterface::class, $datagridView);

        $this->assertTrue($datagridView->hasColumn('foo1'));
        $this->assertFalse($datagridView->hasColumn('foo2'));
    }
}
