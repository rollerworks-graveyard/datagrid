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

use Rollerworks\Component\Datagrid\Column\CellView;
use Rollerworks\Component\Datagrid\Column\ColumnInterface;
use Rollerworks\Component\Datagrid\Column\ColumnTypeInterface;
use Rollerworks\Component\Datagrid\Column\HeaderView;
use Rollerworks\Component\Datagrid\Extension\Core\DataTransformer\SingleMappingTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ColumnType implements ColumnTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['field_mapping', 'label']);
        $resolver->setDefaults(['field_mapping_single' => true]);

        $resolver->setAllowedTypes([
            'label' => 'string',
            'field_mapping' => 'array',
            'field_mapping_single' => 'bool',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildColumn(ColumnInterface $column, array $options)
    {
        // Its save to use this as an array-value is always ignored
        // And the transformer always receives all mapping fields
        if (true === $options['field_mapping_single']) {
            $column->addViewTransformer(new SingleMappingTransformer($options['field_mapping']));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildCellView(CellView $view, ColumnInterface $column, array $options)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildHeaderView(HeaderView $view, ColumnInterface $column, array $options)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'column';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return;
    }
}
