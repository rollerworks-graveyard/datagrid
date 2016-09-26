<?php

declare(strict_types=1);

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
use Rollerworks\Component\Datagrid\Column\ColumnInterface;
use Rollerworks\Component\Datagrid\Extension\Core\DataTransformer\ChainTransformer;
use Rollerworks\Component\Datagrid\Extension\Core\DataTransformer\EmptyValueTransformer;
use Rollerworks\Component\Datagrid\Extension\Core\DataTransformer\ValueFormatTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TextType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildColumn(ColumnInterface $column, array $options)
    {
        $transformer = new ChainTransformer();

        if (null !== $options['empty_value']) {
            $transformer->append(new EmptyValueTransformer($options['empty_value']));
        }

        if (null !== $options['value_format'] || null !== $options['value_glue']) {
            $transformer->append(new ValueFormatTransformer($options['value_glue'], $options['value_format']));
        }

        $column->setViewTransformer($transformer);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'value_glue' => null,
            'value_format' => null,
            'empty_value' => null,
        ]);

        $resolver->setAllowedTypes('value_glue', ['string', 'null']);
        $resolver->setAllowedTypes('value_format', ['string', 'callable', 'null']);
        $resolver->setAllowedTypes('empty_value', ['string', 'array', 'null']);
    }
}
