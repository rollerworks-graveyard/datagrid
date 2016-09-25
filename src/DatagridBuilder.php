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
use Rollerworks\Component\Datagrid\Exception\InvalidArgumentException;
use Rollerworks\Component\Datagrid\Util\CompoundColumnBuilder;
use Rollerworks\Component\Datagrid\Util\CompoundColumnBuilderInterface;

final class DatagridBuilder implements DatagridBuilderInterface
{
    private $factory;
    private $columns = [];
    private $unresolvedColumns = [];

    public function __construct(DatagridFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function add(string $name, string $type = null, array $options = [])
    {
        unset($this->columns[$name]);
        $this->unresolvedColumns[$name] = [
            'type' => $type,
            'options' => $options,
        ];

        return $this;
    }

    /**
     * Add a column instance to the builder.
     *
     * @param ColumnInterface $column
     *
     * @return DatagridBuilderInterface
     */
    public function set(ColumnInterface $column)
    {
        $this->columns[$column->getName()] = $column;
        unset($this->unresolvedColumns[$column->getName()]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function createCompound(string $name, array $options = [], string $type = null): CompoundColumnBuilderInterface
    {
        return new CompoundColumnBuilder($this->factory, $this, $name, $options, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $name)
    {
        unset($this->columns[$name], $this->unresolvedColumns[$name]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $name): bool
    {
        if (isset($this->unresolvedColumns[$name])) {
            return true;
        }

        if (isset($this->columns[$name])) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name): ColumnInterface
    {
        if (isset($this->unresolvedColumns[$name])) {
            $this->columns[$name] = $this->factory->createColumn(
                $name,
                $this->unresolvedColumns[$name]['type'],
                $this->unresolvedColumns[$name]['options']
            );

            unset($this->unresolvedColumns[$name]);

            return $this->columns[$name];
        }

        if (isset($this->columns[$name])) {
            return $this->columns[$name];
        }

        throw new InvalidArgumentException(sprintf('Column with the name "%s" is not set on the builder.', $name));
    }

    /**
     * {@inheritdoc}
     */
    public function getDatagrid(string $name): DatagridInterface
    {
        foreach ($this->unresolvedColumns as $n => $column) {
            $this->columns[$n] = $this->factory->createColumn(
                $n,
                $column['type'],
                $column['options']
            );

            unset($this->unresolvedColumns[$n]);
        }

        return new Datagrid($name, $this->columns);
    }
}
