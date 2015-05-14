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
    const PRE_SET_DATA = 'rollerworks_datagrid.pre_set_data';

    const POST_SET_DATA = 'rollerworks_datagrid.post_set_data';

    const PRE_BIND_DATA = 'rollerworks_datagrid.pre_bind_data';

    const POST_BIND_DATA = 'rollerworks_datagrid.post_bind_data';

    const COLUMN_BIND_DATA = 'rollerworks_datagrid_column.pre_bind_data';

    const PRE_BUILD_VIEW = 'rollerworks_datagrid.pre_build_view';

    const POST_BUILD_VIEW = 'rollerworks_datagrid.post_build_view';
}
