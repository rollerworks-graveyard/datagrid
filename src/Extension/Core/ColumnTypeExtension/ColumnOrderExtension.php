<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Extension\Core\ColumnTypeExtension;

use Rollerworks\Component\Datagrid\Column\ColumnAbstractTypeExtension;
use Rollerworks\Component\Datagrid\Column\ColumnInterface;
use Rollerworks\Component\Datagrid\Column\HeaderView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Allows to set the column sorting order.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class ColumnOrderExtension extends ColumnAbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildHeaderView(ColumnInterface $column, HeaderView $view)
    {
        if (!is_null($order = $column->getOption('display_order'))) {
            $view->setAttribute('display_order', $order);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'column';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'display_order' => null,
        ]);

        $resolver->setAllowedTypes([
            'display_order' => [
                'integer',
                'null',
            ],
        ]);
    }
}
