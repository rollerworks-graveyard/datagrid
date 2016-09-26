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

namespace Rollerworks\Component\Datagrid\Column;

use Rollerworks\Component\Datagrid\DatagridExtensionInterface;
use Rollerworks\Component\Datagrid\Exception\InvalidArgumentException;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
interface ColumnTypeRegistryInterface
{
    /**
     * Returns a column type by name.
     *
     * This methods registers the type extensions from the datagrid extensions.
     *
     * @param string $name The name of the type
     *
     * @throws InvalidArgumentException if the type can not be retrieved from any extension
     *
     * @return ResolvedColumnTypeInterface The type
     */
    public function getType(string $name): ResolvedColumnTypeInterface;

    /**
     * Returns whether the given column type is supported.
     *
     * @param string $name The name of the type
     *
     * @return bool Whether the type is supported
     */
    public function hasType(string $name): bool;

    /**
     * Returns the extensions loaded by the component.
     *
     * @return DatagridExtensionInterface[]
     */
    public function getExtensions(): array;
}
