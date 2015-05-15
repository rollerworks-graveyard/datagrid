<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Exception;

use Rollerworks\Component\Datagrid\DatagridInterface;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class UnknownColumnException extends \InvalidArgumentException implements ExceptionInterface
{
    /**
     * @var DatagridInterface
     */
    private $datagrid;

    /**
     * @var string
     */
    private $columnName;

    /**
     * @param string            $columnName
     * @param DatagridInterface $datagrid
     */
    public function __construct($columnName, DatagridInterface $datagrid)
    {
        $this->datagrid = $datagrid;
        $this->columnName = $columnName;

        parent::__construct(
            sprintf('Column "%s" is not registered in Datagrid "%s".', $columnName, $datagrid->getName())
        );
    }

    /**
     * @return DatagridInterface
     */
    public function getDatagrid()
    {
        return $this->datagrid;
    }

    /**
     * @return string
     */
    public function getColumnName()
    {
        return $this->columnName;
    }
}
