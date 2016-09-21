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

namespace Rollerworks\Component\Datagrid\Tests\Fixtures;

class EventManagerMock
{
    protected $listeners;

    public function __construct($listeners)
    {
        $this->listeners = $listeners;
    }

    public function getListeners()
    {
        return [$this->listeners];
    }
}
