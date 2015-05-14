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

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author FSi sp. z o.o. <info@fsi.pl>
 */
interface DatagridRowViewInterface extends \Iterator, \Countable, \ArrayAccess
{
    /**
     * Return the row-index as defined in the Datagrid view.
     *
     * @return int
     */
    public function getIndex();

    /**
     * Get the source of the row.
     *
     * @return object
     */
    public function getSource();
}
