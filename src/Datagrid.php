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
use Rollerworks\Component\Datagrid\Exception\BadMethodCallException;
use Rollerworks\Component\Datagrid\Exception\InvalidArgumentException;
use Rollerworks\Component\Datagrid\Exception\UnexpectedTypeException;
use Rollerworks\Component\Datagrid\Exception\UnknownColumnException;

/**
 * Default Datagrid implementation.
 *
 * This class should not be construct directly.
 * Use DatagridFactory::createDatagrid() DatagridFactory::createDatagridBuilder()
 * to create a new Datagrid.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class Datagrid implements DatagridInterface
{
    /**
     * Unique datagrid name.
     *
     * @var string
     */
    private $name;

    /**
     * @var array|\Traversable|null
     */
    private $data;

    /**
     * Datagrid columns.
     *
     * @var ColumnInterface[]
     */
    private $columns = [];

    /**
     * @var callable
     */
    private $viewBuilder;

    /**
     * Constructor.
     *
     * @param string            $name        Name of the datagrid
     * @param ColumnInterface[] $columns     Columns of the datagrid
     * @param callable|null     $viewBuilder A callable view builder.
     *                                       Use the decorator pattern to chain multiple
     */
    public function __construct($name, array $columns, callable $viewBuilder = null)
    {
        $this->name = $name;
        $this->viewBuilder = $viewBuilder;

        foreach ($columns as $column) {
            if (!$column instanceof ColumnInterface) {
                throw new InvalidArgumentException(
                    sprintf(
                        'All columns passed to Datagrid::__construct() must of instances of %s.',
                        ColumnInterface::class
                    )
                );
            }

            $this->columns[$column->getName()] = $column;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumn($name)
    {
        if (!isset($this->columns[$name])) {
            throw new UnknownColumnException($name, $this);
        }

        return $this->columns[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * {@inheritdoc}
     */
    public function hasColumn($name)
    {
        return isset($this->columns[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function hasColumnType($type)
    {
        foreach ($this->columns as $column) {
            if ($column->getType()->getInnerType() instanceof $type) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function setData($data)
    {
        if (null !== $this->data) {
            throw new BadMethodCallException('Datagrid::setData() can only be called once.');
        }

        if (!is_array($data) && !$data instanceof \Traversable) {
            throw new UnexpectedTypeException($data, ['array', 'Traversable']);
        }

        $this->data = $data;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function createView()
    {
        if (!isset($this->data)) {
            throw new BadMethodCallException(
                'setDate() must be called before before you can create a view from the Datagrid.'
            );
        }

        $view = new DatagridView($this);

        if (null !== $this->viewBuilder) {
            $builder = $this->viewBuilder;
            $builder($view);
        }

        return $view;
    }
}
