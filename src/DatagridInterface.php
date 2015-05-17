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
use Rollerworks\Component\Datagrid\DataMapper\DataMapperInterface;
use Rollerworks\Component\Datagrid\Exception\UnknownColumnException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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
    public function getName();

    /**
     * Returns data mapper of the datagrid.
     *
     * @return DataMapperInterface
     */
    public function getDataMapper();

    /**
     * Add new a column to the datagrid.
     *
     * @param ColumnInterface $column
     *
     * @return DatagridInterface
     * @internal param string $name
     */
    public function addColumn(ColumnInterface $column);

    /**
     * Remove a column from the datagrid.
     *
     * @param string $name
     *
     * @throws UnknownColumnException when the column is not registered in the datagrid
     */
    public function removeColumn($name);

    /**
     * Remove all columns from Datagrid.
     */
    public function clearColumns();

    /**
     * Return column with by name.
     *
     * @param string $name
     *
     * @throws UnknownColumnException when the column is not registered in the datagrid
     *
     * @return ColumnInterface
     */
    public function getColumn($name);

    /**
     * Return all registered columns in the datagrid.
     *
     * @return array
     */
    public function getColumns();

    /**
     * Get if the column is registered on the datagrid.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasColumn($name);

    /**
     * Get whether column with a specific type is registered
     * on the grid.
     *
     * @param string $type
     *
     * @return bool
     */
    public function hasColumnType($type);

    /**
     * Create a new DatagridView object for rendering the datagrid.
     *
     * The created DatagridView should be passed to a compatible
     * datagrid rendered.
     *
     * @return DatagridViewInterface
     */
    public function createView();

    /**
     * Set data collection of the datagrid.
     *
     * This method should only be called once, to update the data on the
     * datagrid use the bindDate() method instead.
     *
     * Data must be passed as an array or object that implements the
     * \ArrayAccess, \Countable and \IteratorAggregate interfaces.
     *
     * @param array|\Traversable $data
     */
    public function setData($data);

    /**
     * Bind the the datagrid date set an external input.
     *
     * This can be used to make cells editable or update
     * the current datagrid with new data.
     *
     * @param array|\Traversable $data
     */
    public function bindData($data);

    /**
     * Adds an event listener that listens on the specified events.
     *
     * @param string   $eventName The event to listen on
     * @param callable $listener  The listener
     * @param int $priority       The priority of the listener.
     *                            The higher this value, the earlier an event
     *                            listener will be triggered in the chain.
     *                            Note that priority must be between -255 and 255
     */
    public function addEventListener($eventName, callable $listener, $priority = 0);

    /**
     * Returns the data-set of the datagrid.
     *
     * @param bool $original returns the data unprocessed.
     *
     * @return array|\Traversable
     */
    public function getData($original = false);
}
