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
use Rollerworks\Component\Datagrid\Exception\UnexpectedTypeException;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class DatagridFactory implements DatagridFactoryInterface
{

    /**
     * @var ColumnTypeRegistryInterface
     */
    private $typeRegistry;

    /**
     * @param ColumnTypeRegistryInterface $registry
     */
    public function __construct(ColumnTypeRegistryInterface $registry)
    {
        $this->typeRegistry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function createDatagrid($name)
    {
        return new Datagrid($name);
    }

    /**
     * {@inheritdoc}
     */
    public function createDatagridBuilder($name)
    {
        return new DatagridBuilder($this, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function createColumn($name, $type, array $options = [])
    {
        return $this->createColumnBuilder($name, $type, $options);
    }

    /**
     * Creates a new {@link Rollerworks\Component\Datagrid\Column\Column} instance.
     *
     * @param string $name
     * @param string $type
     * @param array  $options
     *
     * @throws UnexpectedTypeException
     *
     * @return Column
     */
    private function createColumnBuilder($name, $type = 'column', array $options = [])
    {
        if (!is_string($type)) {
            throw new UnexpectedTypeException($type, 'string');
        }

        $type = $this->typeRegistry->getType($type);

        $column = $type->createColumn($name, $options);

        // Explicitly call buildType() in order to be able to override either
        // createColumn() or buildType() in the resolved column type
        $type->buildType($column, $column->getOptions());

        return $column;
    }
}
