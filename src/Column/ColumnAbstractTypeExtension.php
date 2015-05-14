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

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Sebastiaan Stok <s.stok@rollerscaps.net>
 */
abstract class ColumnAbstractTypeExtension implements ColumnTypeExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildColumn(ColumnInterface $column)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildHeaderView(ColumnInterface $column, HeaderView $view)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildCellView(ColumnInterface $column, CellView $view)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $optionsResolver)
    {
    }
}
