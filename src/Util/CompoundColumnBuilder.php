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

namespace Rollerworks\Component\Datagrid\Util;

use Rollerworks\Component\Datagrid\Column\CompoundColumn;
use Rollerworks\Component\Datagrid\DatagridBuilderInterface;
use Rollerworks\Component\Datagrid\DatagridFactoryInterface;
use Rollerworks\Component\Datagrid\Extension\Core\Type\CompoundColumnType;

/**
 * The CompoundColumnBuilder creates a new CompoundColumn.
 *
 * A CompoundColumn allows to group multiple columns together,
 * eg. one more more date value or one or more row actions.
 *
 * The 'data_provider' of the CompoundColumn will be set as the 'data_provider'
 * for each sub-column, unless a sub column sets the 'data_provider' explicitly.
 *
 * <code>
 * createCompound('actions', ['label' => 'Actions', 'data_provider' => function ($data) {return ['id' => $data->id();}])
 *   ->add('edit', ActionType::class, ['url_schema' => '/users/{id}/edit'])
 *   ->add('delete', ActionType::class, ['url_schema' => '/users/{id}/edit'])
 * ->end() // This registers the CompoundColumn at the DatagridBuilder, and return the DatagridBuilder.
 * </code>
 */
final class CompoundColumnBuilder implements CompoundColumnBuilderInterface
{
    private $unresolvedColumns = [];
    private $factory;
    private $builder;
    private $name;
    private $options;
    private $type;

    public function __construct(
        DatagridFactoryInterface $factory,
        DatagridBuilderInterface $builder,
        string $name,
        array $options = [],
        string $type = null
    ) {
        if (!isset($options['data_provider'])) {
            $options['data_provider'] = null;
        }

        $this->factory = $factory;
        $this->builder = $builder;
        $this->name = $name;
        $this->options = $options;
        $this->type = $type ?? CompoundColumnType::class;
    }

    /**
     * Add a column to the builder.
     *
     * @param string $name
     * @param string $type
     * @param array  $options
     *
     * @return self
     */
    public function add(string $name, string $type = null, array $options = []): CompoundColumnBuilderInterface
    {
        $this->unresolvedColumns[$name] = [
            'type' => $type,
            'options' => $options,
        ];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $name): CompoundColumnBuilderInterface
    {
        unset($this->unresolvedColumns[$name]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $name): bool
    {
        return isset($this->unresolvedColumns[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function end(): DatagridBuilderInterface
    {
        /** @var CompoundColumn $rootColumn */
        $rootColumn = $this->factory->createColumn($this->name, $this->type, $this->options);

        $columns = [];

        foreach ($this->unresolvedColumns as $n => $column) {
            if (!isset($column['options']['data_provider'])) {
                $column['options']['data_provider'] = $this->options['data_provider'];
            }

            $columns[$n] = $this->factory->createColumn(
                $n,
                $column['type'],
                array_replace($column['options'], ['parent_column' => $rootColumn])
            );
        }

        $rootColumn->setColumns($columns);
        $this->builder->set($rootColumn);

        return $this->builder;
    }
}
