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

use Rollerworks\Component\Datagrid\Column\ColumnTypeExtensionInterface;
use Rollerworks\Component\Datagrid\Column\ColumnTypeInterface;
use Rollerworks\Component\Datagrid\Exception\DatagridException;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
interface DatagridExtensionInterface
{
    /**
     * Check if extension has column type of $type.
     *
     * @param string $type
     *
     * @return bool
     */
    public function hasType($type);

    /**
     * Get column type.
     *
     * @param string $type
     *
     * @throws DatagridException When the given column type is not provided by this extension
     *
     * @return ColumnTypeInterface
     */
    public function getType($type);

    /**
     * Check if extension has any column type extension for column of $type.
     *
     * @param string $type
     *
     * @return bool
     */
    public function hasTypeExtensions($type);

    /**
     * Return extensions for column type provided by this data grid extension.
     *
     * @param string $type
     *
     * @return ColumnTypeExtensionInterface[]
     */
    public function getTypeExtensions($type);
}
