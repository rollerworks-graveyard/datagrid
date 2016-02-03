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

class DatagridViewEvent extends Event
{
    /**
     * @var DatagridInterface
     */
    private $datagrid;

    /**
     * @var DatagridView
     */
    private $view;

    /**
     * Constructor.
     *
     * @param DatagridInterface $datagrid
     * @param DatagridView      $view
     */
    public function __construct(DatagridInterface $datagrid, DatagridView $view)
    {
        $this->datagrid = $datagrid;
        $this->view = $view;
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
     * @return DatagridView
     */
    public function getView()
    {
        return $this->view;
    }
}
