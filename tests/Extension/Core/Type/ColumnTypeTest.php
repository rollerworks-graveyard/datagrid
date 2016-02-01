<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Tests\Extension\Core\Type;

use Rollerworks\Component\Datagrid\Exception\DataProviderException;
use Rollerworks\Component\Datagrid\Extension\Core\Type\ColumnType;

class ColumnTypeTest extends BaseTypeTest
{
    public function testAutoConfigurationDataProvider()
    {
        $object = new \stdClass();
        $object->key = 'foo';
        $object->key2 = 'bar';
        $data = [1 => $object];

        $column1 = $this->factory->createColumn('key', ColumnType::class, ['label' => 'My label']);
        $column2 = $this->factory->createColumn('key2', ColumnType::class, ['label' => 'My label2']);

        $this->datagrid->setData($data);
        $datagridView = $this->datagrid->createView();

        $view1 = $column1->createCellView($datagridView, $object, 1);
        $view2 = $column2->createCellView($datagridView, $object, 1);

        $this->assertEquals($object->key, $view1->value);
        $this->assertEquals($object->key2, $view2->value);
    }

    public function testAutoConfigurationDataProviderFailsForUnsupportedPath()
    {
        $object = new \stdClass();
        $object->key = 'foo';
        $object->key2 = 'bar';
        $data = [1 => $object];

        $column = $this->factory->createColumn('key3', ColumnType::class, ['label' => 'My label']);

        $this->datagrid->setData($data);
        $datagridView = $this->datagrid->createView();

        $this->setExpectedException(DataProviderException::class, 'Unable to get value for column "key3"');

        $column->createCellView($datagridView, $object, 1);
    }

    protected function getTestedType()
    {
        return ColumnType::class;
    }
}
