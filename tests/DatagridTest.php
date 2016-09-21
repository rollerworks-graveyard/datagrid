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

use Prophecy\Argument;
use Rollerworks\Component\Datagrid\Column\CellView;
use Rollerworks\Component\Datagrid\Column\ColumnInterface;
use Rollerworks\Component\Datagrid\Column\HeaderView;
use Rollerworks\Component\Datagrid\Column\ResolvedColumnTypeInterface;
use Rollerworks\Component\Datagrid\Datagrid;
use Rollerworks\Component\Datagrid\DatagridView;
use Rollerworks\Component\Datagrid\Exception\BadMethodCallException;
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
        $this->datagrid = new Datagrid('grid', [$this->createColumn()]);
    }

    /**
     * @param string $name
     * @param string $typeName
     *
     * @return ColumnInterface
     */
    private function createColumn($name = 'foo1', $typeName = TextType::class)
    {
        $type = $this->prophesize(ResolvedColumnTypeInterface::class);
        $type->getInnerType()->willReturn(new $typeName());
        $type->getBlockPrefix()->willReturn(StringUtil::fqcnToBlockPrefix($typeName));

        $column = $this->prophesize(ColumnInterface::class);
        $column->getName()->willReturn($name);
        $column->getType()->willReturn($type->reveal());

        $column->createHeaderView(Argument::any(), Argument::any())->will(
            function ($args) use ($name) {
                /* @var \Prophecy\Prophecy\ObjectProphecy $this */
                return new HeaderView($this->reveal(), $args[0], $name);
            }
        );

        $column->createCellView(Argument::any(), Argument::any(), Argument::any())->will(
            function ($args) use ($name) {
                /* @var \Prophecy\Prophecy\ObjectProphecy $this */
                return new CellView($this->reveal(), $args[0]);
            }
        );

        return $column->reveal();
    }

    /**
     * @expectedException \Rollerworks\Component\Datagrid\Exception\InvalidArgumentException
     */
    public function testInvalidColumnThrowsException()
    {
        new Datagrid('grid', [$this->createColumn(), null]);
    }

    public function testGetName()
    {
        $this->assertSame('grid', $this->datagrid->getName());
    }

    public function testHasColumn()
    {
        $this->assertTrue($this->datagrid->hasColumn('foo1'));
        $this->assertTrue($this->datagrid->hasColumnType(TextType::class));

        $this->assertFalse($this->datagrid->hasColumn('foo2'));
        $this->assertFalse($this->datagrid->hasColumnType('this_type_cant_exists'));

        $this->assertInstanceOf(ColumnInterface::class, $this->datagrid->getColumn('foo1'));
    }

    public function testSetData()
    {
        $data = [
            new Entity('entity1'),
            new Entity('entity2'),
        ];

        $this->datagrid->setData($data);

        $this->assertSame($data, $this->datagrid->getData());
    }

    public function testSetDataWithArrayAsSource()
    {
        $data = [
            ['some', 'data'],
            ['next', 'data'],
        ];

        $this->datagrid->setData($data);

        $this->assertSame($data, $this->datagrid->getData());
    }

    public function testSetDataShouldOnlyBeCalledOnce()
    {
        $data = [
            new Entity('entity1'),
        ];

        $this->datagrid->setData($data);

        $this->setExpectedException(
            BadMethodCallException::class,
            'Datagrid::setData() can only be called once.'
        );

        $this->datagrid->setData($data);
    }

    public function testCreateView()
    {
        $gridData = [
            new Entity('entity1'),
            new Entity('entity2'),
        ];

        $this->datagrid->setData($gridData);

        $view = $this->datagrid->createView();

        $this->assertInstanceOf(DatagridView::class, $view);
        $this->assertTrue($view->hasColumn('foo1'));
        $this->assertFalse($view->hasColumn('foo2'));
    }
}
