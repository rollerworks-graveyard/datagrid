<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Tests\Extension\Core\DataTransformer;

use Rollerworks\Component\Datagrid\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer;
use Symfony\Component\Intl\Util\IntlTestHelper;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class NumberToLocalizedStringTransformerTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        // Since we test against "de_AT", we need the full implementation
        IntlTestHelper::requireFullIntl($this);

        \Locale::setDefault('de_AT');
    }

    public function provideTransformations()
    {
        return [
            [null, '', 'de_AT'],
            [1, '1', 'de_AT'],
            [1.5, '1,5', 'de_AT'],
            [1234.5, '1234,5', 'de_AT'],
            [12345.912, '12345,912', 'de_AT'],
            [1234.5, '1234,5', 'ru'],
            [1234.5, '1234,5', 'fi'],
        ];
    }

    /**
     * @dataProvider provideTransformations
     */
    public function testTransform($from, $to, $locale)
    {
        \Locale::setDefault($locale);

        $transformer = new NumberToLocalizedStringTransformer();

        $this->assertSame($to, $transformer->transform($from));
    }

    public function provideTransformationsWithGrouping()
    {
        return [
            [1234.5, '1 234,5', 'de_AT'],
            [12345.912, '12 345,912', 'de_AT'],
            [1234.5, '1 234,5', 'fr'],
            [1234.5, '1 234,5', 'ru'],
            [1234.5, '1 234,5', 'fi'],
        ];
    }

    /**
     * @dataProvider provideTransformationsWithGrouping
     */
    public function testTransformWithGrouping($from, $to, $locale)
    {
        \Locale::setDefault($locale);

        $transformer = new NumberToLocalizedStringTransformer(null, true);

        $this->assertSame($to, $transformer->transform($from));
    }

    public function testTransformWithPrecision()
    {
        $transformer = new NumberToLocalizedStringTransformer(2);

        $this->assertEquals('1234,50', $transformer->transform(1234.5));
        $this->assertEquals('678,92', $transformer->transform(678.916));
    }

    public function transformWithRoundingProvider()
    {
        return [
            // towards positive infinity (1.6 -> 2, -1.6 -> -1)
            [0, 1234.5, '1235', NumberToLocalizedStringTransformer::ROUND_CEILING],
            [0, 1234.4, '1235', NumberToLocalizedStringTransformer::ROUND_CEILING],
            [0, -1234.5, '-1234', NumberToLocalizedStringTransformer::ROUND_CEILING],
            [0, -1234.4, '-1234', NumberToLocalizedStringTransformer::ROUND_CEILING],
            [1, 123.45, '123,5', NumberToLocalizedStringTransformer::ROUND_CEILING],
            [1, 123.44, '123,5', NumberToLocalizedStringTransformer::ROUND_CEILING],
            [1, -123.45, '-123,4', NumberToLocalizedStringTransformer::ROUND_CEILING],
            [1, -123.44, '-123,4', NumberToLocalizedStringTransformer::ROUND_CEILING],
            // towards negative infinity (1.6 -> 1, -1.6 -> -2)
            [0, 1234.5, '1234', NumberToLocalizedStringTransformer::ROUND_FLOOR],
            [0, 1234.4, '1234', NumberToLocalizedStringTransformer::ROUND_FLOOR],
            [0, -1234.5, '-1235', NumberToLocalizedStringTransformer::ROUND_FLOOR],
            [0, -1234.4, '-1235', NumberToLocalizedStringTransformer::ROUND_FLOOR],
            [1, 123.45, '123,4', NumberToLocalizedStringTransformer::ROUND_FLOOR],
            [1, 123.44, '123,4', NumberToLocalizedStringTransformer::ROUND_FLOOR],
            [1, -123.45, '-123,5', NumberToLocalizedStringTransformer::ROUND_FLOOR],
            [1, -123.44, '-123,5', NumberToLocalizedStringTransformer::ROUND_FLOOR],
            // away from zero (1.6 -> 2, -1.6 -> 2)
            [0, 1234.5, '1235', NumberToLocalizedStringTransformer::ROUND_UP],
            [0, 1234.4, '1235', NumberToLocalizedStringTransformer::ROUND_UP],
            [0, -1234.5, '-1235', NumberToLocalizedStringTransformer::ROUND_UP],
            [0, -1234.4, '-1235', NumberToLocalizedStringTransformer::ROUND_UP],
            [1, 123.45, '123,5', NumberToLocalizedStringTransformer::ROUND_UP],
            [1, 123.44, '123,5', NumberToLocalizedStringTransformer::ROUND_UP],
            [1, -123.45, '-123,5', NumberToLocalizedStringTransformer::ROUND_UP],
            [1, -123.44, '-123,5', NumberToLocalizedStringTransformer::ROUND_UP],
            // towards zero (1.6 -> 1, -1.6 -> -1)
            [0, 1234.5, '1234', NumberToLocalizedStringTransformer::ROUND_DOWN],
            [0, 1234.4, '1234', NumberToLocalizedStringTransformer::ROUND_DOWN],
            [0, -1234.5, '-1234', NumberToLocalizedStringTransformer::ROUND_DOWN],
            [0, -1234.4, '-1234', NumberToLocalizedStringTransformer::ROUND_DOWN],
            [1, 123.45, '123,4', NumberToLocalizedStringTransformer::ROUND_DOWN],
            [1, 123.44, '123,4', NumberToLocalizedStringTransformer::ROUND_DOWN],
            [1, -123.45, '-123,4', NumberToLocalizedStringTransformer::ROUND_DOWN],
            [1, -123.44, '-123,4', NumberToLocalizedStringTransformer::ROUND_DOWN],
            // round halves (.5) to the next even number
            [0, 1234.6, '1235', NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [0, 1234.5, '1234', NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [0, 1234.4, '1234', NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [0, 1233.5, '1234', NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [0, 1232.5, '1232', NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [0, -1234.6, '-1235', NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [0, -1234.5, '-1234', NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [0, -1234.4, '-1234', NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [0, -1233.5, '-1234', NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [0, -1232.5, '-1232', NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [1, 123.46, '123,5', NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [1, 123.45, '123,4', NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [1, 123.44, '123,4', NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [1, 123.35, '123,4', NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [1, 123.25, '123,2', NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [1, -123.46, '-123,5', NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [1, -123.45, '-123,4', NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [1, -123.44, '-123,4', NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [1, -123.35, '-123,4', NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [1, -123.25, '-123,2', NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            // round halves (.5) away from zero
            [0, 1234.6, '1235', NumberToLocalizedStringTransformer::ROUND_HALF_UP],
            [0, 1234.5, '1235', NumberToLocalizedStringTransformer::ROUND_HALF_UP],
            [0, 1234.4, '1234', NumberToLocalizedStringTransformer::ROUND_HALF_UP],
            [0, -1234.6, '-1235', NumberToLocalizedStringTransformer::ROUND_HALF_UP],
            [0, -1234.5, '-1235', NumberToLocalizedStringTransformer::ROUND_HALF_UP],
            [0, -1234.4, '-1234', NumberToLocalizedStringTransformer::ROUND_HALF_UP],
            [1, 123.46, '123,5', NumberToLocalizedStringTransformer::ROUND_HALF_UP],
            [1, 123.45, '123,5', NumberToLocalizedStringTransformer::ROUND_HALF_UP],
            [1, 123.44, '123,4', NumberToLocalizedStringTransformer::ROUND_HALF_UP],
            [1, -123.46, '-123,5', NumberToLocalizedStringTransformer::ROUND_HALF_UP],
            [1, -123.45, '-123,5', NumberToLocalizedStringTransformer::ROUND_HALF_UP],
            [1, -123.44, '-123,4', NumberToLocalizedStringTransformer::ROUND_HALF_UP],
            // round halves (.5) towards zero
            [0, 1234.6, '1235', NumberToLocalizedStringTransformer::ROUND_HALF_DOWN],
            [0, 1234.5, '1234', NumberToLocalizedStringTransformer::ROUND_HALF_DOWN],
            [0, 1234.4, '1234', NumberToLocalizedStringTransformer::ROUND_HALF_DOWN],
            [0, -1234.6, '-1235', NumberToLocalizedStringTransformer::ROUND_HALF_DOWN],
            [0, -1234.5, '-1234', NumberToLocalizedStringTransformer::ROUND_HALF_DOWN],
            [0, -1234.4, '-1234', NumberToLocalizedStringTransformer::ROUND_HALF_DOWN],
            [1, 123.46, '123,5', NumberToLocalizedStringTransformer::ROUND_HALF_DOWN],
            [1, 123.45, '123,4', NumberToLocalizedStringTransformer::ROUND_HALF_DOWN],
            [1, 123.44, '123,4', NumberToLocalizedStringTransformer::ROUND_HALF_DOWN],
            [1, -123.46, '-123,5', NumberToLocalizedStringTransformer::ROUND_HALF_DOWN],
            [1, -123.45, '-123,4', NumberToLocalizedStringTransformer::ROUND_HALF_DOWN],
            [1, -123.44, '-123,4', NumberToLocalizedStringTransformer::ROUND_HALF_DOWN],
        ];
    }

    /**
     * @dataProvider transformWithRoundingProvider
     */
    public function testTransformWithRounding($precision, $input, $output, $roundingMode)
    {
        $transformer = new NumberToLocalizedStringTransformer($precision, null, $roundingMode);

        $this->assertEquals($output, $transformer->transform($input));
    }

    public function testTransformDoesNotRoundIfNoPrecision()
    {
        $transformer = new NumberToLocalizedStringTransformer(null, null, NumberToLocalizedStringTransformer::ROUND_DOWN);

        $this->assertEquals('1234,547', $transformer->transform(1234.547));
    }

    public function reverseTransformWithRoundingProvider()
    {
        return [
            // towards positive infinity (1.6 -> 2, -1.6 -> -1)
            [0, '1234,5', 1235, NumberToLocalizedStringTransformer::ROUND_CEILING],
            [0, '1234,4', 1235, NumberToLocalizedStringTransformer::ROUND_CEILING],
            [0, '-1234,5', -1234, NumberToLocalizedStringTransformer::ROUND_CEILING],
            [0, '-1234,4', -1234, NumberToLocalizedStringTransformer::ROUND_CEILING],
            [1, '123,45', 123.5, NumberToLocalizedStringTransformer::ROUND_CEILING],
            [1, '123,44', 123.5, NumberToLocalizedStringTransformer::ROUND_CEILING],
            [1, '-123,45', -123.4, NumberToLocalizedStringTransformer::ROUND_CEILING],
            [1, '-123,44', -123.4, NumberToLocalizedStringTransformer::ROUND_CEILING],
            // towards negative infinity (1.6 -> 1, -1.6 -> -2)
            [0, '1234,5', 1234, NumberToLocalizedStringTransformer::ROUND_FLOOR],
            [0, '1234,4', 1234, NumberToLocalizedStringTransformer::ROUND_FLOOR],
            [0, '-1234,5', -1235, NumberToLocalizedStringTransformer::ROUND_FLOOR],
            [0, '-1234,4', -1235, NumberToLocalizedStringTransformer::ROUND_FLOOR],
            [1, '123,45', 123.4, NumberToLocalizedStringTransformer::ROUND_FLOOR],
            [1, '123,44', 123.4, NumberToLocalizedStringTransformer::ROUND_FLOOR],
            [1, '-123,45', -123.5, NumberToLocalizedStringTransformer::ROUND_FLOOR],
            [1, '-123,44', -123.5, NumberToLocalizedStringTransformer::ROUND_FLOOR],
            // away from zero (1.6 -> 2, -1.6 -> 2)
            [0, '1234,5', 1235, NumberToLocalizedStringTransformer::ROUND_UP],
            [0, '1234,4', 1235, NumberToLocalizedStringTransformer::ROUND_UP],
            [0, '-1234,5', -1235, NumberToLocalizedStringTransformer::ROUND_UP],
            [0, '-1234,4', -1235, NumberToLocalizedStringTransformer::ROUND_UP],
            [1, '123,45', 123.5, NumberToLocalizedStringTransformer::ROUND_UP],
            [1, '123,44', 123.5, NumberToLocalizedStringTransformer::ROUND_UP],
            [1, '-123,45', -123.5, NumberToLocalizedStringTransformer::ROUND_UP],
            [1, '-123,44', -123.5, NumberToLocalizedStringTransformer::ROUND_UP],
            // towards zero (1.6 -> 1, -1.6 -> -1)
            [0, '1234,5', 1234, NumberToLocalizedStringTransformer::ROUND_DOWN],
            [0, '1234,4', 1234, NumberToLocalizedStringTransformer::ROUND_DOWN],
            [0, '-1234,5', -1234, NumberToLocalizedStringTransformer::ROUND_DOWN],
            [0, '-1234,4', -1234, NumberToLocalizedStringTransformer::ROUND_DOWN],
            [1, '123,45', 123.4, NumberToLocalizedStringTransformer::ROUND_DOWN],
            [1, '123,44', 123.4, NumberToLocalizedStringTransformer::ROUND_DOWN],
            [1, '-123,45', -123.4, NumberToLocalizedStringTransformer::ROUND_DOWN],
            [1, '-123,44', -123.4, NumberToLocalizedStringTransformer::ROUND_DOWN],
            // round halves (.5) to the next even number
            [0, '1234,6', 1235, NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [0, '1234,5', 1234, NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [0, '1234,4', 1234, NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [0, '1233,5', 1234, NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [0, '1232,5', 1232, NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [0, '-1234,6', -1235, NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [0, '-1234,5', -1234, NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [0, '-1234,4', -1234, NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [0, '-1233,5', -1234, NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [0, '-1232,5', -1232, NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [1, '123,46', 123.5, NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [1, '123,45', 123.4, NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [1, '123,44', 123.4, NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [1, '123,35', 123.4, NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [1, '123,25', 123.2, NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [1, '-123,46', -123.5, NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [1, '-123,45', -123.4, NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [1, '-123,44', -123.4, NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [1, '-123,35', -123.4, NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [1, '-123,25', -123.2, NumberToLocalizedStringTransformer::ROUND_HALF_EVEN],
            // round halves (.5) away from zero
            [0, '1234,6', 1235, NumberToLocalizedStringTransformer::ROUND_HALF_UP],
            [0, '1234,5', 1235, NumberToLocalizedStringTransformer::ROUND_HALF_UP],
            [0, '1234,4', 1234, NumberToLocalizedStringTransformer::ROUND_HALF_UP],
            [0, '-1234,6', -1235, NumberToLocalizedStringTransformer::ROUND_HALF_UP],
            [0, '-1234,5', -1235, NumberToLocalizedStringTransformer::ROUND_HALF_UP],
            [0, '-1234,4', -1234, NumberToLocalizedStringTransformer::ROUND_HALF_UP],
            [1, '123,46', 123.5, NumberToLocalizedStringTransformer::ROUND_HALF_UP],
            [1, '123,45', 123.5, NumberToLocalizedStringTransformer::ROUND_HALF_UP],
            [1, '123,44', 123.4, NumberToLocalizedStringTransformer::ROUND_HALF_UP],
            [1, '-123,46', -123.5, NumberToLocalizedStringTransformer::ROUND_HALF_UP],
            [1, '-123,45', -123.5, NumberToLocalizedStringTransformer::ROUND_HALF_UP],
            [1, '-123,44', -123.4, NumberToLocalizedStringTransformer::ROUND_HALF_UP],
            // round halves (.5) towards zero
            [0, '1234,6', 1235, NumberToLocalizedStringTransformer::ROUND_HALF_DOWN],
            [0, '1234,5', 1234, NumberToLocalizedStringTransformer::ROUND_HALF_DOWN],
            [0, '1234,4', 1234, NumberToLocalizedStringTransformer::ROUND_HALF_DOWN],
            [0, '-1234,6', -1235, NumberToLocalizedStringTransformer::ROUND_HALF_DOWN],
            [0, '-1234,5', -1234, NumberToLocalizedStringTransformer::ROUND_HALF_DOWN],
            [0, '-1234,4', -1234, NumberToLocalizedStringTransformer::ROUND_HALF_DOWN],
            [1, '123,46', 123.5, NumberToLocalizedStringTransformer::ROUND_HALF_DOWN],
            [1, '123,45', 123.4, NumberToLocalizedStringTransformer::ROUND_HALF_DOWN],
            [1, '123,44', 123.4, NumberToLocalizedStringTransformer::ROUND_HALF_DOWN],
            [1, '-123,46', -123.5, NumberToLocalizedStringTransformer::ROUND_HALF_DOWN],
            [1, '-123,45', -123.4, NumberToLocalizedStringTransformer::ROUND_HALF_DOWN],
            [1, '-123,44', -123.4, NumberToLocalizedStringTransformer::ROUND_HALF_DOWN],
        ];
    }
}
