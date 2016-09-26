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
use Rollerworks\Component\Datagrid\DataTransformerInterface;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
interface ColumnInterface
{
    /**
     * Returns the name of the column in the Datagrid.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Returns the column-type name.
     *
     * @return ResolvedColumnTypeInterface
     */
    public function getType(): ResolvedColumnTypeInterface;

    /**
     * Set the view transform of the column.
     *
     * The transform method of the transformer is used to convert data from the
     * normalized to the view format.
     *
     * @param DataTransformerInterface|null $viewTransformer
     */
    public function setViewTransformer(DataTransformerInterface $viewTransformer = null);

    /**
     * Returns the view transformer of the column.
     *
     * @return DataTransformerInterface|null
     */
    public function getViewTransformer();

    /**
     * Returns all options passed during the construction of the column.
     *
     * @return array The passed options
     */
    public function getOptions(): array;

    /**
     * Returns the value of a specific option.
     *
     * @param string $name    The option name
     * @param mixed  $default The value returned if the option does not exist
     *
     * @return mixed The option value
     */
    public function getOption($name, $default = null);

    /**
     * Returns whether a specific option exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasOption($name): bool;

    /**
     * @param DatagridView $datagrid
     *
     * @return HeaderView
     */
    public function createHeaderView(DatagridView $datagrid): HeaderView;

    /**
     * @param DatagridView $datagrid
     * @param object       $object
     * @param int|string   $index
     *
     * @return CellView
     */
    public function createCellView(DatagridView $datagrid, $object, $index): CellView;

    /**
     * Set the data-provider for the column.
     *
     * @param callable $dataProvider
     */
    public function setDataProvider(callable $dataProvider);

    /**
     * Get data-provider for this column.
     *
     * @return callable
     */
    public function getDataProvider(): callable;
}
