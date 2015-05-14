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
use Rollerworks\Component\Datagrid\Exception\InvalidArgumentException;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author FSi sp. z o.o. <info@fsi.pl>
 */
class ChainMapper implements DataMapperInterface
{
    /**
     * @var DataMapperInterface[]
     */
    protected $mappers = [];

    /**
     * Constructor.
     *
     * @param array $mappers
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $mappers)
    {
        if (!count($mappers)) {
            throw new InvalidArgumentException('There must be at least one mapper in the chain.');
        }

        foreach ($mappers as $mapper) {
            if (!$mapper instanceof DataMapperInterface) {
                throw new InvalidArgumentException('Mapper needs to implement Rollerworks\Component\Datagrid\DataMapper\DataMapperInterface');
            }
            $this->mappers[] = $mapper;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getData($field, $object)
    {
        $data = null;
        $dataFound = false;
        $lastMsg = null;

        foreach ($this->mappers as $mapper) {
            try {
                $data = $mapper->getData($field, $object);
            } catch (DataMappingException $e) {
                $data = null;
                $lastMsg = $e->getMessage();

                continue;
            }

            $dataFound = true;
            break;
        }

        if (!$dataFound) {
            if (!isset($lastMsg)) {
                $lastMsg = sprintf('Cant find any data that fits the "%s" field.', $field);
            }

            throw new DataMappingException($lastMsg);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function setData($field, $object, $value)
    {
        $dataChanged = false;
        $lastMsg = null;

        foreach ($this->mappers as $mapper) {
            try {
                $mapper->setData($field, $object, $value);
            } catch (DataMappingException $e) {
                $lastMsg = $e->getMessage();

                continue;
            }

            $dataChanged = true;
            break;
        }

        if (!$dataChanged) {
            if (!isset($lastMsg)) {
                $lastMsg = sprintf('Cant find any data that fits the "%s" field.', $field);
            }

            throw new DataMappingException($lastMsg);
        }

        return true;
    }
}
