<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Extension\Core\ColumnType;

use Rollerworks\Component\Datagrid\Column\AbstractColumnType;
use Rollerworks\Component\Datagrid\Column\CellView;
use Rollerworks\Component\Datagrid\Column\ColumnInterface;
use Rollerworks\Component\Datagrid\Column\HeaderView;

class BatchType extends AbstractColumnType
{
    /**
     * {@inheritdoc}
     */
    public function buildCellView(CellView $view, ColumnInterface $column, array $options)
    {
        $view->attributes['datagrid_name'] = $column->getDatagrid()->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function buildHeaderView(HeaderView $view, ColumnInterface $column, array $options)
    {
        $view->attributes['datagrid_name'] = $column->getDatagrid()->getName();
    }
}
