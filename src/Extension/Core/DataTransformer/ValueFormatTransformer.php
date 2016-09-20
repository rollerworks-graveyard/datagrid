<?php declare(strict_types=1);

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Extension\Core\DataTransformer;

use Rollerworks\Component\Datagrid\DataTransformerInterface;
use Rollerworks\Component\Datagrid\Exception\TransformationFailedException;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class ValueFormatTransformer implements DataTransformerInterface
{
    /**
     * @var null|string
     */
    private $glue;

    /**
     * @var callback|null|string
     */
    private $format;

    /**
     * @param null|string          $glue
     * @param null|string|callback $format
     */
    public function __construct($glue = null, $format = null)
    {
        $this->glue = $glue;
        $this->format = $format;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (is_array($value) && null === $this->glue && null === $this->format) {
            throw new TransformationFailedException(
                sprintf('At least "glue" or "format" option must be set when the end value is an array.')
            );
        }

        if (null === $this->format) {
            // Glue is required to be set at this point, so no need for further checking.
            if (is_array($value)) {
                return implode($this->glue, $value);
            }

            return (string) $value;
        }

        if (!is_array($value)) {
            $format = $this->format;

            if (is_callable($format)) {
                return $format($value);
            }

            return sprintf($format, $value);
        }

        return $this->formatValue($value, $this->format, $this->glue);
    }

    private function formatValue($value, $format, $glue): string
    {
        $formatCallable = is_callable($format);

        if (null === $glue) {
            if ($formatCallable) {
                return $format($value);
            }

            return vsprintf($format, $value);
        }

        $formattedValues = [];

        foreach ($value as $field => $val) {
            if ($formatCallable) {
                $formattedValues[] = $format($val, $field);
            } else {
                $formattedValues[] = sprintf($format, $val);
            }
        }

        return implode($glue, $formattedValues);
    }
}
