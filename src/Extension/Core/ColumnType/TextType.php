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
use Rollerworks\Component\Datagrid\Column\ColumnInterface;
use Rollerworks\Component\Datagrid\Extension\Core\DataTransformer\TrimTransformer;
use Rollerworks\Component\Datagrid\Extension\Core\DataTransformer\ValueFormatTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TextType extends AbstractColumnType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'text';
    }

    /**
     * {@inheritdoc}
     */
    public function buildColumn(ColumnInterface $column, array $options)
    {
        if ($options['trim']) {
            $column->addViewTransformer(new TrimTransformer());
        }

        if (null !== $options['empty_value'] || null !== $options['value_format'] || null !== $options['value_glue']) {
            $column->addViewTransformer(
               new ValueFormatTransformer(
                   $options['empty_value'],
                   $options['value_glue'],
                   $options['value_format'],
                   $options['field_mapping']
               )
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'trim' => false,
            'value_glue' => null,
            'value_format' => null,
            'empty_value' => null,
        ]);

        if ($resolver instanceof OptionsResolverInterface) {
            $resolver->setAllowedTypes(
                [
                    'trim' => 'bool',
                    'value_glue' => ['string', 'null'],
                    'value_format' => [
                        'string',
                        'callable',
                        'null',
                    ],
                    'empty_value' => ['string', 'array', 'null'],
                ]
            );
        } else {
            $resolver->setAllowedTypes('trim', 'bool');
            $resolver->setAllowedTypes('value_glue', ['string', 'null']);
            $resolver->setAllowedTypes('value_format', ['string', 'callable', 'null']);
            $resolver->setAllowedTypes('empty_value', ['string', 'array', 'null']);
        }
    }
}
