<?php declare(strict_types=1);

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid;

use Rollerworks\Component\Datagrid\Column\ColumnInterface;
use Rollerworks\Component\Datagrid\Column\ColumnTypeRegistryInterface;

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
    public function createDatagrid(string $name, array $columns): DatagridInterface
    {
        return new Datagrid($name, $columns);
    }

    /**
     * {@inheritdoc}
     */
    public function createDatagridBuilder(string $name): DatagridBuilderInterface
    {
        return new DatagridBuilder($this, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function createColumn(string $name, string $type, array $options = []): ColumnInterface
    {
        $type = $this->typeRegistry->getType($type);

        $column = $type->createColumn($name, $options);

        // Explicitly call buildType() in order to be able to override either
        // createColumn() or buildType() in the resolved column type.
        $type->buildType($column, $column->getOptions());

        return $column;
    }
}
