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
use Rollerworks\Component\Datagrid\Column\HeaderView;
use Rollerworks\Component\Datagrid\Exception\InvalidArgumentException;
use Rollerworks\Component\Datagrid\Exception\UnexpectedTypeException;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author FSi sp. z o.o. <info@fsi.pl>
 */
class DatagridView implements DatagridViewInterface
{
    /**
     * Original column objects passed from Datagrid.
     *
     * This array should be used only to call methods like createCellView() or
     * createHeaderView().
     *
     * @var array
     */
    private $columns = [];

    /**
     * @var HeaderView[]
     */
    private $columnsHeaders = [];

    /**
     * @var array
     */
    private $originColumns = [];

    /**
     * Datagrid the view bound to.
     *
     * @var DatagridInterface
     */
    private $datagrid;

    /**
     * @var DataRowsetInterface
     */
    private $rowset;

    /**
     * @var mixed[]
     */
    private $vars = [];

    /**
     * Constructs.
     *
     * Should be called only from Datagrid::createView() method.
     *
     * @param DatagridInterface   $datagrid
     * @param ColumnInterface[]   $columns
     * @param DataRowsetInterface $rowset
     *
     * @throws UnexpectedTypeException
     */
    public function __construct(DatagridInterface $datagrid, array $columns, DataRowsetInterface $rowset)
    {
        $this->datagrid = $datagrid;
        $this->rowset = $rowset;

        foreach ($columns as $column) {
            if (!$column instanceof ColumnInterface) {
                throw new UnexpectedTypeException($column, ColumnInterface::class);
            }

            $this->columns[$column->getName()] = $column;
            $headerView = $column->createHeaderView($this);
            $this->columnsHeaders[$column->getName()] = $headerView;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDatagrid()
    {
        return $this->datagrid;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->datagrid->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function hasColumn($name)
    {
        return isset($this->columnsHeaders[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function hasColumnType($type)
    {
        foreach ($this->columnsHeaders as $header) {
            if ($header->column->getType()->getName() === $type) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function removeColumn($name)
    {
        if (isset($this->columnsHeaders[$name])) {
            unset($this->columnsHeaders[$name]);
            $this->originColumns = [];
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumn($name)
    {
        if ($this->hasColumn($name)) {
            return $this->columnsHeaders[$name];
        }

        throw new InvalidArgumentException(sprintf('Column "%s" does not exist in data grid.', $name));
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns()
    {
        return $this->columnsHeaders;
    }

    /**
     * {@inheritdoc}
     */
    public function clearColumns()
    {
        $this->columnsHeaders = [];
        $this->originColumns = [];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addColumn(HeaderView $column)
    {
        $this->columnsHeaders[$column->column->getName()] = $column;
        $this->originColumns = [];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function replaceColumn(HeaderView $column)
    {
        if (!array_key_exists($column->column->getName(), $this->columns)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Column with name "%s" is not registered in this view for "%s".',
                    $column->column->getName(),
                    $this->datagrid->getName()
                )
            );
        }

        $this->columnsHeaders[$column->column->getName()] = $column;
        $this->originColumns = [];

        return $this;
    }

    /**
     * Set the column list.
     *
     * @param HeaderView[] $columns
     *
     * @throws UnexpectedTypeException
     *
     * @return self
     */
    public function setColumns(array $columns)
    {
        $this->columnsHeaders = [];
        $this->originColumns = [];

        foreach ($columns as $column) {
            if (!$column instanceof HeaderView) {
                throw new UnexpectedTypeException($column, 'Rollerworks\Component\Datagrid\Column\HeaderView');
            }

            $this->columns[$column->column->getName()] = $column->column;
            $this->columnsHeaders[$column->column->getName()] = $column;
        }

        return $this;
    }

    /**
     * Return rowset indexes as array.
     *
     * @return array
     */
    public function getIndexes()
    {
        $indexes = [];
        foreach ($this->rowset as $index => $row) {
            $indexes[] = $index;
        }

        return $indexes;
    }

    /**
     * Returns the number of elements in the collection.
     *
     * @return int
     */
    public function count()
    {
        return $this->rowset->count();
    }

    /**
     * Return the current element.
     *
     * @return DatagridRowView current element from the rowset
     */
    public function current()
    {
        return new DatagridRowView($this, $this->getOriginColumns(), $this->rowset->current(), $this->rowset->key());
    }

    /**
     * Return the identifying key of the current element.
     *
     * @return int
     */
    public function key()
    {
        return $this->rowset->key();
    }

    /**
     * Move forward to next element.
     */
    public function next()
    {
        $this->rowset->next();
    }

    /**
     * Rewind the Iterator to the first element.
     */
    public function rewind()
    {
        $this->rowset->rewind();
    }

    /**
     * Check if there is a current element after calls to rewind() or next().
     *
     * @return bool False if there's nothing more to iterate over
     */
    public function valid()
    {
        return $this->rowset->valid();
    }

    /**
     * Check if an offset exists.
     * Required by the ArrayAccess implementation.
     *
     * @param string $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->rowset[$offset]);
    }

    /**
     * Get the row for the given offset.
     *
     * @param int $offset
     *
     * @throws \InvalidArgumentException
     *
     * @return DatagridRowViewInterface
     */
    public function offsetGet($offset)
    {
        if (isset($this->rowset[$offset])) {
            return new DatagridRowView($this, $this->getOriginColumns(), $this->rowset[$offset], $offset);
        }

        throw new \InvalidArgumentException(sprintf('Row "%s" does not exist in rowset.', $offset));
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

    /**
     * Set view variable.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function setVar($key, $value)
    {
        $this->vars[$key] = $value;
    }

    /**
     * Get a variable value by key.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getVar($key, $default = null)
    {
        if (array_key_exists($key, $this->vars)) {
            return $this->vars[$key];
        }

        return $default;
    }

    /**
     * Get all the variables assigned to this view.
     *
     * @return mixed[]
     */
    public function getVars()
    {
        return $this->vars;
    }

    /**
     * Return the origin columns in order of columns headers.
     *
     * @return array
     */
    private function getOriginColumns()
    {
        if ($this->originColumns) {
            return $this->originColumns;
        }

        $columns = [];

        foreach ($this->columnsHeaders as $name => $header) {
            $columns[$name] = $this->columns[$name];
        }

        $this->originColumns = $columns;

        return $columns;
    }
}
