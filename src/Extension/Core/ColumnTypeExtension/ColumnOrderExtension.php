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

use Rollerworks\Component\Datagrid\Column\AbstractColumnTypeExtension;
use Rollerworks\Component\Datagrid\Column\ColumnInterface;
use Rollerworks\Component\Datagrid\Column\HeaderView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Allows to set the column sorting order.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class ColumnOrderExtension extends AbstractColumnTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildHeaderView(HeaderView $view, ColumnInterface $column, array $options)
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
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'display_order' => null,
        ]);

        if ($resolver instanceof OptionsResolverInterface) {
            $resolver->setAllowedTypes(
                [
                    'display_order' => ['integer', 'null'],
                ]
            );
        } else {
            $resolver->setAllowedTypes('display_order', ['integer', 'null']);
        }
    }
}
