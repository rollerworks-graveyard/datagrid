<?php

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
use Rollerworks\Component\Datagrid\Exception\UnexpectedTypeException;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author FSi sp. z o.o. <info@fsi.pl>
 */
class ValueFormatTransformer implements DataTransformerInterface
{
    /**
     * @var string
     */
    private $emptyValue;

    /**
     * @var null|string
     */
    private $glue;

    /**
     * @var callback|null|string
     */
    private $format;

    /**
     * @param string|array         $emptyValue
     * @param null|string          $glue
     * @param null|string|callback $format
     * @param string[]             $mappingFields
     *
     * @throws UnexpectedTypeException
     */
    public function __construct($emptyValue = '', $glue = null, $format = null, array $mappingFields = [])
    {
        $this->validateEmptyValueOption($emptyValue, $mappingFields);

        $this->emptyValue = $emptyValue;
        $this->glue = $glue;
        $this->format = $format;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        $value = $this->populateValue($value, $this->emptyValue);

        if (is_array($value) && null !== $this->glue && null === $this->format) {
            $value = implode($this->glue, $value);
        }

        if (null !== $this->format) {
            $format = $this->format;
            $formatCallable = is_callable($format);

            if (is_array($value)) {
                if (null !== $this->glue) {
                    $formattedValues = [];

                    foreach ($value as $val) {
                        if ($formatCallable) {
                            $formattedValues[] = $format($val);
                        } else {
                            $formattedValues[] = sprintf($format, $val);
                        }
                    }

                    $value = implode($this->glue, $formattedValues);
                } else {
                    if ($formatCallable) {
                        $value = $format($value);
                    } else {
                        $value = vsprintf($format, $value);
                    }
                }
            } else {
                if ($formatCallable) {
                    $value = $format($value);
                } else {
                    $value = sprintf($format, $value);
                }
            }
        }

        if (is_array($value) && 1 === count($value)) {
            $value = reset($value);
        }

        if (!$this->glue && null === $this->format && is_array($value)) {
            throw new TransformationFailedException(sprintf('At least "glue" or "format" option must be set when the end value is an array.'));
        }

        return $value;
    }

    /**
     * @param string|array $emptyValue
     * @param array        $mappingFields
     *
     * @throws UnexpectedTypeException
     * @throws TransformationFailedException
     */
    private function validateEmptyValueOption($emptyValue, array $mappingFields)
    {
        if (is_string($emptyValue)) {
            return;
        }

        if (!is_array($emptyValue)) {
            throw new UnexpectedTypeException($emptyValue, ['string', 'array']);
        }

        foreach ($emptyValue as $field => $value) {
            if (!in_array($field, $mappingFields, true)) {
                throw new TransformationFailedException(sprintf('Empty-value of mapping field "%s" doesn\'t exists in field mapping.', $field));
            }

            if (!is_string($value)) {
                throw new TransformationFailedException(sprintf('Empty-value of mapping field "%s" must be a string value.', $field));
            }
        }
    }

    /**
     * @param mixed $value
     * @param mixed $emptyValue
     *
     * @return array|string
     */
    private function populateValue($value, $emptyValue)
    {
        // Don't use empty() here as 0 is a none-empty value
        if (is_string($emptyValue)) {
            if (!isset($value) || '' === $value) {
                return $emptyValue;
            }

            if (is_array($value)) {
                foreach ($value as $i => &$val) {
                    if (!isset($val) || '' === $val) {
                        $val = $emptyValue;
                    }
                }
            }

            return $value;
        }

        /*
         * If the value is a simple string and $emptyValue is array there is no way
         * to guess which value should be used.
         */
        if (!is_array($value)) {
            return (string) $value;
        }

        foreach ($value as $field => &$fieldValue) {
            if (!isset($val) || '' === $val) {
                $fieldValue = array_key_exists($field, $emptyValue) ? $emptyValue[$field] : '';
            }
        }

        return $value;
    }
}
