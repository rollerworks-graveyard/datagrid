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

use Rollerworks\Component\Datagrid\Exception\TransformationFailedException;

/**
 * Transforms between a normalized format and a localized money string.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author Florian Eckerstorfer <florian@eckerstorfer.org>
 */
class MoneyToLocalizedStringTransformer extends NumberToLocalizedStringTransformer
{
    /**
     * @var int|null
     */
    private $divisor;

    /**
     * @var string
     */
    private $defaultCurrency;

    /**
     * @var null|string
     */
    private $currencyField;

    /**
     * @var null|string
     */
    private $inputField;

    /**
     * Constructor.
     *
     * @param int    $precision
     * @param bool   $grouping
     * @param int    $roundingMode
     * @param int    $divisor
     * @param string $defaultCurrency
     * @param string $inputField
     * @param string $currencyField
     */
    public function __construct($precision = null, $grouping = null, $roundingMode = null, $divisor = null, $defaultCurrency = 'EUR', $inputField = null, $currencyField = null)
    {
        if (null === $grouping) {
            $grouping = true;
        }

        if (null === $precision) {
            $precision = 2;
        }

        parent::__construct($precision, $grouping, $roundingMode);

        if (null === $divisor) {
            $divisor = 1;
        }

        $this->divisor = $divisor;

        $this->defaultCurrency = $defaultCurrency;

        $this->currencyField = $currencyField;
        $this->inputField = $inputField;
    }

    /**
     * Transforms a normalized format into a localized money string.
     *
     * @param array|string|int|float $value array, float or string
     *
     * @throws TransformationFailedException If the given value is not numeric or
     *                                       if the value can not be transformed.
     *
     * @return string Localized money string
     */
    public function transform($value)
    {
        if (null === $value) {
            return '';
        }

        if (is_array($value) && $this->currencyField) {
            if (!$this->defaultCurrency && $this->currencyField && !isset($value[$this->currencyField])) {
                throw new TransformationFailedException(sprintf('Field "%s" is "%s" does not exist provided value.', $this->currencyField));
            }

            if (!isset($value[$this->inputField])) {
                throw new TransformationFailedException(sprintf('Field "%s" is "%s" does not exist provided value.', $this->inputField));
            }

            $currency = isset($value[$this->currencyField]) ? $value[$this->currencyField] : $this->defaultCurrency;
            $amountValue = $value[$this->inputField];
        } elseif (is_array($value)) {
            if (!$this->defaultCurrency && !isset($value['currency'])) {
                throw new TransformationFailedException('The field "currency" should not be empty when no default is set.');
            }

            if (!isset($value['value'])) {
                throw new TransformationFailedException('The field "value" should not be empty.');
            }

            $currency = isset($value['currency']) ? $value['currency'] : $this->defaultCurrency;
            $amountValue = $value['value'];
        } else {
            $currency = $this->defaultCurrency;
            $amountValue = $value;
        }

        if (!is_numeric($amountValue)) {
            throw new TransformationFailedException('Expected a numeric value.');
        }

        $amountValue /= $this->divisor;
        $formatter = $this->getNumberFormatter(\NumberFormatter::CURRENCY);

        $value = $formatter->formatCurrency($amountValue, $currency);

        if (intl_is_failure($formatter->getErrorCode())) {
            throw new TransformationFailedException($formatter->getErrorMessage());
        }

        // Convert fixed spaces to normal ones
        $value = str_replace("\xc2\xa0", ' ', $value);

        return $value;
    }
}
