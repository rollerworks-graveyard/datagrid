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
use Symfony\Component\PropertyAccess\Exception\ExceptionInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author FSi sp. z o.o. <info@fsi.pl>
 */
class PropertyAccessorMapper implements DataMapperInterface
{
    /**
     * {@inheritdoc}
     */
    public function getData($field, $object)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        try {
            $data = $accessor->getValue($object, $field);
        } catch (ExceptionInterface $e) {
            throw new DataMappingException($e->getMessage(), 0, $e);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function setData($field, $object, $value)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        try {
            $accessor->setValue($object, $field, $value);
        } catch (ExceptionInterface $e) {
            throw new DataMappingException($e->getMessage(), 0, $e);
        }
    }
}
