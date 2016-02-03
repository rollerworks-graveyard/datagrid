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
use Rollerworks\Component\Datagrid\Exception\DatagridException;
use Rollerworks\Component\Datagrid\Exception\UnexpectedTypeException;
use Rollerworks\Component\Datagrid\Exception\UnknownColumnException;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Default Datagrid implementation.
 *
 * This class should not be construct directly.
 * Use DatagridFactory::createDatagrid() DatagridFactory::createDatagridBuilder()
 * to create a new Datagrid.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author FSi sp. z o.o. <info@fsi.pl>
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
     * EventDispatcher for data and view creation.
     *
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->dispatcher = new EventDispatcher();
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
    public function addEventListener($eventName, callable $listener, $priority = 0)
    {
        $this->dispatcher->addListener($eventName, $listener, $priority);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addEventSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->dispatcher->addSubscriber($subscriber);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addColumn(ColumnInterface $column)
    {
        $name = $column->getName();

        if (isset($this->columns[$name])) {
            throw new DatagridException(
                sprintf('A column with name "%s" is already registered on the datagrid', $name)
            );
        }

        $this->columns[$name] = $column;

        return $this;
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
    public function removeColumn($name)
    {
        if (!isset($this->columns[$name])) {
            throw new UnknownColumnException($name, $this);
        }

        unset($this->columns[$name]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function clearColumns()
    {
        $this->columns = [];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setData($data)
    {
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

        $event = new DatagridViewEvent($this, $view);
        $this->dispatcher->dispatch(DatagridEvents::BUILD_VIEW, $event);

        return $view;
    }
}
