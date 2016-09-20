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
     * @param string            $name    Name of the datagrid
     * @param ColumnInterface[] $columns Columns of the datagrid
     *
     * @return DatagridInterface
     */
    public function createDatagrid(string $name, array $columns): DatagridInterface;

    /**
     * Create a new DatagridBuilderInterface instance.
     *
     * @return DatagridBuilderInterface
     */
    public function createDatagridBuilder(): DatagridBuilderInterface;
}
