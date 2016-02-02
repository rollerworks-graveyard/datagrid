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
     * The PRE_BUILD_VIEW event is dispatched at the beginning of the Datagrid::setData() method.
     *
     * It can be used to:
     *  - Change date from a source, before set the data on the datagrid.
     *  - Provide data for the datagrid from an external source.
     *
     * The event listener method receives a Rollerworks\Component\Datagrid\DataGridEvent instance.
     */
    const PRE_BUILD_VIEW = 'rollerworks_datagrid.pre_build_view';

    /**
     * The PRE_SET_DATA event is dispatched at the beginning of the Datagrid::setData() method.
     *
     * It can be used to:
     *  - Change date from a source, before set the data on the datagrid.
     *  - Provide data for the datagrid from an external source.
     *
     * The event listener method receives a Rollerworks\Component\Datagrid\DataGridEvent instance.
     */
    const POST_BUILD_VIEW = 'rollerworks_datagrid.post_build_view';
}
