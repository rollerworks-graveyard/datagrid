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

namespace Rollerworks\Component\Datagrid\Tests\Fixtures\Extension;

use Rollerworks\Component\Datagrid\Column\AbstractTypeExtension;
use Rollerworks\Component\Datagrid\Extension\Core\Type\DateTimeType;

final class DateTypeExtension extends AbstractTypeExtension
{
    public function getExtendedType(): string
    {
        return DateTimeType::class;
    }
}
