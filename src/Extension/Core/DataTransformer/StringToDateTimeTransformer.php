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
 * Transforms between a date string and a DateTime object.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author Florian Eckerstorfer <florian@eckerstorfer.org>
 */
class StringToDateTimeTransformer extends BaseDateTimeTransformer
{
    /**
     * Format used for parsing strings.
     *
     * Different than the {@link $generateFormat} because formats for parsing
     * support additional characters in PHP that are not supported for
     * generating strings.
     *
     * @var string
     */
    private $parseFormat;

    /**
     * Transforms a \DateTime instance to a string.
     *
     * @see \DateTime::format() for supported formats
     *
     * @param string $inputTimezone  The name of the input timezone
     * @param string $outputTimezone The name of the output timezone
     * @param string $format         The date format
     *
     * @throws UnexpectedTypeException if a timezone is not a string
     */
    public function __construct($inputTimezone = null, $outputTimezone = null, $format = 'Y-m-d H:i:s')
    {
        parent::__construct($inputTimezone, $outputTimezone);

        $this->parseFormat = $format;
    }

    /**
     * Transforms a date string in the configured timezone into a DateTime object.
     *
     * @param string $value A value as produced by PHP's date() function
     *
     * @throws TransformationFailedException If the given value is not a string,
     *                                       if the date could not be parsed or
     *                                       if the input timezone is not supported
     *
     * @return \DateTime|null An instance of \DateTime
     */
    public function transform($value)
    {
        if (empty($value)) {
            return null;
        }

        if (!is_string($value)) {
            throw new TransformationFailedException('Expected a string.');
        }

        try {
            $outputTz = new \DateTimeZone($this->outputTimezone);
            $dateTime = \DateTime::createFromFormat($this->parseFormat, $value, $outputTz);

            $lastErrors = \DateTime::getLastErrors();

            if (0 < $lastErrors['warning_count'] || 0 < $lastErrors['error_count']) {
                throw new TransformationFailedException(
                    implode(', ', array_merge(
                        array_values($lastErrors['warnings']),
                        array_values($lastErrors['errors'])
                    ))
                );
            }

            if ($this->inputTimezone !== $this->outputTimezone) {
                $dateTime->setTimezone(new \DateTimeZone($this->inputTimezone));
            }
        } catch (TransformationFailedException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }

        return $dateTime;
    }
}
