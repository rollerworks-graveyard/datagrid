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
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class ModelType extends AbstractColumnType
{
    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    /**
     * ColumnType constructor.
     *
     * @param PropertyAccessor|null $propertyAccessor
     */
    public function __construct(PropertyAccessor $propertyAccessor = null)
    {
        if (null === $propertyAccessor) {
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
        }

        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function buildColumn(ColumnInterface $column, array $options)
    {
        $nestedTransformer = new NestedListTransformer();

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

        $column->addViewTransformer(new ModelToArrayTransformer($this->propertyAccessor, $options['model_fields']));
        $column->addViewTransformer($nestedTransformer);

        // Only perform the final formatting when the model is transformed
        if ($isFormatted) {
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
            'model_value_glue' => null,
            'model_value_format' => null,
            'model_empty_value' => null,

            'value_glue' => null,
            'value_format' => null,
            'empty_value' => null,
        ]);

        $resolver->setRequired(['model_fields']);
        $resolver->setAllowedTypes('model_value_glue', ['string', 'null']);
        $resolver->setAllowedTypes('model_value_format', ['string', 'callable', 'null']);
        $resolver->setAllowedTypes('model_fields', ['array']);
        $resolver->setAllowedTypes('model_empty_value', ['string', 'null']);
        $resolver->setAllowedTypes('value_glue', ['string', 'null']);
        $resolver->setAllowedTypes('value_format', ['string', 'callable', 'null']);
        $resolver->setAllowedTypes('empty_value', ['string', 'null']);
    }
}
