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
use Rollerworks\Component\Datagrid\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NumberType extends AbstractColumnType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'number';
    }

    /**
     * {@inheritDoc}
     */
    public function buildColumn(ColumnInterface $column, array $options)
    {
        $column->addViewTransformer(new NumberToLocalizedStringTransformer(
            $options['precision'],
            $options['grouping'],
            $options['rounding_mode']
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // default precision is locale specific (usually around 3)
            'precision' => null,
            'grouping' => false,
            'rounding_mode' => \NumberFormatter::ROUND_HALFUP,
        ]);

        $resolver->setAllowedValues([
            'rounding_mode' => [
                \NumberFormatter::ROUND_FLOOR,
                \NumberFormatter::ROUND_DOWN,
                \NumberFormatter::ROUND_HALFDOWN,
                \NumberFormatter::ROUND_HALFEVEN,
                \NumberFormatter::ROUND_HALFUP,
                \NumberFormatter::ROUND_UP,
                \NumberFormatter::ROUND_CEILING,
            ],
        ]);
    }
}
