<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Extension\Core\ColumnTypeExtension;

use Rollerworks\Component\Datagrid\Extension\Core\ColumnType\CompoundColumnType;

/**
 * Allows to set the compound-column sorting order.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class CompoundColumnOrderExtension extends ColumnOrderExtension
{
    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return CompoundColumnType::class;
    }
}
