<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Tests\Extension\Core\ColumnType;

use Rollerworks\Component\Datagrid\Extension\Core\ColumnType\NumberType;
use Symfony\Component\Intl\Util\IntlTestHelper;

class NumberTypeTest extends BaseTypeTest
{
    protected function setUp()
    {
        parent::setUp();

        // we test against "de_DE", so we need the full implementation
        IntlTestHelper::requireFullIntl($this);

        \Locale::setDefault('de_DE');
    }

    protected function getTestedType()
    {
        return NumberType::class;
    }

    public function testDefaultFormatting()
    {
        $this->assertCellValueEquals('12345,679', '12345.67890');
    }

    public function testDefaultFormattingWithGrouping()
    {
        $this->assertCellValueEquals('12.345,679', '12345.67890', ['grouping' => true]);
    }

    public function testDefaultFormattingWithPrecision()
    {
        $this->assertCellValueEquals('12345,68', '12345.67890', ['precision' => 2]);
    }

    public function testDefaultFormattingWithRounding()
    {
        $this->assertCellValueEquals(
            '12346',
            '12345.54321',
            ['precision' => 0, 'rounding_mode' => \NumberFormatter::ROUND_UP]
        );
    }
}
