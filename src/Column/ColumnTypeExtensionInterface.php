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
     * Transform the value before passing it to ColumnTypeInterface::transformValue().
     *
     * Note that this done before the filterValue() method of the type
     * that is extended.
     *
     * So with type: column <- datetime (with extension) <- pubdate.
     * '<-' indicating the parent-type.
     *
     * Will call the preFilterValue() method for "datetime" after the
     * "column" type is filtered!
     *
     * @param mixed           $value
     * @param ColumnInterface $column
     * @param array           $options
     *
     * @return mixed Returns the filtered value
     */
    public function preFilterValue($value, ColumnInterface $column, array $options);

    /**
     * Transform the value before passing it to the view.
     *
     * @param mixed           $value
     * @param ColumnInterface $column
     * @param array           $options
     *
     * @return mixed Returns the filtered value
     */
    public function postTransformValue($value, ColumnInterface $column, array $options);

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
    public function getExtendedType();
}
