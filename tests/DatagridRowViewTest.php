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

use Rollerworks\Component\Datagrid\Column\CellView;
use Rollerworks\Component\Datagrid\Column\ColumnInterface;
use Rollerworks\Component\Datagrid\DatagridRowView;
use Rollerworks\Component\Datagrid\DatagridView;

class DatagridRowViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DatagridRowView
     */
    private $rowView;

    private $datagridView;

    /**
     * @var
     */
    private $cellView;

    private $source;

    protected function setUp()
    {
        $this->source = [0 => new \stdClass()];
        $this->datagridView = $this->getMockBuilder(DatagridView::class)->disableOriginalConstructor()->getMock();
        $this->cellView = $this->getMockBuilder(CellView::class)->disableOriginalConstructor()->getMock();

        $column = $this->createMock(ColumnInterface::class);

        $column->expects($this->once())
            ->method('getName')
            ->willReturn('foo');

        $column->expects($this->once())
            ->method('createCellView')
            ->with($this->datagridView, $this->source, 0)
            ->willReturn($this->cellView);

        $columns = [
            'foo' => $column,
        ];

        $this->rowView = new DatagridRowView($this->datagridView, $columns, $this->source, 0);
    }

    public function testViewArgumentsAreProvidedAndCorrect()
    {
        $this->assertSame(0, $this->rowView->index);
        $this->assertSame($this->source, $this->rowView->source);
        $this->assertSame($this->datagridView, $this->rowView->datagrid);
    }
}
