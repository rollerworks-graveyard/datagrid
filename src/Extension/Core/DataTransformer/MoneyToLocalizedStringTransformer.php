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
     * @var int
     */
    private $divisor = 1;

    /**
     * @var string
     */
    private $defaultCurrency;

    /**
     * Constructor.
     *
     * @param int    $precision
     * @param bool   $grouping
     * @param int    $roundingMode
     * @param int    $divisor
     * @param string $defaultCurrency
     */
    public function __construct(
        int $precision = 2,
        bool $grouping = true,
        int $roundingMode = self::ROUND_HALF_UP,
        int $divisor = 1,
        string $defaultCurrency = 'EUR'
    ) {
        parent::__construct($precision, $grouping, $roundingMode);

        $this->divisor = $divisor;
        $this->defaultCurrency = $defaultCurrency;
    }

    /**
     * Transforms a normalized format into a localized money string.
     *
     * @param string|int|float|array $value
     *
     * @throws TransformationFailedException If the given value is not numeric or
     *                                       if the value can not be transformed
     *
     * @return string Localized money string
     */
    public function transform($value)
    {
        if (null === $value) {
            return '';
        }

        if (is_array($value)) {
            return $this->doTransformation($value['currency'] ?: $this->defaultCurrency, (string) $value['amount']);
        }

        // Convert fixed spaces to normal ones.
        $value = str_replace("\xc2\xa0", ' ', (string) $value);

        if (false !== strpos($value, ' ')) {
            return $this->transformStringWithCurrencyToLocalized($value);
        }

        return $this->doTransformation($this->defaultCurrency, $value);
    }

    private function transformStringWithCurrencyToLocalized(string $value)
    {
        list($currency, $amount)=explode(' ', $value);

        if (mb_strlen($currency) <> 3) {
            throw new TransformationFailedException(
                sprintf(
                    'Currency "%s" extracted from "%s" is not accepted. Only three character format is supported.',
                    $currency,
                    $value
                )
            );
        }

        return $this->doTransformation($currency, $amount);
    }

    private function doTransformation(string $currency, string $amount)
    {
        if (!is_numeric($amount)) {
            throw new TransformationFailedException('Expected a numeric value.');
        }

        $amount /= $this->divisor;
        $formatter = $this->getNumberFormatter(\NumberFormatter::CURRENCY);

        $value = $formatter->formatCurrency($amount, $currency);

        if (intl_is_failure($formatter->getErrorCode())) {
            throw new TransformationFailedException($formatter->getErrorMessage());
        }

        // Convert fixed spaces to normal ones
        return str_replace("\xc2\xa0", ' ', $value);
    }
}
