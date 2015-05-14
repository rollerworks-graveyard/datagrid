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

class ResolvedColumnTypeFactory implements ResolvedColumnTypeFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createResolvedType(ColumnTypeInterface $type, array $typeExtensions, ResolvedColumnTypeInterface $parent = null)
    {
        return new ResolvedColumnType($type, $typeExtensions, $parent);
    }
}
