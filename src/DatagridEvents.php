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
     * The PRE_SET_DATA event is dispatched at the beginning of the Datagrid::setData() method.
     *
     * It can be used to:
     *  - Change date from a source, before set the data on the datagrid.
     *  - Provide data for the datagrid from an external source.
     *
     * The event listener method receives a Rollerworks\Component\Datagrid\DataGridEvent instance.
     *
     * @Event
     */
    const PRE_SET_DATA = 'rollerworks_datagrid.pre_set_data';

    /**
     * The POST_SET_DATA event is dispatched after the the Datagrid::setData() method
     * once the data is set and the DataRowset is created.
     *
     * It can be used to fetch data after it's set.
     *
     * The event listener method receives a Rollerworks\Component\Datagrid\DataGridEvent instance.
     *
     * @Event
     */
    const POST_SET_DATA = 'rollerworks_datagrid.post_set_data';

    /**
     * The PRE_BIND_DATA event is dispatched at the beginning of the Datagrid::bindData() method.
     *
     * It can be used to:
     *  - Change data from the request, before submitting the data to the datagrid.
     *
     * The event listener method receives a Rollerworks\Component\Datagrid\DataGridEvent instance.
     *
     * @Event
     */
    const PRE_BIND_DATA = 'rollerworks_datagrid.pre_bind_data';

    /**
     * The POST_BIND_DATA event is dispatched after the Datagrid::bindData() method
     * once the data is updated and the values are filtered.
     *
     * It can be used to fetch (updated) data after it's set.
     *
     * The event listener method receives a Rollerworks\Component\Datagrid\DataGridEvent instance.
     *
     * @Event
     */
    const POST_BIND_DATA = 'rollerworks_datagrid.post_bind_data';

    /**
     * The COLUMN_BIND_DATA event is dispatched for each column on the datagrid
     * after the Datagrid::bindData() method.
     *
     * It can be used to:
     *  - Update the data on a single column
     *  - Handle request data for an editable form that needs the request data.
     *
     * The event listener method receives a Rollerworks\Component\Datagrid\DatagridColumnEvent instance.
     *
     * @Event
     */
    const COLUMN_BIND_DATA = 'rollerworks_datagrid_column.pre_bind_data';

    /**
     * The PRE_BUILD_VIEW event is dispatched at the beginning of the Datagrid::setData() method.
     *
     * It can be used to:
     *  - Change date from a source, before set the data on the datagrid.
     *  - Provide data for the datagrid from an external source.
     *
     * The event listener method receives a Rollerworks\Component\Datagrid\DataGridEvent instance.
     *
     * @Event
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
     *
     * @Event
     */
    const POST_BUILD_VIEW = 'rollerworks_datagrid.post_build_view';
}
