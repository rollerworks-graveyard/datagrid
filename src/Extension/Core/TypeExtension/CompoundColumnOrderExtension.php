<?php declare(strict_types=1);

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Extension\Core\TypeExtension;

use Rollerworks\Component\Datagrid\Extension\Core\Type\CompoundColumnType;

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
    public function getExtendedType(): string
    {
        return CompoundColumnType::class;
    }
}
