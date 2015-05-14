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

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class UnknownColumnException extends \InvalidArgumentException implements ExceptionInterface
{
    /**
     * @param string $fieldName
     * @param string $datagrid
     */
    public function __construct($fieldName, $datagrid)
    {
        parent::__construct(sprintf('Column "%s" is not registered in the Datagrid.', $fieldName, $datagrid));
    }
}
