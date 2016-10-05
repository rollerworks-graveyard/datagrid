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

namespace Rollerworks\Component\Datagrid\Column;

use Rollerworks\Component\Datagrid\DatagridView;
use Rollerworks\Component\Datagrid\Exception\BadMethodCallException;

class CompoundColumn extends Column
{
    private $columns = [];

    public function createHeaderView(DatagridView $datagrid): HeaderView
    {
        if (!$this->locked || !$this->columns) {
            throw new BadMethodCallException(
                'Cannot be create a headerView, the Column is not properly configured.'
            );
        }

        return parent::createHeaderView($datagrid);
    }

    public function createCellView(HeaderView $header, $object, $index): CellView
    {
        if (!$this->locked || !$this->columns) {
            throw new BadMethodCallException(
                'Cannot be create a cellView, the Column is not properly configured.'
            );
        }

        return parent::createCellView($header, $object, $index);
    }

    /**
     * @param ColumnInterface[] $columns
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;
        $this->locked = true;
    }

    /**
     * @return ColumnInterface[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }
}
