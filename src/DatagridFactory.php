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

use Rollerworks\Component\Datagrid\Column\Column;
use Rollerworks\Component\Datagrid\Column\ColumnTypeInterface;
use Rollerworks\Component\Datagrid\Column\ColumnTypeRegistryInterface;
use Rollerworks\Component\Datagrid\Column\ResolvedColumnTypeFactoryInterface;
use Rollerworks\Component\Datagrid\Column\ResolvedColumnTypeInterface;
use Rollerworks\Component\Datagrid\DataMapper\DataMapperInterface;
use Rollerworks\Component\Datagrid\Exception\UnexpectedTypeException;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class DatagridFactory implements DatagridFactoryInterface
{
    /**
     * @var DataMapperInterface
     */
    private $dataMapper;

    /**
     * @var ColumnTypeRegistryInterface
     */
    private $typeRegistry;

    /**
     * @param ColumnTypeRegistryInterface $registry
     * @param DataMapperInterface         $dataMapper
     *
     * @internal param ResolvedColumnTypeFactoryInterface $resolvedTypeFactory
     */
    public function __construct(ColumnTypeRegistryInterface $registry, DataMapperInterface $dataMapper)
    {
        $this->dataMapper = $dataMapper;
        $this->typeRegistry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function createDatagrid($name)
    {
        return new Datagrid($name, $this->dataMapper);
    }

    /**
     * {@inheritdoc}
     */
    public function createDatagridBuilder($name, DataMapperInterface $dataMapper = null)
    {
        return new DatagridBuilder($this, $name, $dataMapper ?: $this->dataMapper);
    }

    /**
     * {@inheritdoc}
     */
    public function createColumn($name, $type, DatagridInterface $datagrid, array $options = [])
    {
        return $this->createColumnBuilder($name, $datagrid, $type, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getDataMapper()
    {
        return $this->dataMapper;
    }

    /**
     * Creates a new {@link Rollerworks\Component\Datagrid\Column\Column} instance.
     *
     * @param string            $name
     * @param DatagridInterface $datagrid
     * @param string            $type
     * @param array             $options
     *
     * @throws UnexpectedTypeException
     *
     * @return Column
     */
    private function createColumnBuilder($name, DatagridInterface $datagrid, $type = 'column', array $options = [])
    {
        if (!is_string($type)) {
            throw new UnexpectedTypeException($type, 'string');
        }

        $type = $this->typeRegistry->getType($type);

        $column = $type->createColumn($name, $datagrid, $options);

        // Explicitly call buildType() in order to be able to override either
        // createColumn() or buildType() in the resolved column type
        $type->buildType($column, $column->getOptions());

        return $column;
    }
}
