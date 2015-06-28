<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Test;

use Rollerworks\Component\Datagrid\Datagrid;

abstract class ColumnTypeTestCase extends DatagridIntegrationTestCase
{
    /**
     * @var Datagrid
     */
    protected $datagrid;

    public static function assertDateTimeEquals(\DateTime $expected, \DateTime $actual)
    {
        self::assertEquals($expected->format('c'), $actual->format('c'));
    }

    protected function setUp()
    {
        parent::setUp();

        $this->datagrid = $this->factory->createDatagrid('grid');
    }

    protected function assertCellValueEquals($expectedValue, $data, array $options = [], array $viewAttributes = null, $idx = 1)
    {
        $column = $this->factory->createColumn('id', $this->getTestedType(), $this->datagrid, array_merge(['label' => 'My label', 'field_mapping' => ['key' => 'key']], $options));

        if (!is_array($data)) {
            $object = new \stdClass();
            $object->key = $data;
            $data = [$idx => $object];
        }

        $this->datagrid->setData($data);
        $datagridView = $this->datagrid->createView();

        $view = $column->createCellView($datagridView, $data[$idx], $idx);

        $this->assertEquals($expectedValue, $view->value);

        if (null !== $viewAttributes) {
            $viewAttributes['row'] = 1;

            $this->assertEquals($viewAttributes, $view->attributes);
        }
    }

    protected function assertCellValueNotEquals($expectedValue, $data, array $options = [], $idx = 1)
    {
        $column = $this->factory->createColumn('id', $this->getTestedType(), $this->datagrid, array_merge(['label' => 'My label', 'field_mapping' => ['key']], $options));

        if (!is_array($data)) {
            $object = new \stdClass();
            $object->key = $data;
            $data = [$idx => $object];
        }

        $this->datagrid->setData($data);
        $datagridView = $this->datagrid->createView();

        $view = $column->createCellView($datagridView, $data[$idx], $idx);
        $this->assertNotEquals($expectedValue, $view->value);
    }

    abstract protected function getTestedType();
}
