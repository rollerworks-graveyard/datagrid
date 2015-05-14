<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Column;

use Rollerworks\Component\Datagrid\DatagridEvent;

class DatagridColumnEvent extends DatagridEvent
{
    /**
     * @var ColumnInterface
     */
    protected $column;

    /**
     * Constructor.
     *
     * @param ColumnInterface                 $column
     * @param array|\ArrayAccess|\Traversable $data
     */
    public function __construct(ColumnInterface $column, $data)
    {
        parent::__construct($column->getDatagrid(), $data);

        $this->column = $column;
    }

    /**
     * @return ColumnInterface
     */
    public function getColumn()
    {
        return $this->column;
    }
}
