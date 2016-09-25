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
use Rollerworks\Component\Datagrid\Util\CompoundColumnBuilderInterface;

interface DatagridBuilderInterface
{
    /**
     * Add a column to the builder.
     *
     * @param string $name
     * @param string $type
     * @param array  $options
     *
     * @return self
     */
    public function add(string $name, string $type = null, array $options = []);

    /**
     * Add a column instance to the builder.
     *
     * @param ColumnInterface $column
     *
     * @return DatagridBuilderInterface
     */
    public function set(ColumnInterface $column);

    /**
     * Create a new CompoundColumnBuilder object.
     *
     * A CompoundColumn allows to group multiple columns together,
     * eg. one more more date value or one or more row actions.
     *
     * <code>
     * createCompound('actions', ['label' => 'Actions'])
     *   ->add('edit', ActionType::class, ['data_provider' => '[id]', 'url_schema' => '/users/{id}/edit'])
     *   ->add('delete', ActionType::class, ['data_provider' => '[id]', 'url_schema' => '/users/{id}/edit'])
     * ->end() // This registers the CompoundColumn at the DatagridBuilder, and return the DatagridBuilder.
     * </code>
     *
     * @param string $name    Name of this CompoundColumn
     * @param array  $options Options for the CompoundColumn
     * @param string $type    Optional type, must be a child-type of CompoundColumnType
     *
     * @return CompoundColumnBuilderInterface
     */
    public function createCompound(string $name, array $options = [], string $type = null): CompoundColumnBuilderInterface;

    /**
     * Remove a column from the builder.
     *
     * @param string $name
     *
     * @return self
     */
    public function remove(string $name);

    /**
     * Returns whether the builder has a column with the name.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * Get the registered column by name.
     *
     * @param string $name
     *
     * @return ColumnInterface
     */
    public function get(string $name): ColumnInterface;

    /**
     * Return the configured datagrid instance.
     *
     * @param string $name
     *
     * @return DatagridInterface
     */
    public function getDatagrid(string $name): DatagridInterface;
}
