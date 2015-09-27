<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Extension\Core\ColumnType;

use Rollerworks\Component\Datagrid\Column\AbstractColumnType;
use Rollerworks\Component\Datagrid\Column\CellView;
use Rollerworks\Component\Datagrid\Column\ColumnInterface;
use Rollerworks\Component\Datagrid\Exception\UnexpectedTypeException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * CompoundColumn allows multiple sub-columns for advanced view building.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class CompoundColumnType extends AbstractColumnType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['label', 'columns']);

        // Not used but required by the ResolvedColumnType
        $resolver->setDefaults(['field_mapping' => []]);

        if ($resolver instanceof OptionsResolverInterface) {
            $resolver->setAllowedTypes(
                [
                    'label' => 'string',
                    'columns' => 'array',
                ]
            );
        } else {
            $resolver->setAllowedTypes('label', 'string');
            $resolver->setAllowedTypes('columns', 'array');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildCellView(CellView $view, ColumnInterface $column, array $options)
    {
        $cells = [];

        foreach ($options['columns'] as $subColumn) {
            if (!$subColumn instanceof ColumnInterface) {
                throw new UnexpectedTypeException($subColumn, ColumnInterface::class);
            }

            $subView = $subColumn->createCellView($view->datagrid, $view->source, $view->attributes['row']);
            $subView->attributes['compound'] = true;

            $cells[$subColumn->getName()] = $subView;
        }

        return $view->value = $cells;
    }
}
