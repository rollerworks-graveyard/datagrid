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

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author FSi sp. z o.o. <info@fsi.pl>
 */
interface DatagridViewInterface extends \Iterator, \Countable, \ArrayAccess
{
    /**
     * Returns Datagrid the view bound to.
     *
     * @return DatagridInterface
     */
    public function getDatagrid();

    /**
     * Returns the Datagrid name.
     *
     * @return string
     */
    public function getName();

    /**
     * Check if column is registered in view.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasColumn($name);

    /**
     * Checks if column with specific type was added to grid.
     *
     * @param string $type
     *
     * @return bool
     */
    public function hasColumnType($type);

    /**
     * Removes a column from view.
     *
     * @param string $name
     */
    public function removeColumn($name);

    /**
     * Get a column from the view.
     *
     * @param string $name
     *
     * @return HeaderView
     *
     * @throw \InvalidArgumentException when the column is not registered
     */
    public function getColumn($name);

    /**
     * Return all columns registered in view.
     *
     * @return array
     */
    public function getColumns();

    /**
     * Remove all columns from the view.
     */
    public function clearColumns();

    /**
     * Add a new column to view.
     *
     * @param HeaderView $column
     */
    public function addColumn(HeaderView $column);

    /**
     * Add a new column to view.
     *
     * @param HeaderView $column
     */
    public function replaceColumn(HeaderView $column);
}
