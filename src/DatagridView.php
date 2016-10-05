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
class DatagridView extends BaseView implements \IteratorAggregate, \Countable
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

    public function __construct(DatagridInterface $datagrid)
    {
        $this->name = $datagrid->getName();
    }

    /**
     * Initialize the datagrid view.
     */
    public function init(DatagridInterface $datagrid)
    {
        if (null === $data = $datagrid->getData()) {
            throw new InvalidArgumentException('No data provided for the view.');
        }

        $columns = $datagrid->getColumns();

        foreach ($columns as $column) {
            $this->columns[$column->getName()] = $column->createHeaderView($this);
        }

        if (!isset($this->vars['row_vars'])) {
            $this->vars['row_vars'] = [];
        }

        foreach ($data as $id => $value) {
            $this->rows[$id] = $row = new DatagridRowView($this, $columns, $value, $id);
            $row->vars = $this->vars['row_vars'];
        }
    }

    public function hasColumn(string $name): bool
    {
        return isset($this->columns[$name]);
    }

    public function getColumn(string $name): HeaderView
    {
        if (isset($this->columns[$name])) {
            return $this->columns[$name];
        }

        throw new InvalidArgumentException(sprintf('Column "%s" does not exist in datagrid.', $name));
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
