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
use Rollerworks\Component\Datagrid\DatagridInterface;
use Rollerworks\Component\Datagrid\DatagridView;
use Rollerworks\Component\Datagrid\Exception\InvalidArgumentException;
use Rollerworks\Component\Datagrid\Tests\Fixtures\Entity;

class DatagridViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DatagridView
     */
    private $gridView;

    protected function setUp()
    {
        $column = $this->getMock(ColumnInterface::class);

        $column->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('foo');

        $column->expects($this->once())
            ->method('createHeaderView')
            ->willReturn($this->getMockBuilder(HeaderView::class)->disableOriginalConstructor()->getMock());

        $datagrid = $this->getMock(DatagridInterface::class);

        $datagrid->expects($this->once())
            ->method('getColumns')
            ->willReturn([$column]);

        $datagrid->expects($this->once())
            ->method('getData')
            ->willReturn(
                [
                    new Entity('entity1'),
                    new Entity('entity2'),
                ]
            );

        $this->gridView = new DatagridView($datagrid);
    }

    public function testHasColumn()
    {
        $this->assertTrue($this->gridView->hasColumn('foo'));
        $this->assertFalse($this->gridView->hasColumn('bar'));

        $this->assertInstanceOf(HeaderView::class, $this->gridView->getColumn('foo'));
    }

    public function testGetColumn()
    {
        $this->assertInstanceOf(HeaderView::class, $this->gridView->getColumn('foo'));
    }

    public function testThrowsExceptionWhenGettingUnRegisteredColumn()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->gridView->getColumn('bar');
    }

    public function testCountReturnsNumberOfRows()
    {
        $this->assertCount(2, $this->gridView);
    }
}
