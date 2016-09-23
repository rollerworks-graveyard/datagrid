<?php

declare(strict_types=1);

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Column;

use Rollerworks\Component\Datagrid\DatagridView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * A wrapper for a column type and its extensions.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
interface ResolvedColumnTypeInterface
{
    /**
     * Returns the prefix of the template block name for this type.
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix(): string;

    /**
     * Returns the parent type.
     *
     * @return ResolvedColumnTypeInterface|null The parent type or null
     */
    public function getParent();

    /**
     * Returns the wrapped column type.
     *
     * @return ColumnTypeInterface The wrapped column type
     */
    public function getInnerType(): ColumnTypeInterface;

    /**
     * Returns the extensions of the wrapped column type.
     *
     * @return ColumnTypeExtensionInterface[] An array of {@link ColumnTypeExtensionInterface} instances
     */
    public function getTypeExtensions(): array;

    /**
     * Returns a new ColumnInterface instance.
     *
     * @param string $name
     * @param array  $options
     *
     * @return ColumnInterface
     */
    public function createColumn(string $name, array $options = []): ColumnInterface;

    /**
     * This configures the {@link ColumnInterface} instance.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the column.
     *
     * @param ColumnInterface $config
     * @param array           $options
     */
    public function buildType(ColumnInterface $config, array $options);

    /**
     * Creates a new header view for a column of this type.
     *
     * @param ColumnInterface $column
     * @param DatagridView    $datagrid
     *
     * @return HeaderView
     */
    public function createHeaderView(ColumnInterface $column, DatagridView $datagrid): HeaderView;

    /**
     * Creates a new cell view for a column of this type.
     *
     * @param ColumnInterface $column
     * @param DatagridView    $datagrid
     *
     * @return CellView
     */
    public function createCellView(ColumnInterface $column, DatagridView $datagrid): CellView;

    /**
     * Configures a header view for the type hierarchy.
     *
     * @param HeaderView      $view
     * @param ColumnInterface $column
     * @param array           $options
     */
    public function buildHeaderView(HeaderView $view, ColumnInterface $column, array $options);

    /**
     * Configures a cell view for the type hierarchy.
     *
     * @param CellView        $view
     * @param ColumnInterface $column
     * @param array           $options
     */
    public function buildCellView(CellView $view, ColumnInterface $column, array $options);

    /**
     * Returns the configured options resolver used for this type.
     *
     * @return OptionsResolver The options resolver
     */
    public function getOptionsResolver(): OptionsResolver;

    /**
     * Get the value of the column.
     *
     * @param ColumnInterface $column
     * @param mixed           $object
     *
     * @return mixed
     */
    public function getValue(ColumnInterface $column, $object);
}
