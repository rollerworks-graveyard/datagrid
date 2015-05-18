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
use Rollerworks\Component\Datagrid\DataMapper\DataMapperInterface;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
interface DatagridFactoryInterface
{
    /**
     * Create a new ColumnInterface instance.
     *
     * @param string            $name
     * @param string            $type
     * @param DatagridInterface $datagrid
     * @param array             $options
     *
     * @return ColumnInterface
     */
    public function createColumn($name, $type, DatagridInterface $datagrid, array $options = []);

    /**
     * Create a new DatagridInterface instance with a unique name.
     *
     * @param string $name
     *
     * @return DatagridInterface
     */
    public function createDatagrid($name);

    /**
     * Create a new DatagridBuilderInterface instance.
     *
     * @param string              $name
     * @param DataMapperInterface $dataMapper
     *
     * @return DatagridInterface
     */
    public function createDatagridBuilder($name, DataMapperInterface $dataMapper = null);

    /**
     * Get the Datagrid DataMapper.
     *
     * @return DataMapperInterface
     */
    public function getDataMapper();
}
