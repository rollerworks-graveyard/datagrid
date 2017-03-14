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

namespace Rollerworks\Component\Datagrid\Tests\Extension\Core\DataTransformer;

use PHPUnit\Framework\TestCase;
use Rollerworks\Component\Datagrid\Extension\Core\DataTransformer\IntegerToLocalizedStringTransformer;
use Symfony\Component\Intl\Util\IntlTestHelper;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class IntegerToLocalizedStringTransformerTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        // Since we test against "de_AT", we need the full implementation
        IntlTestHelper::requireFullIntl($this, '58.1');

        \Locale::setDefault('de_AT');
    }

    public function transformWithRoundingProvider()
    {
        return [
            // towards positive infinity (1.6 -> 2, -1.6 -> -1)
            [1234.5, '1235', IntegerToLocalizedStringTransformer::ROUND_CEILING],
            [1234.4, '1235', IntegerToLocalizedStringTransformer::ROUND_CEILING],
            [-1234.5, '-1234', IntegerToLocalizedStringTransformer::ROUND_CEILING],
            [-1234.4, '-1234', IntegerToLocalizedStringTransformer::ROUND_CEILING],
            // towards negative infinity (1.6 -> 1, -1.6 -> -2)
            [1234.5, '1234', IntegerToLocalizedStringTransformer::ROUND_FLOOR],
            [1234.4, '1234', IntegerToLocalizedStringTransformer::ROUND_FLOOR],
            [-1234.5, '-1235', IntegerToLocalizedStringTransformer::ROUND_FLOOR],
            [-1234.4, '-1235', IntegerToLocalizedStringTransformer::ROUND_FLOOR],
            // away from zero (1.6 -> 2, -1.6 -> 2)
            [1234.5, '1235', IntegerToLocalizedStringTransformer::ROUND_UP],
            [1234.4, '1235', IntegerToLocalizedStringTransformer::ROUND_UP],
            [-1234.5, '-1235', IntegerToLocalizedStringTransformer::ROUND_UP],
            [-1234.4, '-1235', IntegerToLocalizedStringTransformer::ROUND_UP],
            // towards zero (1.6 -> 1, -1.6 -> -1)
            [1234.5, '1234', IntegerToLocalizedStringTransformer::ROUND_DOWN],
            [1234.4, '1234', IntegerToLocalizedStringTransformer::ROUND_DOWN],
            [-1234.5, '-1234', IntegerToLocalizedStringTransformer::ROUND_DOWN],
            [-1234.4, '-1234', IntegerToLocalizedStringTransformer::ROUND_DOWN],
            // round halves (.5) to the next even number
            [1234.6, '1235', IntegerToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [1234.5, '1234', IntegerToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [1234.4, '1234', IntegerToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [1233.5, '1234', IntegerToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [1232.5, '1232', IntegerToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [-1234.6, '-1235', IntegerToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [-1234.5, '-1234', IntegerToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [-1234.4, '-1234', IntegerToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [-1233.5, '-1234', IntegerToLocalizedStringTransformer::ROUND_HALF_EVEN],
            [-1232.5, '-1232', IntegerToLocalizedStringTransformer::ROUND_HALF_EVEN],
            // round halves (.5) away from zero
            [1234.6, '1235', IntegerToLocalizedStringTransformer::ROUND_HALF_UP],
            [1234.5, '1235', IntegerToLocalizedStringTransformer::ROUND_HALF_UP],
            [1234.4, '1234', IntegerToLocalizedStringTransformer::ROUND_HALF_UP],
            [-1234.6, '-1235', IntegerToLocalizedStringTransformer::ROUND_HALF_UP],
            [-1234.5, '-1235', IntegerToLocalizedStringTransformer::ROUND_HALF_UP],
            [-1234.4, '-1234', IntegerToLocalizedStringTransformer::ROUND_HALF_UP],
            // round halves (.5) towards zero
            [1234.6, '1235', IntegerToLocalizedStringTransformer::ROUND_HALF_DOWN],
            [1234.5, '1234', IntegerToLocalizedStringTransformer::ROUND_HALF_DOWN],
            [1234.4, '1234', IntegerToLocalizedStringTransformer::ROUND_HALF_DOWN],
            [-1234.6, '-1235', IntegerToLocalizedStringTransformer::ROUND_HALF_DOWN],
            [-1234.5, '-1234', IntegerToLocalizedStringTransformer::ROUND_HALF_DOWN],
            [-1234.4, '-1234', IntegerToLocalizedStringTransformer::ROUND_HALF_DOWN],
        ];
    }

    /**
     * @dataProvider transformWithRoundingProvider
     */
    public function testTransformWithRounding($input, $output, $roundingMode)
    {
        $transformer = new IntegerToLocalizedStringTransformer(null, $roundingMode);

        $this->assertEquals($output, $transformer->transform($input));
    }

    public function reverseTransformWithRoundingProvider()
    {
        return [
            // towards positive infinity (1.6 -> 2, -1.6 -> -1)
            ['1234,5', 1235, IntegerToLocalizedStringTransformer::ROUND_CEILING],
            ['1234,4', 1235, IntegerToLocalizedStringTransformer::ROUND_CEILING],
            ['-1234,5', -1234, IntegerToLocalizedStringTransformer::ROUND_CEILING],
            ['-1234,4', -1234, IntegerToLocalizedStringTransformer::ROUND_CEILING],
            // towards negative infinity (1.6 -> 1, -1.6 -> -2)
            ['1234,5', 1234, IntegerToLocalizedStringTransformer::ROUND_FLOOR],
            ['1234,4', 1234, IntegerToLocalizedStringTransformer::ROUND_FLOOR],
            ['-1234,5', -1235, IntegerToLocalizedStringTransformer::ROUND_FLOOR],
            ['-1234,4', -1235, IntegerToLocalizedStringTransformer::ROUND_FLOOR],
            // away from zero (1.6 -> 2, -1.6 -> 2)
            ['1234,5', 1235, IntegerToLocalizedStringTransformer::ROUND_UP],
            ['1234,4', 1235, IntegerToLocalizedStringTransformer::ROUND_UP],
            ['-1234,5', -1235, IntegerToLocalizedStringTransformer::ROUND_UP],
            ['-1234,4', -1235, IntegerToLocalizedStringTransformer::ROUND_UP],
            // towards zero (1.6 -> 1, -1.6 -> -1)
            ['1234,5', 1234, IntegerToLocalizedStringTransformer::ROUND_DOWN],
            ['1234,4', 1234, IntegerToLocalizedStringTransformer::ROUND_DOWN],
            ['-1234,5', -1234, IntegerToLocalizedStringTransformer::ROUND_DOWN],
            ['-1234,4', -1234, IntegerToLocalizedStringTransformer::ROUND_DOWN],
            // round halves (.5) to the next even number
            ['1234,6', 1235, IntegerToLocalizedStringTransformer::ROUND_HALF_EVEN],
            ['1234,5', 1234, IntegerToLocalizedStringTransformer::ROUND_HALF_EVEN],
            ['1234,4', 1234, IntegerToLocalizedStringTransformer::ROUND_HALF_EVEN],
            ['1233,5', 1234, IntegerToLocalizedStringTransformer::ROUND_HALF_EVEN],
            ['1232,5', 1232, IntegerToLocalizedStringTransformer::ROUND_HALF_EVEN],
            ['-1234,6', -1235, IntegerToLocalizedStringTransformer::ROUND_HALF_EVEN],
            ['-1234,5', -1234, IntegerToLocalizedStringTransformer::ROUND_HALF_EVEN],
            ['-1234,4', -1234, IntegerToLocalizedStringTransformer::ROUND_HALF_EVEN],
            ['-1233,5', -1234, IntegerToLocalizedStringTransformer::ROUND_HALF_EVEN],
            ['-1232,5', -1232, IntegerToLocalizedStringTransformer::ROUND_HALF_EVEN],
            // round halves (.5) away from zero
            ['1234,6', 1235, IntegerToLocalizedStringTransformer::ROUND_HALF_UP],
            ['1234,5', 1235, IntegerToLocalizedStringTransformer::ROUND_HALF_UP],
            ['1234,4', 1234, IntegerToLocalizedStringTransformer::ROUND_HALF_UP],
            ['-1234,6', -1235, IntegerToLocalizedStringTransformer::ROUND_HALF_UP],
            ['-1234,5', -1235, IntegerToLocalizedStringTransformer::ROUND_HALF_UP],
            ['-1234,4', -1234, IntegerToLocalizedStringTransformer::ROUND_HALF_UP],
            // round halves (.5) towards zero
            ['1234,6', 1235, IntegerToLocalizedStringTransformer::ROUND_HALF_DOWN],
            ['1234,5', 1234, IntegerToLocalizedStringTransformer::ROUND_HALF_DOWN],
            ['1234,4', 1234, IntegerToLocalizedStringTransformer::ROUND_HALF_DOWN],
            ['-1234,6', -1235, IntegerToLocalizedStringTransformer::ROUND_HALF_DOWN],
            ['-1234,5', -1234, IntegerToLocalizedStringTransformer::ROUND_HALF_DOWN],
            ['-1234,4', -1234, IntegerToLocalizedStringTransformer::ROUND_HALF_DOWN],
        ];
    }
}
