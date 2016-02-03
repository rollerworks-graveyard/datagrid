<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid;

final class DatagridEvents
{
    /**
     * The BUILD_VIEW event is dispatched after a new DatagridView object is created
     * in the Datagrid::createView() method.
     *
     * It can be used to update the DatagridView with additional information.
     * Changing columns or rows is not possible.
     *
     * The event listener method receives a {@link Rollerworks\Component\Datagrid\DatagridViewEvent} instance.
     */
    const BUILD_VIEW = 'rollerworks_datagrid.build_view';
}
