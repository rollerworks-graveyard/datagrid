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

use Rollerworks\Component\Datagrid\Column\ColumnInterface;
use Rollerworks\Component\Datagrid\Exception\BadMethodCallException;
use Rollerworks\Component\Datagrid\Exception\UnknownColumnException;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author FSi sp. z o.o. <info@fsi.pl>
 */
interface DatagridInterface
{
    /**
     * Returns the datagrid name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Return column with by name.
     *
     * @param string $name
     *
     * @throws UnknownColumnException when the column is not registered in the datagrid
     *
     * @return ColumnInterface
     */
    public function getColumn($name): ColumnInterface;

    /**
     * Return all registered columns in the datagrid.
     *
     * @return ColumnInterface[]
     */
    public function getColumns(): array;

    /**
     * Get if the column is registered on the datagrid.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasColumn($name): bool;

    /**
     * Get whether column with a specific type is registered
     * on the grid.
     *
     * @param string $type
     *
     * @return bool
     */
    public function hasColumnType($type): bool;

    /**
     * Create a new DatagridView object for rendering the datagrid.
     *
     * The created DatagridView should be passed to a compatible
     * datagrid renderer.
     *
     * @return DatagridView
     */
    public function createView(): DatagridView;

    /**
     * Set the data collection of the datagrid.
     *
     * This method should only be called once and throw an exception
     * when called more then once.
     *
     * Data must be passed as an array or object that implements the
     * \ArrayAccess, \Countable and \IteratorAggregate interfaces.
     *
     * @param array|\Traversable $data
     *
     * @throws BadMethodCallException When data was already set
     */
    public function setData($data);

    /**
     * Returns the data collection of the datagrid.
     *
     * @return array|\Traversable|null Returns null when no data was set
     */
    public function getData();
}
