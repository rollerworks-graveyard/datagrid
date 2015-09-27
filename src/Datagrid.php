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
use Rollerworks\Component\Datagrid\DataMapper\DataMapperInterface;
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
     * DataCollection used to render the view.
     *
     * @var DataRowset
     */
    private $rowset;

    /**
     * @var array
     */
    private $data = [
        'original' => null,
        'processed' => null,
    ];

    /**
     * DataMapper used by all columns to retrieve
     * data from rowset objects.
     *
     * @var DataMapperInterface
     */
    private $dataMapper;

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
     * @param string              $name
     * @param DataMapperInterface $dataMapper
     */
    public function __construct($name, $dataMapper = null)
    {
        $this->name = $name;
        $this->dispatcher = new EventDispatcher();
        $this->dataMapper = $dataMapper;
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
    public function getDataMapper()
    {
        return $this->dataMapper;
    }

    /**
     * {@inheritdoc}
     */
    public function setData($data)
    {
        $this->data['original'] = $data;

        $event = new DatagridEvent($this, $data);
        $this->dispatcher->dispatch(DatagridEvents::PRE_SET_DATA, $event);

        $data = $event->getData();

        if (!is_array($data) && !$data instanceof \Traversable) {
            throw new UnexpectedTypeException($data, ['array', 'Traversable']);
        }

        $this->rowset = new DataRowset($data);
        $this->data['processed'] = $data;

        $event = new DatagridEvent($this, $this->rowset);
        $this->dispatcher->dispatch(DatagridEvents::POST_SET_DATA, $event);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($original = false)
    {
        if ($original) {
            return $this->data['original'];
        }

        return $this->data['processed'];
    }

    /**
     * {@inheritdoc}
     */
    public function bindData($data)
    {
        $event = new DatagridEvent($this, $data);
        $this->dispatcher->dispatch(DatagridEvents::PRE_BIND_DATA, $event);

        $data = $event->getData();

        if (!is_array($data) && !$data instanceof \ArrayIterator) {
            throw new UnexpectedTypeException($data, ['array', 'ArrayIterator']);
        }

        foreach ($data as $index => $values) {
            if (!isset($this->rowset[$index])) {
                unset($data[$index]);

                continue;
            }

            $object = $this->rowset[$index];

            foreach ($this->columns as $column) {
                $column->bindData($values, $object, $index);
            }
        }

        $event = new DatagridEvent($this, $data);
        $this->dispatcher->dispatch(DatagridEvents::POST_BIND_DATA, $event);
    }

    /**
     * {@inheritdoc}
     */
    public function createView()
    {
        $event = new DatagridEvent($this, null);
        $this->dispatcher->dispatch(DatagridEvents::PRE_BUILD_VIEW, $event);

        $view = new DatagridView($this, $this->columns, $this->getRowset());

        $event = new DatagridEvent($this, $view);
        $this->dispatcher->dispatch(DatagridEvents::POST_BUILD_VIEW, $event);
        $view = $event->getData();

        return $view;
    }

    /**
     * Returns data grid rowset that contains source data.
     *
     * @throws BadMethodCallException When getRowset() is called before any date is set.
     *
     * @return DataRowset
     */
    private function getRowset()
    {
        if (!isset($this->rowset)) {
            throw new BadMethodCallException(
                'setDate() must be called before before you can create a view from the Datagrid.'
            );
        }

        return $this->rowset;
    }
}
