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

interface ColumnTypeExtensionWithPostTransform
{
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
}
