<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Tests\Extension\Core\EventListener;

use Rollerworks\Component\Datagrid\Column\HeaderView;
use Rollerworks\Component\Datagrid\DatagridEvent;
use Rollerworks\Component\Datagrid\Extension\Core\EventListener\ColumnOrderListener;

class ColumnOrderListenerTest extends \PHPUnit_Framework_TestCase
{
    public static function provideCases()
    {
        return [
            [
                 [
                    'negative2' => -2,
                    'neutral1' => null,
                    'negative1' => -1,
                    'neutral2' => null,
                    'positive1' => 1,
                    'neutral3' => null,
                    'positive2' => 2,
                ],
                [
                    'negative2',
                    'negative1',
                    'neutral1',
                    'neutral2',
                    'neutral3',
                    'positive1',
                    'positive2',
                ],
            ],
            [
                [
                    'neutral1' => null,
                    'neutral2' => null,
                    'neutral3' => null,
                    'neutral4' => null,
                ],
                [
                    'neutral1',
                    'neutral2',
                    'neutral3',
                    'neutral4',
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideCases
     */
    public function testColumnOrder($inputColumns, $expectedSorted)
    {
        $subscriber = new ColumnOrderListener();

        $view = $this->getMock('Rollerworks\Component\Datagrid\DatagridViewInterface');
        $columns = [];

        $type = $this->getMock('Rollerworks\Component\Datagrid\Column\ResolvedColumnTypeInterface');
        $type->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('text'));

        foreach ($inputColumns as $name => $order) {
            $column = $this->getMock('Rollerworks\Component\Datagrid\Column\ColumnInterface');
            $column->expects($this->any())
                ->method('getType')
                ->will($this->returnValue($type));

            $columnHeader = new HeaderView($column, $view, $name);
            $columnHeader->attributes['display_order'] = $order;
            $columns[] = $columnHeader;
        }

        $view = $this->getMockBuilder('Rollerworks\Component\Datagrid\DatagridView')->disableOriginalConstructor()->getMock();
        $view
            ->expects($this->once())
            ->method('getColumns')
            ->will($this->returnValue($columns));

        $view
            ->expects($this->once())
            ->method('setColumns')
            ->will($this->returnCallback(function (array $columns) use ($expectedSorted) {
                $sorted = [];
                foreach ($columns as $column) {
                    $sorted[] = $column->label;
                }

               $this->assertSame($expectedSorted, $sorted);
            }));

        $datagrid = $this->getMock('Rollerworks\Component\Datagrid\DatagridInterface');
        $event = new DatagridEvent($datagrid, $view);

        $subscriber->postBuildView($event);
    }
}
