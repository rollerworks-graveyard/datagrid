<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Extension\Core;

use Rollerworks\Component\Datagrid\AbstractDatagridExtension;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class CoreExtension extends AbstractDatagridExtension
{
    /**
     * {@inheritdoc}
     */
    protected function loadColumnTypes()
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        return [
            new ColumnType\ColumnType($propertyAccessor),
            new ColumnType\CompoundColumnType(),

            new ColumnType\ActionType(),
            new ColumnType\BatchType(),
            new ColumnType\BooleanType(),
            new ColumnType\DateTimeType(),
            new ColumnType\ModelType($propertyAccessor),
            new ColumnType\MoneyType(),
            new ColumnType\NumberType(),
            new ColumnType\TextType(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function loadColumnTypesExtensions()
    {
        return [
            new ColumnTypeExtension\ColumnOrderExtension(),
            new ColumnTypeExtension\CompoundColumnOrderExtension(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function loadSubscribers()
    {
        return [
            new EventListener\ColumnOrderListener(),
        ];
    }
}
