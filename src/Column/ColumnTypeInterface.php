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
interface ColumnTypeInterface
{
    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver);

    /**
     * Configures the column.
     *
     * @param ColumnInterface $column  The column
     * @param array           $options The resolved options
     */
    public function buildColumn(ColumnInterface $column, array $options);

    /**
     * Configures the CellView instance.
     *
     * @param CellView        $view    The view
     * @param ColumnInterface $column  The column
     * @param array           $options The resolved options
     */
    public function buildCellView(CellView $view, ColumnInterface $column, array $options);

    /**
     * Configures the HeaderView instance.
     *
     * @param HeaderView      $view    The view
     * @param ColumnInterface $column  The column
     * @param array           $options The resolved options
     */
    public function buildHeaderView(HeaderView $view, ColumnInterface $column, array $options);

    /**
     * Returns the prefix of the template block name for this type.
     *
     * @return string The prefix of the template block name.
     */
    public function getBlockPrefix();

    /**
     * Returns the fully-qualified class name of the parent type.
     *
     * @return string|null The name of the parent type if any, null otherwise.
     */
    public function getParent();
}
