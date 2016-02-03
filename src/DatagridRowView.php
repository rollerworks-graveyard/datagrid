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
use Rollerworks\Component\Datagrid\Exception\BadMethodCallException;
use Rollerworks\Component\Datagrid\Exception\UnexpectedTypeException;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class DatagridRowView implements \IteratorAggregate, \Countable, \ArrayAccess
{
    /**
     * Cells views.
     *
     * @var CellView[]
     */
    public $cells = [];

    /**
     * The source object for which view is created.
     *
     * @var array|object
     */
    public $source;

    /**
     * Row index as given by the Datagrid.
     *
     * @var int
     */
    public $index;

    /**
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
     *
     * @throws UnexpectedTypeException
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
     * Required by the ArrayAccess implementation.
     *
     * @param string $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->cells[$offset]);
    }

    /**
     * Required by the ArrayAccess implementation.
     *
     * @param string $offset
     *
     * @return CellView
     */
    public function offsetGet($offset)
    {
        return $this->cells[$offset];
    }

    /**
     * Implements \ArrayAccess.
     *
     * @throws BadMethodCallException always as overwriting a cell is not allowed.
     */
    public function offsetSet($offset, $value)
    {
        throw new BadMethodCallException('Not supported');
    }

    /**
     * Implements \ArrayAccess.
     *
     * @throws BadMethodCallException always as removing a cell is not allowed.
     */
    public function offsetUnset($offset)
    {
        throw new BadMethodCallException('Not supported');
    }

    /**
     * Returns an iterator to iterate over cell (implements \IteratorAggregate).
     *
     * @return \ArrayIterator The iterator.
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->cells);
    }
}
