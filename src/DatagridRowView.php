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

use Rollerworks\Component\Datagrid\Column\ColumnInterface;
use Rollerworks\Component\Datagrid\Column\ColumnTypeInterface;
use Rollerworks\Component\Datagrid\Exception\UnexpectedTypeException;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author FSi sp. z o.o. <info@fsi.pl>
 */
class DatagridRowView implements DatagridRowViewInterface
{
    /**
     * Cells views.
     *
     * @var array
     */
    protected $cellViews = [];

    /**
     * The source object for which view is created.
     *
     * @var mixed
     */
    protected $source;

    /**
     * Row index as given by the Datagrid.
     *
     * @var int
     */
    protected $index;

    /**
     * Constructor.
     *
     * @param DatagridViewInterface $datagridView
     * @param ColumnInterface[]     $columns
     * @param mixed                 $source
     * @param int                   $index
     *
     * @throws UnexpectedTypeException
     */
    public function __construct(DatagridViewInterface $datagridView, array $columns, $source, $index)
    {
        $this->count = count($columns);
        $this->source = $source;
        $this->index = $index;

        foreach ($columns as $name => $column) {
            if (!$column instanceof ColumnInterface) {
                throw new UnexpectedTypeException($column, 'Rollerworks\Component\Datagrid\Column\ColumnInterface');
            }

            $view = $column->createCellView($datagridView, $source, $index);
            $view->source = $source;

            $this->cellViews[$name] = $view;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * {@inheritdoc}
     */
    public function getSource()
    {
        return $this->source;
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
        return count($this->cellViews);
    }

    /**
     * Return the current cell view.
     *
     * Similar to the current() function for arrays in PHP.
     * Required by interface Iterator.
     *
     * @return Column\CellView current element from the rowset
     */
    public function current()
    {
        return current($this->cellViews);
    }

    /**
     * Return the identifying key of the current column.
     *
     * Similar to the key() function for arrays in PHP.
     * Required by interface Iterator.
     *
     * @return string
     */
    public function key()
    {
        return key($this->cellViews);
    }

    /**
     * Move forward to next cell view.
     *
     * Similar to the next() function for arrays in PHP.
     * Required by interface Iterator.
     *
     * @return string
     */
    public function next()
    {
        next($this->cellViews);
    }

    /**
     * Rewind the Iterator to the first element.
     *
     * Similar to the reset() function for arrays in PHP.
     * Required by interface Iterator.
     */
    public function rewind()
    {
        reset($this->cellViews);
    }

    /**
     * Checks if current position is valid.
     *
     * Required by the SeekableIterator implementation.
     *
     * @return bool
     */
    public function valid()
    {
        return $this->key() !== null;
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
        return isset($this->cellViews[$offset]);
    }

    /**
     * Required by the ArrayAccess implementation.
     *
     * @param string $offset
     *
     * @throws \InvalidArgumentException
     *
     * @return mixed|false|ColumnTypeInterface
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->cellViews[$offset];
        }

        throw new \InvalidArgumentException(sprintf('Column "%s" does not exist in row.', $offset));
    }

    /**
     * Does nothing.
     *
     * Required by the ArrayAccess implementation.
     *
     * @param string $offset
     * @param mixed  $value
     */
    public function offsetSet($offset, $value)
    {
    }

    /**
     * Does nothing.
     *
     * Required by the ArrayAccess implementation.
     *
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
    }
}
