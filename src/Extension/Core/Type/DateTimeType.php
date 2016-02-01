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
use Rollerworks\Component\Datagrid\Column\ColumnInterface;
use Rollerworks\Component\Datagrid\Extension\Core\DataTransformer\ArrayToDateTimeTransformer;
use Rollerworks\Component\Datagrid\Extension\Core\DataTransformer\DateTimeToLocalizedStringTransformer;
use Rollerworks\Component\Datagrid\Extension\Core\DataTransformer\StringToDateTimeTransformer;
use Rollerworks\Component\Datagrid\Extension\Core\DataTransformer\TimestampToDateTimeTransformer;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * DateTimeType.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author Florian Eckerstorfer <florian@eckerstorfer.org>
 */
class DateTimeType extends AbstractType
{
    const DEFAULT_DATE_FORMAT = \IntlDateFormatter::MEDIUM;
    const DEFAULT_TIME_FORMAT = \IntlDateFormatter::MEDIUM;

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
        $dateFormat = is_int($options['date_format']) ? $options['date_format'] : self::DEFAULT_DATE_FORMAT;
        $timeFormat = is_int($options['time_format']) ? $options['time_format'] : self::DEFAULT_TIME_FORMAT;
        $calendar = \IntlDateFormatter::GREGORIAN;
        $pattern = is_string($options['format']) ? $options['format'] : null;

        if (!in_array($dateFormat, self::$acceptedFormats, true)) {
            throw new InvalidOptionsException('The "date_format" option must be one of the IntlDateFormatter constants (FULL, LONG, MEDIUM, SHORT) or a string representing a custom format.');
        }

        if ('string' === $options['input']) {
            $column->addViewTransformer(
                new StringToDateTimeTransformer($options['model_timezone'], $options['model_timezone'])
            );
        } elseif ('timestamp' === $options['input']) {
            $column->addViewTransformer(
                new TimestampToDateTimeTransformer($options['model_timezone'], $options['model_timezone'])
            );
        } elseif ('array' === $options['input']) {
            $parts = ['year', 'month', 'day', 'hour'];
            if (!is_int($timeFormat) && false !== stripos($timeFormat, 'h')) {
                $parts[] = 'minute';
            }

            if (!is_int($timeFormat) && false !== stripos($timeFormat, 's')) {
                $parts[] = 'second';
            }

            $column->addViewTransformer(
                new ArrayToDateTimeTransformer($options['model_timezone'], $options['model_timezone'], $parts)
            );
        }

        $column->addViewTransformer(new DateTimeToLocalizedStringTransformer(
            $options['model_timezone'],
            $options['view_timezone'],
            $dateFormat,
            $timeFormat,
            $calendar,
            $pattern
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
            'format' => null,
        ]);

        $resolver->setAllowedTypes('input', ['array', 'string']);
        $resolver->setAllowedTypes('model_timezone', ['null', 'string']);
        $resolver->setAllowedTypes('view_timezone', ['null', 'string']);
        $resolver->setAllowedTypes('date_format', ['null', 'string', 'integer']);
        $resolver->setAllowedTypes('format', ['null', 'string']);
        $resolver->setAllowedValues('input', ['string', 'timestamp', 'datetime', 'array']);
    }
}
