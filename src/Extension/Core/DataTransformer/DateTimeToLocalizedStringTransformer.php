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

namespace Rollerworks\Component\Datagrid\Extension\Core\DataTransformer;

use Rollerworks\Component\Datagrid\Exception\TransformationFailedException;
use Rollerworks\Component\Datagrid\Exception\UnexpectedTypeException;

/**
 * Transforms between a normalized time and a localized time string.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author Florian Eckerstorfer <florian@eckerstorfer.org>
 */
class DateTimeToLocalizedStringTransformer extends BaseDateTimeTransformer
{
    private $dateFormat;
    private $timeFormat;
    private $pattern;
    private $calendar;

    /**
     * Constructor.
     *
     * @see BaseDateTimeTransformer::formats for available format options
     *
     * @param string $inputTimezone  The name of the input timezone
     * @param string $outputTimezone The name of the output timezone
     * @param int    $dateFormat     The date format
     * @param int    $timeFormat     The time format
     * @param int    $calendar       One of the \IntlDateFormatter calendar constants
     * @param string $pattern        A pattern to pass to \IntlDateFormatter
     *
     * @throws UnexpectedTypeException If a format is not supported or if a timezone is not a string
     */
    public function __construct($inputTimezone = null, $outputTimezone = null, $dateFormat = null, $timeFormat = null, $calendar = \IntlDateFormatter::GREGORIAN, $pattern = null)
    {
        parent::__construct($inputTimezone, $outputTimezone);

        if (null === $dateFormat) {
            $dateFormat = \IntlDateFormatter::MEDIUM;
        }

        if (null === $timeFormat) {
            $timeFormat = \IntlDateFormatter::SHORT;
        }

        if (!in_array($dateFormat, self::$formats, true)) {
            throw new UnexpectedTypeException($dateFormat, self::$formats);
        }

        if (!in_array($timeFormat, self::$formats, true)) {
            throw new UnexpectedTypeException($timeFormat, self::$formats);
        }

        $this->dateFormat = $dateFormat;
        $this->timeFormat = $timeFormat;
        $this->calendar = $calendar;
        $this->pattern = $pattern;
    }

    /**
     * Transforms a normalized date into a localized date string.
     *
     * @param \DateTime $dateTime Normalized date
     *
     * @throws TransformationFailedException If the given value is not an instance
     *                                       of \DateTime or if the date could not
     *                                       be transformed
     *
     * @return string|array Localized date string/array
     */
    public function transform($dateTime)
    {
        if (null === $dateTime) {
            return '';
        }

        if (!$dateTime instanceof \DateTime) {
            throw new TransformationFailedException('Expected a \DateTime.');
        }

        // convert time to UTC before passing it to the formatter
        $dateTime = clone $dateTime;
        if ('UTC' !== $this->inputTimezone) {
            $dateTime->setTimezone(new \DateTimeZone('UTC'));
        }

        $value = $this->getIntlDateFormatter()->format((int) $dateTime->format('U'));

        if (intl_get_error_code() != 0) {
            throw new TransformationFailedException(intl_get_error_message());
        }

        return $value;
    }

    /**
     * Returns a preconfigured IntlDateFormatter instance.
     *
     * @return \IntlDateFormatter
     */
    protected function getIntlDateFormatter()
    {
        $dateFormat = $this->dateFormat;
        $timeFormat = $this->timeFormat;
        $timezone = $this->outputTimezone;
        $calendar = $this->calendar;
        $pattern = $this->pattern ?? '';

        $intlDateFormatter = new \IntlDateFormatter(\Locale::getDefault(), $dateFormat, $timeFormat, $timezone, $calendar, $pattern);
        $intlDateFormatter->setLenient(false);

        // Ensure the year is always four digits when no pattern is set
        if (!$pattern) {
            $intlDateFormatter->setPattern(str_replace(['yy', 'yyyyyyyy'], 'yyyy', $intlDateFormatter->getPattern()));
        }

        return $intlDateFormatter;
    }
}
