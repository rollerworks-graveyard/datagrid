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

use Rollerworks\Component\Datagrid\Column\ColumnInterface;
use Rollerworks\Component\Datagrid\Column\ColumnTypeInterface;
use Rollerworks\Component\Datagrid\Exception\BadMethodCallException;
use Rollerworks\Component\Datagrid\Exception\InvalidArgumentException;
use Rollerworks\Component\Datagrid\Exception\UnexpectedTypeException;

final class DatagridBuilder implements DatagridBuilderInterface
{
    /**
     * @var DatagridFactory
     */
    private $factory;

    /**
     * @var string
     */
    private $name;

    /**
     * @var ColumnInterface[]
     */
    private $columns = [];

    /**
     * @var array[]
     */
    private $unresolvedColumns = [];

    /**
     * @var bool
     */
    private $locked;

    /**
     * @param DatagridFactoryInterface $factory
     * @param string                   $name
     */
    public function __construct(DatagridFactoryInterface $factory, $name)
    {
        $this->factory = $factory;
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function add($field, $type = null, array $options = [])
    {
        if ($this->locked) {
            throw new BadMethodCallException(
                'DatagridBuilder methods cannot be accessed anymore once the builder is turned into a Datagrid instance.'
            );
        }

        if (!$field instanceof ColumnInterface && !is_string($field)) {
            throw new UnexpectedTypeException($field, ['string', ColumnInterface::class]);
        }

        if ($field instanceof ColumnInterface) {
            $this->columns[$field->getName()] = $field;
            unset($this->unresolvedColumns[$field->getName()]);

            return $this;
        }

        if (!$type instanceof ColumnTypeInterface && !is_string($type)) {
            throw new UnexpectedTypeException($type, ['string', ColumnTypeInterface::class]);
        }

        $this->unresolvedColumns[$field] = [
            'type' => $type,
            'options' => $options,
        ];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($name)
    {
        if ($this->locked) {
            throw new BadMethodCallException(
                'DatagridBuilder methods cannot be accessed anymore once the builder is turned into a Datagrid instance.'
            );
        }

        unset($this->columns[$name], $this->unresolvedColumns[$name]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function has($name)
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
    public function get($name)
    {
        if (isset($this->unresolvedColumns[$name])) {
            return $this->unresolvedColumns[$name];
        }

        if (isset($this->columns[$name])) {
            return $this->columns[$name];
        }

        throw new InvalidArgumentException(sprintf('Column with the name "%s" is not set on the builder.', $name));
    }

    /**
     * {@inheritdoc}
     */
    public function getDatagrid()
    {
        if ($this->locked) {
            throw new BadMethodCallException(
                'DatagridBuilder methods cannot be accessed anymore once the builder is turned into a Datagrid instance.'
            );
        }

        $datagrid = new Datagrid($this->name);

        foreach ($this->unresolvedColumns as $name => $column) {
            $this->columns[$name] = $this->factory->createColumn(
                $name,
                $column['type'],
                $column['options']
            );

            unset($this->unresolvedColumns[$name]);
        }

        foreach ($this->columns as $column) {
            $datagrid->addColumn($column);
        }

        $this->locked = true;

        return $datagrid;
    }
}
