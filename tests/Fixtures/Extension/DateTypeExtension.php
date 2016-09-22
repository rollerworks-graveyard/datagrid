<?php

declare(strict_types = 1);

/*
 * Copyright (c) the Contributors as noted in the AUTHORS file.
 *
 * This file is part of the Park-Manager project.
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
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
