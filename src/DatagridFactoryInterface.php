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

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
interface DatagridFactoryInterface
{
    /**
     * Create a new Column instance.
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
     * Create a new Datagrid instance with a unique name.
     *
     * @param string $name
     *
     * @return DatagridInterface
     */
    public function createDatagrid($name);

    /**
     * Get the Datagrid DataMapper.
     *
     * @return DataMapper\DataMapperInterface
     */
    public function getDataMapper();
}
