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

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
interface DatagridFactoryInterface
{
    /**
     * Create a new ColumnInterface instance.
     *
     * @param string $name
     * @param string $type
     * @param array  $options
     *
     * @return ColumnInterface
     */
    public function createColumn(string $name, string $type, array $options = []): ColumnInterface;

    /**
     * Create a new DatagridInterface instance with a unique name.
     *
     * @param string|DatagridConfiguratorInterface $configurator Configurator for building the datagrid,
     *                                                           a string will be resolved to a configurator
     * @param string                               $name         Name of the datagrid,
     *                                                           defaults to the simple name of the configurator
     * @param array                                $options      Additional options for the configurator
     *
     * @return DatagridInterface
     */
    public function createDatagrid($configurator, string $name = null, array $options = []): DatagridInterface;

    /**
     * Create a new DatagridBuilderInterface instance.
     *
     * @return DatagridBuilderInterface
     */
    public function createDatagridBuilder(): DatagridBuilderInterface;
}
