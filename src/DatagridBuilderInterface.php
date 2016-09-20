<?php declare(strict_types=1);

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

interface DatagridBuilderInterface
{
    /**
     * Add a column to the datagrid builder.
     *
     * @param string|ColumnInterface     $field
     * @param string|ColumnTypeInterface $type
     * @param array                      $options
     *
     * @return DatagridBuilderInterface
     */
    public function add($field, $type = null, array $options = []);

    /**
     * Remove a column from the datagrid builder.
     *
     * @param string $name
     *
     * @return DatagridBuilderInterface
     */
    public function remove($name);

    /**
     * Returns whether the datagrid builder has a column with
     * the specified name.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has($name);

    /**
     * Get the registered column by name.
     *
     * @param string $name
     *
     * @return ColumnInterface
     */
    public function get($name);

    /**
     * Return the configured datagrid instance.
     *
     * @return DatagridInterface
     */
    public function getDatagrid();
}
