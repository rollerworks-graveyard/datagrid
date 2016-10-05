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

namespace Rollerworks\Component\Datagrid\Test;

use Rollerworks\Component\Datagrid\Datagrid;

abstract class ColumnTypeTestCase extends DatagridIntegrationTestCase
{
    public static function assertDateTimeEquals(\DateTime $expected, \DateTime $actual)
    {
        self::assertEquals($expected->format('c'), $actual->format('c'));
    }

    protected function assertCellValueEquals($expectedValue, $data, array $options = [], array $viewAttributes = null, $idx = 1)
    {
        $column = $this->factory->createColumn(
            'id',
            $this->getTestedType(),
            array_merge(
                [
                    'label' => 'My label',
                    'data_provider' => !is_array($data) ? function ($data) {
                        return $data->key;
                    } : null,
                ],
                $options
            )
        );

        $datagrid = new Datagrid('grid', [$column]);

        if (!is_array($data)) {
            $object = new \stdClass();
            $object->key = $data;
            $data = [$idx => $object];
        }

        $datagrid->setData($data);
        $datagridView = $datagrid->createView();

        $view = $column->createCellView($datagridView->columns['id'], $data[$idx], $idx);

        $this->assertEquals($expectedValue, $view->value);

        if (null !== $viewAttributes) {
            $viewAttributes['row'] = 1;

            self::assertViewVarsEquals($viewAttributes, $view);
        }
    }

    protected function assertCellValueNotEquals($expectedValue, $data, array $options = [], $idx = 1)
    {
        $column = $this->factory->createColumn(
            'id',
            $this->getTestedType(),
            array_merge(
                [
                    'label' => 'My label',
                    'data_provider' => !is_array($data) ? function ($data) {
                        return $data->key;
                    } : null,
                ],
                $options
            )
        );

        $datagrid = new Datagrid('grid', [$column]);

        if (!is_array($data)) {
            $object = new \stdClass();
            $object->key = $data;

            $data = [$idx => $object];
        }

        $datagrid->setData($data);
        $datagridView = $datagrid->createView();

        $view = $column->createCellView($datagridView->columns['id'], $data[$idx], $idx);
        $this->assertNotEquals($expectedValue, $view->value);
    }

    protected static function assertViewVarsEquals(array $viewAttributes, $view)
    {
        self::assertEquals(
            self::normalizeViewExpectation($viewAttributes, $view),
            $view->vars
        );
    }

    abstract protected function getTestedType(): string;

    /**
     * Ensures the 'base' view-vars are set.
     *
     * @param array $viewAttributes
     * @param       $view
     *
     * @return array
     */
    protected static function normalizeViewExpectation(array $viewAttributes, $view)
    {
        if (!isset($view->vars['unique_block_prefix'])) {
            return $viewAttributes;
        }

        $viewAttributes = array_replace(
            [
                'unique_block_prefix' => $view->vars['unique_block_prefix'],
                'block_prefixes' => $view->vars['block_prefixes'],
                'cache_key' => $view->vars['cache_key'],
            ],
            $viewAttributes
        );

        if (isset($view->vars['header_attr']) && !isset($viewAttributes['header_attr'])) {
            $viewAttributes['header_attr'] = $view->vars['header_attr'];
        }

        if (isset($view->vars['label_attr']) && !isset($viewAttributes['label_attr'])) {
            $viewAttributes['label_attr'] = $view->vars['label_attr'];
        }

        if (isset($view->vars['cell_attr']) && !isset($viewAttributes['cell_attr'])) {
            $viewAttributes['cell_attr'] = $view->vars['cell_attr'];
        }

        return $viewAttributes;
    }
}
