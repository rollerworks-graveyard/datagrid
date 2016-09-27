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

use Rollerworks\Component\Datagrid\Exception\InvalidArgumentException;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
interface DatagridRegistryInterface
{
    /**
     * Returns a DatagridConfigurator by name.
     *
     * @param string $name The name of the datagrid configurator
     *
     * @throws InvalidArgumentException if the configurator can not be retrieved
     *
     * @return DatagridConfiguratorInterface
     */
    public function getConfigurator(string $name): DatagridConfiguratorInterface;

    /**
     * Returns whether the given DatagridConfigurator is supported.
     *
     * @param string $name The name of the datagrid configurator
     *
     * @return bool
     */
    public function hasConfigurator(string $name): bool;
}
