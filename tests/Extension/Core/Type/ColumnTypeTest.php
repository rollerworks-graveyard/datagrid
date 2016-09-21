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

namespace Rollerworks\Component\Datagrid\Tests\Extension\Core\Type;

use Rollerworks\Component\Datagrid\Exception\DataProviderException;
use Rollerworks\Component\Datagrid\Extension\Core\Type\ColumnType;

class ColumnTypeTest extends BaseTypeTest
{
    public function testPassLabelToView()
    {
        $column = $this->factory->createColumn(
            'id',
            $this->getTestedType(),
            [
                'label' => 'My label',
                'data_provider' => function ($data) {
                    return $data->key;
                },
            ]
        );

        $datagrid = $this->factory->createDatagrid('grid', [$column]);

        $object = new \stdClass();
        $object->key = new \DateTime();

        $datagrid->setData([1 => $object]);

        $view = $datagrid->createView();
        $view = $column->createHeaderView($view);

        $this->assertSame('My label', $view->label);
    }

    public function testAutoConfigurationDataProvider()
    {
        $object = new \stdClass();
        $object->key = 'foo';
        $object->key2 = 'bar';
        $data = [1 => $object];

        $column1 = $this->factory->createColumn('key', ColumnType::class);
        $column2 = $this->factory->createColumn('key2', ColumnType::class);

        $datagrid = $this->factory->createDatagrid('grid', [$column1, $column2]);

        $datagrid->setData($data);
        $datagridView = $datagrid->createView();

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

        $column = $this->factory->createColumn('key3', ColumnType::class);

        $datagrid = $this->factory->createDatagrid('grid', [$column]);
        $datagrid->setData($data);

        $this->setExpectedException(DataProviderException::class, 'Unable to get value for column "key3"');

        $datagrid->createView();
    }

    protected function getTestedType(): string
    {
        return ColumnType::class;
    }
}
