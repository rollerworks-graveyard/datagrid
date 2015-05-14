<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\DataMapper;

use Rollerworks\Component\Datagrid\Exception\DataMappingException;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author FSi sp. z o.o. <info@fsi.pl>
 */
interface DataMapperInterface
{
    /**
     * Get data from object for specified column type.
     *
     * @param string       $field
     * @param object|array $object
     *
     * @throws DataMappingException when the mapper fails to get the data of the column
     *
     * @return mixed
     */
    public function getData($field, $object);

    /**
     * Sets data to object for specified column type.
     *
     * @param string       $field
     * @param object|array $object
     * @param mixed        $value
     *
     * @throws DataMappingException when the mapper fails to set the data on the column
     */
    public function setData($field, $object, $value);
}
