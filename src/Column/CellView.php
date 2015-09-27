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

use Rollerworks\Component\Datagrid\DatagridViewInterface;

/**
 * CellView provides the data for rendering the Datagrid cell.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class CellView
{
    /**
     * Cell value.
     *
     * In most cases this will be a simple string.
     *
     * @var mixed
     */
    public $value;

    /**
     * Use the content as raw (as-is) without escaping.
     *
     * WARNING! this requires a save value to prevent XSS.
     *
     * @var bool
     */
    public $useRaw = false;

    /**
     * @var array|object
     */
    public $source;

    /**
     * Cell attributes.
     *
     * @var array
     */
    public $attributes = [];

    /**
     * @var ColumnInterface
     */
    public $column;

    /**
     * @var DatagridViewInterface
     */
    public $datagrid;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type;

    /**
     * Constructor.
     *
     * @param ColumnInterface       $column
     * @param DatagridViewInterface $datagrid
     */
    public function __construct(ColumnInterface $column, DatagridViewInterface $datagrid)
    {
        $this->column = $column;
        $this->datagrid = $datagrid;

        $this->prefix = $column->getType()->getBlockPrefix();
        $this->name = $column->getName();
    }
}
