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
use Rollerworks\Component\Datagrid\Extension\Core\DataTransformer\DateTimeToLocalizedStringTransformer;
use Rollerworks\Component\Datagrid\Extension\Core\DataTransformer\StringToDateTimeTransformer;
use Rollerworks\Component\Datagrid\Extension\Core\DataTransformer\TimestampToDateTimeTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * DateTimeType.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class DateTimeType extends AbstractType
{
    /**
     * @var array
     */
    private static $acceptedFormats = [
        \IntlDateFormatter::FULL,
        \IntlDateFormatter::LONG,
        \IntlDateFormatter::MEDIUM,
        \IntlDateFormatter::SHORT,
        \IntlDateFormatter::NONE,
    ];

    /**
     * {@inheritdoc}
     */
    public function buildColumn(ColumnInterface $column, array $options)
    {
        if ('string' === $options['input']) {
            $column->addViewTransformer(
                new StringToDateTimeTransformer($options['model_timezone'], $options['model_timezone'])
            );
        } elseif ('timestamp' === $options['input']) {
            $column->addViewTransformer(
                new TimestampToDateTimeTransformer($options['model_timezone'], $options['model_timezone'])
            );
        }

        $column->addViewTransformer(new DateTimeToLocalizedStringTransformer(
            $options['model_timezone'],
            $options['view_timezone'],
            $options['date_format'],
            $options['time_format'],
            $options['calendar'],
            $options['format']
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'input' => 'datetime',
            'model_timezone' => null,
            'view_timezone' => null,
            'date_format' => \IntlDateFormatter::MEDIUM,
            'time_format' => \IntlDateFormatter::MEDIUM,
            'calendar' => \IntlDateFormatter::GREGORIAN,
            'format' => null,
        ]);

        $resolver->setAllowedValues('date_format', self::$acceptedFormats);
        $resolver->setAllowedValues('time_format', self::$acceptedFormats);

        $resolver->setAllowedTypes('model_timezone', ['null', 'string']);
        $resolver->setAllowedTypes('view_timezone', ['null', 'string']);
        $resolver->setAllowedTypes('format', ['null', 'string']);
        $resolver->setAllowedTypes('calendar', ['int', 'IntlCalendar']);
        $resolver->setAllowedValues('input', ['string', 'timestamp', 'datetime']);
    }
}
