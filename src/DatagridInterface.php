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
     * Returns data mapper.
     *
     * @return \Rollerworks\Component\Datagrid\DataMapper\DataMapperInterface
     */
    public function getDataMapper();

    /**
     * Add new a column to Datagrid.
     *
     * Remember that column type must be registered in the
     * DatagridFactory that was used to create the current Datagrid.
     *
     * @param string|ColumnInterface $column
     * @param string                 $type
     * @param array                  $options
     *
     * @return DatagridInterface
     */
    public function addColumn($column, $type = 'text', $options = []);

    /**
     * Remove a column from Datagrid.
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException when column with $name not exists in grid.
     */
    public function removeColumn($name);

    /**
     * Remove all columns from Datagrid.
     */
    public function clearColumns();

    /**
     * Return column with $name.
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException when column with $name not exists in grid.
     *
     * @return ColumnInterface
     */
    public function getColumn($name);

    /**
     * Return all registered columns in the grid.
     *
     * @return array
     */
    public function getColumns();

    /**
     * Checks if column was added to the grid.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasColumn($name);

    /**
     * Checks if column with specific type was added to the grid.
     *
     * @param string $type
     *
     * @return bool
     */
    public function hasColumnType($type);

    /**
     * Create DatagridView object that should be used to render data grid.
     *
     * @return DatagridViewInterface
     */
    public function createView();

    /**
     * Set data collection.
     *
     * This method is different from bind data and should not be used to update data.
     * Data should be passed as an array or object that implements the
     * \ArrayAccess, \Countable and \IteratorAggregate interfaces.
     *
     * @param array|\Traversable $data
     */
    public function setData($data);

    /**
     * This method should be used only to update already set data.
     *
     * @param array|\Traversable $data
     */
    public function bindData($data);

    /**
     * Adds an event listener that listens on the specified events.
     *
     * @param string   $eventName The event to listen on
     * @param callable $listener  The listener
     * @param int      $priority  The higher this value, the earlier an event
     *                            listener will be triggered in the chain (defaults to 0)
     */
    public function addEventListener($eventName, $listener, $priority = 0);

    /**
     * Adds an event subscriber.
     *
     * The subscriber is asked for all the events he is
     * interested in and added as a listener for these events.
     *
     * @param EventSubscriberInterface $subscriber The subscriber.
     */
    public function addEventSubscriber(EventSubscriberInterface $subscriber);

    /**
     * Returns the data set on the datagrid.
     *
     * @param bool $original returns the data unprocessed.
     *
     * @return mixed|\Traversable
     */
    public function getData($original = false);
}
