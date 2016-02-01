<?php

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
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author FSi sp. z o.o. <info@fsi.pl>
 */
class BooleanType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return TextType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'true_value' => 'true',
            'false_value' => 'false',
            'value_format' => function (Options $options) {
                $trueValue = $options['true_value'];
                $falseValue = $options['false_value'];

                // Return a closure for later execution (the actual formatter)
                return function ($value) use ($trueValue, $falseValue) {
                    $value = (array) $value;

                    $boolValue = true;
                    foreach ($value as $val) {
                        $boolValue = (bool) ($boolValue & (bool) $val);
                        if (!$boolValue) {
                            break;
                        }
                    }

                    return $boolValue ? $trueValue : $falseValue;
                };
            },
        ]);
    }
}
