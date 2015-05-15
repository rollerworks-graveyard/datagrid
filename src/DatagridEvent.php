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

use Symfony\Component\EventDispatcher\Event;

class DatagridEvent extends Event
{
    /**
     * @var DatagridInterface
     */
    private $datagrid;

    /**
     * @var array|\Traversable
     */
    private $data;

    /**
     * Constructor.
     *
     * @param DatagridInterface  $datagrid
     * @param array|\Traversable $data
     */
    public function __construct(DatagridInterface $datagrid, $data)
    {
        $this->datagrid = $datagrid;
        $this->data = $data;
    }

    /**
     * Get the datagrid object.
     *
     * @return DatagridInterface
     */
    public function getDatagrid()
    {
        return $this->datagrid;
    }

    /**
     * Return the data set on the datagrid.
     *
     * @return array|\Traversable
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the data set on the datagrid.
     *
     * Allows updating data for example if you need
     * to filter values.
     *
     * @param array|\Traversable $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}
