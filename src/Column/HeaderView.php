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

use Rollerworks\Component\Datagrid\BaseView;
use Rollerworks\Component\Datagrid\DatagridView;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class HeaderView extends BaseView
{
    /**
     * @var string
     */
    public $label;

    /**
     * @var DatagridView
     */
    public $datagrid;

    /**
     * @var string
     */
    public $name;

    /**
     * @param ColumnInterface $column
     * @param DatagridView    $datagrid
     * @param string          $label
     */
    public function __construct(ColumnInterface $column, DatagridView $datagrid, string $label = null)
    {
        $this->datagrid = $datagrid;
        $this->name = $column->getName();
        $this->label = $label;
    }
}
