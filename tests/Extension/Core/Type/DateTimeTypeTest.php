<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Tests\Extension\Core\Type;

use Rollerworks\Component\Datagrid\Extension\Core\Type\DateTimeType;
use Symfony\Component\Intl\Util\IntlTestHelper;

class DateTimeTypeTest extends BaseTypeTest
{
    protected function getTestedType()
    {
        return DateTimeType::class;
    }

    protected function setUp()
    {
        // we test against different locales, so we need the full
        // implementation
        IntlTestHelper::requireFullIntl($this);

        parent::setUp();
    }

    // TODO complete tests, transformers work so tests should be kept small
}
