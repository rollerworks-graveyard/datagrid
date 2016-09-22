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
use Rollerworks\Component\Datagrid\Extension\Core\DataTransformer\MoneyToLocalizedStringTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class MoneyType extends AbstractType
{
    public function buildColumn(ColumnInterface $column, array $options)
    {
        $column->addViewTransformer(new MoneyToLocalizedStringTransformer(
            $options['precision'],
            $options['grouping'],
            $options['rounding_mode'],
            $options['divisor'],
            $options['currency']
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'precision' => 2,
            'grouping' => false,
            'divisor' => 1,
            'currency' => 'EUR',
            'rounding_mode' => \NumberFormatter::ROUND_HALFUP,
        ]);

        $resolver->setAllowedValues(
            'rounding_mode',
            [
                \NumberFormatter::ROUND_FLOOR,
                \NumberFormatter::ROUND_DOWN,
                \NumberFormatter::ROUND_HALFDOWN,
                \NumberFormatter::ROUND_HALFEVEN,
                \NumberFormatter::ROUND_HALFUP,
                \NumberFormatter::ROUND_UP,
                \NumberFormatter::ROUND_CEILING,
            ]
        );
    }
}
