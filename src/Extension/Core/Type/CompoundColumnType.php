<?php declare(strict_types=1);

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
use Rollerworks\Component\Datagrid\Exception\UnexpectedTypeException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * CompoundColumn allows multiple sub-columns for advanced view building.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class CompoundColumnType extends AbstractType
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
        $resolver->setRequired(['columns']);
        $resolver->setDefault('label', null);

        // Not used but required by the ResolvedColumnType
        $resolver->setAllowedTypes('label', ['string', 'null']);
        $resolver->setAllowedTypes('columns', 'array');
    }

    /**
     * {@inheritdoc}
     */
    public function buildColumn(ColumnInterface $column, array $options)
    {
        // Simple pass all $data to the sub-columns;
        $column->setDataProvider(
            function ($data) {
                return $data;
            }
        );
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
