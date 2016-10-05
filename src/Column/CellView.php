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
 * CellView provides the data for rendering the Datagrid cell.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class CellView extends BaseView
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
     * @var HeaderView
     */
    public $column;

    /**
     * @var DatagridView
     */
    public $datagrid;

    /**
     * @var string
     */
    public $name;

    /**
     * Constructor.
     *
     * @param HeaderView   $column
     * @param DatagridView $datagrid
     */
    public function __construct(HeaderView $column, DatagridView $datagrid)
    {
        $this->name = $column->name;
        $this->datagrid = $datagrid;
        $this->column = $column;
    }
}
