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

namespace Rollerworks\Component\Datagrid\Util;

use Rollerworks\Component\Datagrid\DatagridBuilderInterface;

interface CompoundColumnBuilderInterface
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
    public function add(string $name, string $type = null, array $options = []): CompoundColumnBuilderInterface;

    /**
     * Remove a column from the builder.
     *
     * @return self
     */
    public function remove(string $name): CompoundColumnBuilderInterface;

    /**
     * Returns whether the builder has a column with the name.
     *
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * Ends the CompoundColumnBuilder process and registers the CompoundColumn
     * at the DatagridBuilder instance.
     *
     * @return DatagridBuilderInterface
     */
    public function end(): DatagridBuilderInterface;
}
