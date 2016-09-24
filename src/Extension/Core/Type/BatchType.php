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

namespace Rollerworks\Component\Datagrid\Extension\Core\Type;

use Rollerworks\Component\Datagrid\Column\AbstractType;
use Rollerworks\Component\Datagrid\Column\CellView;
use Rollerworks\Component\Datagrid\Column\ColumnInterface;
use Rollerworks\Component\Datagrid\Column\HeaderView;
use Rollerworks\Component\Datagrid\Exception\UnexpectedTypeException;

class BatchType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildCellView(CellView $view, ColumnInterface $column, array $options)
    {
        if (!is_scalar($view->value)) {
            throw new UnexpectedTypeException($view->value, 'scalar');
        }

        $id = str_replace(':', '-', sprintf('%s-%s__%s', $view->datagrid->name, $view->name, $view->value));

        // Strip leading underscores and digits. These are allowed in
        // form names, but not in HTML4 ID attributes.
        // http://www.w3.org/TR/html401/struct/global.html#adef-id
        $id = ltrim($id, '_0123456789');

        $view->attributes['datagrid_name'] = $view->datagrid->name;
        $view->attributes['selection_name'] = sprintf('%s[%s][]', $view->datagrid->name, $view->name);
        $view->attributes['selection_id'] = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function buildHeaderView(HeaderView $view, ColumnInterface $column, array $options)
    {
        $view->attributes['datagrid_name'] = $view->datagrid->name;
    }
}
