<?php

declare(strict_types=1);

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
use Rollerworks\Component\Datagrid\Util\StringUtil;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class DatagridFactory implements DatagridFactoryInterface
{
    private $typeRegistry;
    private $datagridRegistry;

    public function __construct(ColumnTypeRegistryInterface $typeRegistry, DatagridRegistryInterface $datagridRegistry)
    {
        $this->typeRegistry = $typeRegistry;
        $this->datagridRegistry = $datagridRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function createDatagrid($configurator, string $name = null, array $options = []): DatagridInterface
    {
        if (!$configurator instanceof DatagridConfiguratorInterface) {
            $configurator = $this->datagridRegistry->getConfigurator($configurator);
        }

        if (null === $name) {
            $name = StringUtil::fqcnToBlockPrefix(get_class($configurator));
        }

        $builder = $this->createDatagridBuilder();
        $configurator->buildDatagrid($builder, $options);

        return $builder->getDatagrid($name);
    }

    /**
     * {@inheritdoc}
     */
    public function createDatagridBuilder(): DatagridBuilderInterface
    {
        return new DatagridBuilder($this);
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
