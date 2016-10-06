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

namespace Rollerworks\Component\Datagrid\Extension\Core\Type;

use Rollerworks\Component\Datagrid\Column\CellView;
use Rollerworks\Component\Datagrid\Column\ColumnInterface;
use Rollerworks\Component\Datagrid\Column\CompoundColumn;
use Rollerworks\Component\Datagrid\Column\HeaderView;

/**
 * CompoundColumn allows multiple sub-columns for advanced view building.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class CompoundColumnType extends BaseType
{
    /**
     * {@inheritdoc}
     */
    public function buildColumn(ColumnInterface $column, array $options)
    {
        // Simple pass all $data to the sub-columns.
        $column->setDataProvider(
            function ($data) {
                return $data;
            }
        );
    }

    /**
     * @param HeaderView                     $view
     * @param ColumnInterface|CompoundColumn $column
     * @param array                          $options
     */
    public function buildHeaderView(HeaderView $view, ColumnInterface $column, array $options)
    {
        parent::buildHeaderView($view, $column, $options);

        // The header information contains the actual block information (and cache key)
        $datagrid = $view->datagrid;

        $headers = [];

        foreach ($column->getColumns() as $subColumn) {
            $headers[$subColumn->getName()] = $subColumn->createHeaderView($datagrid);
        }

        $view->vars['_sub_headers'] = $headers;
    }

    /**
     * {@inheritdoc}
     */
    public function buildCellView(CellView $view, ColumnInterface $column, array $options)
    {
        $parent = $view->column;

        // Set shared information from the header.
        // This information is not recomputed for better performance.
        // Each header is created once, but this method will be called
        // 5000 times for a grid with 500 rows!

        $view->vars = array_replace($view->vars, [
            'cell_attr' => $options['cell_attr'],
            'unique_block_prefix' => $parent->vars['unique_block_prefix'],
            'block_prefixes' => $parent->vars['block_prefixes'],
            'cache_key' => $parent->vars['cache_key'],
        ]);

        $cells = [];

        $headers = $view->column->vars['_sub_headers'];

        /** @var CompoundColumn $column */
        foreach ($column->getColumns() as $subColumn) {
            $name = $subColumn->getName();

            $subView = $subColumn->createCellView($headers[$name], $view->source, $view->vars['row']);
            $subView->vars['compound'] = true;

            $cells[$name] = $subView;
        }

        return $view->value = $cells;
    }
}
