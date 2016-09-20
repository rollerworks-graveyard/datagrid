<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid;

use Rollerworks\Component\Datagrid\Column\CellView;
use Rollerworks\Component\Datagrid\Column\ColumnInterface;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class DatagridRowView implements \IteratorAggregate, \Countable
{
    /**
     * READ-ONLY: Cells views.
     *
     * @var CellView[]
     */
    public $cells = [];

    /**
     * READ-ONLY: The source object from which view is created.
     *
     * @var array|object
     */
    public $source;

    /**
     * READ-ONLY: Row index as given by the Datagrid.
     *
     * @var int
     */
    public $index;

    /**
     * READ-ONLY: DatagridView this row is part of.
     *
     * @var DatagridView
     */
    public $datagrid;

    /**
     * Constructor.
     *
     * @param DatagridView      $datagridView
     * @param ColumnInterface[] $columns
     * @param mixed             $source
     * @param int               $index
     */
    public function __construct(DatagridView $datagridView, array $columns, $source, $index)
    {
        $this->datagrid = $datagridView;
        $this->source = $source;
        $this->index = $index;

        foreach ($columns as $column) {
            $this->cells[$column->getName()] = $column->createCellView($datagridView, $source, $index);
        }
    }

    /**
     * Returns the number of cells in the row.
     *
     * Implementation of Countable::count()
     *
     * @return int
     */
    public function count()
    {
        return count($this->cells);
    }

    /**
     * Returns an iterator for the cells.
     *
     * @return \ArrayIterator The iterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->cells);
    }
}
