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
use Rollerworks\Component\Datagrid\Extension\Core\DataTransformer\ModelToArrayTransformer;
use Rollerworks\Component\Datagrid\Extension\Core\DataTransformer\NestedListTransformer;
use Rollerworks\Component\Datagrid\Extension\Core\DataTransformer\TrimTransformer;
use Rollerworks\Component\Datagrid\Extension\Core\DataTransformer\ValueFormatTransformer;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class ModelType extends AbstractColumnType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'model';
    }

    /**
     * {@inheritdoc}
     */
    public function buildColumn(ColumnInterface $column, array $options)
    {
        if (count($options['field_mapping']) > 2) {
            throw new InvalidOptionsException('Column Type "model" does not support multiple fields mapping, use the "compound_column" to solve this.');
        }

        $nestedTransformer = new NestedListTransformer();

        if ($options['trim']) {
            $nestedTransformer->addTransformer(new TrimTransformer());
        }

        $isFormatted = false;

        if (null !== $options['model_empty_value'] || null !== $options['model_value_format'] || null !== $options['model_value_glue']) {
            $nestedTransformer->addTransformer(
               new ValueFormatTransformer(
                   (string) $options['model_empty_value'],
                   $options['model_value_glue'],
                   $options['model_value_format'],
                   $options['model_fields']
               )
            );

            $isFormatted = true;
        }

        $column->addViewTransformer(new ModelToArrayTransformer($column->getDatagrid()->getDataMapper(), $options['model_fields']));
        $column->addViewTransformer($nestedTransformer);

        // Only perform the final formatting when the model is transformed
        if ($isFormatted) {
            if ($options['trim']) {
                $column->addViewTransformer(new TrimTransformer());
            }

            if (null !== $options['empty_value'] || null !== $options['value_format'] || null !== $options['value_glue']) {
                $column->addViewTransformer(
                   new ValueFormatTransformer(
                       (string) $options['empty_value'],
                       $options['value_glue'],
                       $options['value_format']
                   )
                );
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'model_trim' => false,
            'model_value_glue' => null,
            'model_value_format' => null,
            'model_empty_value' => null,

            'trim' => false,
            'value_glue' => null,
            'value_format' => null,
            'empty_value' => null,
        ]);

        $resolver->setRequired(['model_fields']);

        if ($resolver instanceof OptionsResolverInterface) {
            $resolver->setAllowedTypes(
                [
                    'model_trim' => 'bool',
                    'model_value_glue' => ['string', 'null'],
                    'model_value_format' => ['string', 'callable', 'null'],
                    'model_fields' => ['array'],
                    'model_empty_value' => ['string', 'null'],
                    'trim' => 'bool',
                    'value_glue' => ['string', 'null'],
                    'value_format' => ['string', 'callable', 'null'],
                    'empty_value' => ['string', 'null'],
                ]
            );
        } else {
            $resolver->setAllowedTypes('model_trim', 'bool');
            $resolver->setAllowedTypes('model_value_glue', ['string', 'null']);
            $resolver->setAllowedTypes('model_value_format', ['string', 'callable', 'null']);
            $resolver->setAllowedTypes('model_fields', ['array']);
            $resolver->setAllowedTypes('model_empty_value', ['string', 'null']);
            $resolver->setAllowedTypes('trim', 'bool');
            $resolver->setAllowedTypes('value_glue', ['string', 'null']);
            $resolver->setAllowedTypes('value_format', ['string', 'callable', 'null']);
            $resolver->setAllowedTypes('empty_value', ['string', 'null']);
        }
    }
}
