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
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
interface ColumnTypeExtensionInterface
{
    /**
     * @param ColumnInterface $column
     */
    public function buildColumn(ColumnInterface $column);

    /**
     * @param ColumnInterface $column
     * @param HeaderView      $view
     */
    public function buildHeaderView(ColumnInterface $column, HeaderView $view);

    /**
     * @param ColumnInterface $column
     * @param CellView        $view
     */
    public function buildCellView(ColumnInterface $column, CellView $view);

    /**
     * Configures the default options for this type.
     *
     * @param OptionsResolverInterface $optionsResolver
     */
    public function setDefaultOptions(OptionsResolverInterface $optionsResolver);

    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType();
}
