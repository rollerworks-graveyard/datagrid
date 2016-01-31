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
 */
final class EmptyValueTransformer implements DataTransformerInterface
{
    /**
     * @var string|array
     */
    private $emptyValue;

    /**
     * @param string|array $emptyValue
     *
     * @throws UnexpectedTypeException
     */
    public function __construct($emptyValue = '')
    {
        if (!is_string($emptyValue) && !is_array($emptyValue)) {
            throw new UnexpectedTypeException($emptyValue, ['string', 'array']);
        }

        $this->emptyValue = $emptyValue;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (is_array($this->emptyValue) && !is_array($value)) {
            throw new TransformationFailedException('Empty value is an array, but provided value is not an array.');
        }

        // Note. empty is not used here as 0 is a none-empty value.
        // Only false, null and an empty string is considered empty.

        if (!is_array($value)) {
            return '' === (string) $value ? $this->emptyValue : $value;
        }

        $emptyIsString = is_string($this->emptyValue);

        foreach ($value as $field => &$val) {
            if (isset($val) && '' !== $val) {
                continue;
            }

            if ($emptyIsString) {
                $val = $this->emptyValue;
            } elseif (!array_key_exists($field, $this->emptyValue)) {
                throw new TransformationFailedException(
                    sprintf('No empty value-replacement for field "%s" set.', $field)
                );
            } else {
                $emptyValue = $this->emptyValue[$field];

                if (!is_string($emptyValue)) {
                    throw new TransformationFailedException(
                        sprintf('Empty value of field "%s" must be a string value.', $field)
                    );
                }

                $val = $emptyValue;
            }

        }

        return $value;
    }
}
