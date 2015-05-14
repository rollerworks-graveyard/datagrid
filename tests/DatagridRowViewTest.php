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

use Rollerworks\Component\Datagrid\DatagridRowView;

class DatagridRowViewTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateDatagridRowView()
    {
        $source = 'SOURCE';

        $datagridView = $this->getMock('Rollerworks\Component\Datagrid\DatagridViewInterface');
        $cellView = $this->getMock('Rollerworks\Component\Datagrid\Column\CellViewInterface');

        $column = $this->getMock('Rollerworks\Component\Datagrid\Column\ColumnInterface');
        $column->expects($this->atLeastOnce())
                ->method('createCellView')
                ->with($datagridView, $source, 0)
                ->will($this->returnValue($cellView));

        $columns = [
            'foo' => $column,
        ];

        $gridRow = new DatagridRowView($datagridView, $columns, $source, 0);

        $this->assertSame($gridRow->current(), $cellView);
        $this->assertSame($gridRow->getSource(), $source);
    }
}
