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
    protected $datagrid;

    /**
     * @var array|\ArrayAccess|\Traversable
     */
    protected $data;

    /**
     * Constructor.
     *
     * @param DatagridInterface               $datagrid
     * @param array|\ArrayAccess|\Traversable $data
     */
    public function __construct(DatagridInterface $datagrid, $data)
    {
        $this->datagrid = $datagrid;
        $this->data = $data;
    }

    /**
     * @return DatagridInterface
     */
    public function getDatagrid()
    {
        return $this->datagrid;
    }

    /**
     * Returns the data associated with this event.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Allows updating data for example if you need to filter values.
     *
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}
