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
use Rollerworks\Component\Datagrid\Util\StringUtil;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ColumnType implements ColumnTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['field_mapping', 'label']);
        $resolver->setDefaults(['field_mapping_single' => true]);

        // BC layer for Symfony 2.7 and 3.0
        if ($resolver instanceof OptionsResolverInterface) {
            $resolver->setAllowedTypes(
                [
                    'label' => 'string',
                    'field_mapping' => 'array',
                    'field_mapping_single' => 'bool',
                ]
            );
        } else {
            $resolver->setAllowedTypes('label', 'string');
            $resolver->setAllowedTypes('field_mapping', 'array');
            $resolver->setAllowedTypes('field_mapping_single', 'bool');
        }
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
    public function getParent()
    {
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * @return string The prefix of the template block name.
     */
    public function getBlockPrefix()
    {
        return StringUtil::fqcnToBlockPrefix(get_class($this));
    }
}
