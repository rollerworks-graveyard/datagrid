<?php declare(strict_types=1);

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
     * @param array           $options
     */
    public function buildColumn(ColumnInterface $column, array $options);

    /**
     * @param HeaderView      $view
     * @param ColumnInterface $column
     * @param array           $options
     */
    public function buildHeaderView(HeaderView $view, ColumnInterface $column, array $options);

    /**
     * @param CellView        $view
     * @param ColumnInterface $column
     * @param array           $options
     */
    public function buildCellView(CellView $view, ColumnInterface $column, array $options);

    /**
     * Configures the default options for this type.
     *
     * @param OptionsResolver $optionsResolver
     */
    public function configureOptions(OptionsResolver $optionsResolver);

    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType(): string;
}
