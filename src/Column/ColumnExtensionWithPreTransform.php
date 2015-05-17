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

interface ColumnExtensionWithPreTransform
{
    /**
     * Transform the value before passing it to ColumnTypeInterface::transformValue().
     *
     * Note that this done before the filterValue() method of the type
     * that is extended.
     *
     * So with type: column <- datetime (with extension) <- pubdate.
     * '<-' indicating the parent-type.
     *
     * Will call the preTransformValue() method for "datetime" after the
     * "column" type is filtered!
     *
     * @param mixed           $value
     * @param ColumnInterface $column
     * @param array           $options
     *
     * @return mixed Returns the filtered value
     */
    public function preTransformValue($value, ColumnInterface $column, array $options);
}
