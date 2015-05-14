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

class UnexpectedTypeException extends DatagridException
{
    /**
     * @param string       $value
     * @param string|array $expectedType
     */
    public function __construct($value, $expectedType)
    {
        if (is_array($expectedType)) {
            $expectedType = implode('", "', $expectedType);
        }

        parent::__construct(sprintf('Expected argument of type "%s", "%s" given', $expectedType, is_object($value) ? get_class($value) : gettype($value)));
    }
}
