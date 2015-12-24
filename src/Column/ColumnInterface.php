<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Column;

use Rollerworks\Component\Datagrid\DatagridInterface;
use Rollerworks\Component\Datagrid\DatagridViewInterface;
use Rollerworks\Component\Datagrid\DataTransformerInterface;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
interface ColumnInterface
{
    /**
     * Returns the name of the column in the Datagrid.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the column-type name.
     *
     * @return ResolvedColumnTypeInterface
     */
    public function getType();

    /**
     * Appends / prepends a transformer to the view transformer chain.
     *
     * The transform method of the transformer is used to convert data from the
     * normalized to the view format.
     * The reverseTransform method of the transformer is used to convert from the
     * view to the normalized format.
     *
     * @param DataTransformerInterface $viewTransformer
     * @param bool                     $forcePrepend    if set to true, prepend instead of appending
     *
     * @return self The configuration object.
     */
    public function addViewTransformer(DataTransformerInterface $viewTransformer, $forcePrepend = false);

    /**
     * Clears the view transformers.
     *
     * @return self The configuration object.
     */
    public function resetViewTransformers();

    /**
     * Returns the view transformers of the column cell.
     *
     * @return DataTransformerInterface[] An array of {@link DataTransformerInterface} instances.
     */
    public function getViewTransformers();

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
     * Returns the Datagrid the column is registered in.
     *
     * @return DatagridInterface
     */
    public function getDatagrid();

    /**
     * Returns all options passed during the construction of the column.
     *
     * @return array The passed options.
     */
    public function getOptions();

    /**
     * Returns the value of a specific option.
     *
     * @param string $name    The option name.
     * @param mixed  $default The value returned if the option does not exist.
     *
     * @return mixed The option value.
     */
    public function getOption($name, $default = null);

    /**
     * Returns whether a specific option exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasOption($name);

    /**
     * @param DatagridViewInterface $datagrid
     *
     * @return HeaderView
     */
    public function createHeaderView(DatagridViewInterface $datagrid);

    /**
     * @param DatagridViewInterface $datagrid
     * @param object                $object
     * @param int|string            $index
     *
     * @return CellView
     */
    public function createCellView(DatagridViewInterface $datagrid, $object, $index);

    /**
     * Binds data into object using DataMapper object.
     *
     * @param mixed      $data
     * @param object     $object
     * @param int|string $index
     */
    public function bindData($data, $object, $index);
}
