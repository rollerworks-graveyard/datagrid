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

use Rollerworks\Component\Datagrid\Column\HeaderView;
use Rollerworks\Component\Datagrid\Exception\InvalidArgumentException;

/**
 * DatagridView provides all the information to render a Datagrid.
 *
 * This class should only be initialized be directly.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class DatagridView implements \IteratorAggregate, \Countable
{
    /**
     * READ-ONLY: The Datagrid name.
     *
     * @var string
     */
    public $name;

    /**
     * READ-ONLY: The Datagrid Header views.
     *
     * @var HeaderView[]
     */
    public $columns = [];

    /**
     * READ-ONLY: The Datagrid rows.
     *
     * @var DatagridRowView[]
     */
    public $rows = [];

    /**
     * Extra variables for view rendering.
     *
     * It's possible to set values directly.
     * But the property type itself should not be changed!
     *
     * @var array
     */
    public $vars = [];

    /**
     * Constructor.
     *
     * @param DatagridInterface $datagrid
     */
    public function __construct(DatagridInterface $datagrid)
    {
        $this->name = $datagrid->getName();
        $columns = $datagrid->getColumns();

        if (null === $data = $datagrid->getData()) {
            throw new InvalidArgumentException('No data provided for the view.');
        }

        foreach ($columns as $column) {
            $this->columns[$column->getName()] = $column->createHeaderView($this);
        }

        foreach ($data as $id => $value) {
            $this->rows[$id] = new DatagridRowView($this, $columns, $value, $id);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasColumn($name)
    {
        return isset($this->columns[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getColumn($name)
    {
        if (isset($this->columns[$name])) {
            return $this->columns[$name];
        }

        throw new InvalidArgumentException(sprintf('Column "%s" does not exist in datagrid.', $name));
    }

    /**
     * Get a variable value by key.
     *
     * This method should only be used when the key can null.
     * Else it's faster to get ths var's value directly.
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
     * Returns the number of elements in the collection.
     *
     * @return int
     */
    public function count()
    {
        return count($this->rows);
    }

    /**
     * Returns an iterator to iterate over rows (implements \IteratorAggregate).
     *
     * @return \ArrayIterator The iterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->rows);
    }
}
