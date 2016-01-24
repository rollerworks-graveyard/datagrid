<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Tests\Extension\Core\ColumnType;

use Rollerworks\Component\Datagrid\Test\ColumnTypeTestCase;

abstract class BaseTypeTest extends ColumnTypeTestCase
{
    public function testPassLabelToView()
    {
        $column = $this->factory->createColumn(
            'id',
            $this->getTestedType(),
            $this->datagrid,
            [
                'label' => 'My label',
                'data_provider' => function ($data) {
                    return $data['key'];
                },
            ]
        );

        $object = new \stdClass();
        $object->key = ' foo ';
        $this->datagrid->setData([1 => $object]);

        $datagridView = $this->datagrid->createView();
        $view = $column->createHeaderView($datagridView);

        $this->assertSame('My label', $view->label);
    }
}
