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
use Rollerworks\Component\Datagrid\Exception\UnexpectedTypeException;
use Rollerworks\Component\Datagrid\Extension\Core\DataTransformer\CompoundColumnTransformer;
use Rollerworks\Component\Datagrid\Extension\Core\DataTransformer\ValueFormatTransformer;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * CompoundColumn allows multiple sub-columns for advanced view building.
 *
 * One can reference this type as parent and set the options
 * 'value_glue' and 'value_format' to null and then build the view manually.
 * Values are then passed by name and transformed value.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class CompoundColumnType extends AbstractColumnType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'compound_column';
    }

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

        $resolver->setDefaults([
            'value_glue' => '<br>',
            'value_format' => null,
            'field_mapping' => function (Options $options, $fieldMapping) {
                if ($fieldMapping) {
                    return $fieldMapping;
                }

                // No mapping was set load the mapping from the children
                $fieldMapping = [];

                foreach ($options['columns'] as $subColumn) {
                    if (!$subColumn instanceof ColumnInterface) {
                        throw new UnexpectedTypeException($subColumn, ColumnInterface::class);
                    }

                    $fieldMapping = array_merge($fieldMapping, $subColumn->getOption('field_mapping', []));
                }

                $fieldMapping['__no_check__'] = true;

                return $fieldMapping;
            },
        ]);

        $resolver->setAllowedTypes([
            'label' => 'string',
            'field_mapping' => 'array',
            'columns' => 'array',
            'value_glue' => ['string', 'null'],
            'value_format' => [
                'string',
                'callable',
                'null',
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildColumn(ColumnInterface $column, array $options)
    {
        // Skip the type checking if the lazy option-resolver has already done that
        // This improves performance
        if (!isset($options['columns']['__no_check__'])) {
            foreach ($options['columns'] as $subColumn) {
                if (!$subColumn instanceof ColumnInterface) {
                    throw new UnexpectedTypeException($subColumn, 'Rollerworks\Component\Datagrid\Column\ColumnInterface');
                }
            }
        }

        $column->addViewTransformer(new CompoundColumnTransformer($options['columns']));

        // Don't perform the formatter when neither glue or value_format is set
        // This should only be done by a child-type which handles the View manually
        if (null !== $options['value_glue'] || null !== $options['value_format']) {
            $column->addViewTransformer(new ValueFormatTransformer('', $options['value_glue'], $options['value_format']));
        }
    }
}
