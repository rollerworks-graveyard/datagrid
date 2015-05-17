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
use Rollerworks\Component\Datagrid\Column\ColumnTypeRegistryInterface;
use Rollerworks\Component\Datagrid\Column\ColumnTypeInterface;
use Rollerworks\Component\Datagrid\Column\ResolvedColumnTypeFactoryInterface;
use Rollerworks\Component\Datagrid\Column\ResolvedColumnTypeInterface;
use Rollerworks\Component\Datagrid\DataMapper\DataMapperInterface;
use Rollerworks\Component\Datagrid\Exception\DatagridException;
use Rollerworks\Component\Datagrid\Exception\UnexpectedTypeException;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author FSi sp. z o.o. <info@fsi.pl>
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
     * @var ResolvedColumnTypeFactoryInterface
     */
    private $resolvedTypeFactory;

    /**
     * @param ColumnTypeRegistryInterface            $registry
     * @param ResolvedColumnTypeFactoryInterface $resolvedTypeFactory
     * @param DataMapperInterface                $dataMapper
     */
    public function __construct(ColumnTypeRegistryInterface $registry, ResolvedColumnTypeFactoryInterface $resolvedTypeFactory, DataMapperInterface $dataMapper)
    {
        $this->dataMapper = $dataMapper;
        $this->typeRegistry = $registry;
        $this->resolvedTypeFactory = $resolvedTypeFactory;
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
        $column = $this->createColumnBuilder($name, $datagrid, $type, $options);

        return $column;
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
        if ($type instanceof ColumnTypeInterface) {
            $type = $this->resolveType($type);
        } elseif (is_string($type)) {
            $type = $this->typeRegistry->getType($type);
        } elseif (!$type instanceof ResolvedColumnTypeInterface) {
            throw new UnexpectedTypeException(
                $type,
                [
                    'string',
                    ResolvedColumnTypeInterface::class,
                    ColumnTypeInterface::class,
                ]
            );
        }

        $column = $type->createColumn($name, $datagrid, $options);

        // Explicitly call buildType() in order to be able to override either
        // createColumn() or buildType() in the resolved column type
        $type->buildType($column, $column->getOptions());

        return $column;
    }

    /**
     * Wraps a type into a ResolvedColumnTypeInterface implementation and connects
     * it with its parent type.
     *
     * @param ColumnTypeInterface $type The type to resolve
     *
     * @return ResolvedColumnTypeInterface The resolved type
     */
    private function resolveType(ColumnTypeInterface $type)
    {
        $parentType = $type->getParent();

        if ($parentType instanceof ColumnTypeInterface) {
            $parentType = $this->resolveType($parentType);
        } elseif (null !== $parentType) {
            $parentType = $this->typeRegistry->getType($parentType);
        }

        return $this->resolvedTypeFactory->createResolvedType(
            $type, // Type extensions are not supported for unregistered type instances,
            // i.e. type instances that are passed to the DatagridFactory directly,
            // nor for their parents, if getParent() also returns a type instance.
            [],
            $parentType
        );
    }
}
