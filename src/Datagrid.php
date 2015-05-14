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
use Rollerworks\Component\Datagrid\Column\ColumnInterface;
use Rollerworks\Component\Datagrid\DataMapper\DataMapperInterface;
use Rollerworks\Component\Datagrid\Exception\BadMethodCallException;
use Rollerworks\Component\Datagrid\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Datagrid implementation.
 *
 * This class should not be construct directly.
 * Use DatagridFactory::createDatagrid() for creating
 * a new Datagrid instance.
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
    private $data = ['original' => null, 'processed' => null];

    /**
     * DataMapper used by all columns to retrieve data from rowset objects.
     *
     * @var DataMapperInterface
     */
    private $dataMapper;

    /**
     * @var DatagridFactoryInterface
     */
    private $datagridFactory;

    /**
     * Columns.
     *
     * @var ColumnInterface[]
     */
    private $columns = [];

    /**
     * @var array[]
     */
    private $unresolvedColumns = [];

    /**
     * EventDispatcher for data and view creation.
     *
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * Constructor.
     *
     * @param string                   $name
     * @param DatagridFactoryInterface $datagridFactory
     * @param DataMapperInterface      $dataMapper
     */
    public function __construct($name, DatagridFactoryInterface $datagridFactory, $dataMapper = null)
    {
        $this->name = $name;
        $this->datagridFactory = $datagridFactory;
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
     * @param DataMapperInterface $dataMapper
     */
    public function setDataMapper(DataMapperInterface $dataMapper)
    {
        $this->dataMapper = $dataMapper;
    }

    /**
     * {@inheritdoc}
     */
    public function addEventListener($eventName, $listener, $priority = 0)
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
    public function getEventDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function addColumn($column, $type = 'text', $options = [])
    {
        if ($column instanceof ColumnInterface) {
            $this->columns[$column->getName()] = $column;
            unset($this->unresolvedColumns[$column->getName()]);

            return $this;
        }

        $this->columns[$column] = null;
        $this->unresolvedColumns[$column] = [
            'type' => $type,
            'options' => $options,
        ];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumn($name)
    {
        if (isset($this->unresolvedColumns[$name])) {
            return $this->resolveColumn($name);
        }

        if (!$this->hasColumn($name)) {
            throw new \InvalidArgumentException(sprintf('Column "%s" does not exist in datagrid.', $name));
        }

        return $this->columns[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns()
    {
        $this->resolveColumns();

        return $this->columns;
    }

    /**
     * {@inheritdoc}
     */
    public function hasColumn($name)
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
    public function hasColumnType($type)
    {
        $this->resolveColumns();

        foreach ($this->columns as $column) {
            if ($column->getType()->getName() === $type) {
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
        if (!$this->hasColumn($name)) {
            throw new \InvalidArgumentException(sprintf('Column "%s" does not exist in datagrid.', $name));
        }

        unset($this->columns[$name], $this->unresolvedColumns[$name]);

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
            throw new \InvalidArgumentException('Array or Traversable object is expected in setData method.');
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

        $this->resolveColumns();

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

        // Resolve after PRE_BUILD_VIEW so columns can be removed without speed lose
        $this->resolveColumns();

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
            throw new BadMethodCallException('setDate() must be called before before you can create a view from the Datagrid.');
        }

        return $this->rowset;
    }

    /**
     * Converts an unresolved column into a {@link Column} instance.
     *
     * @param string $name The name of the unresolved column
     *
     * @return Column The created instance.
     */
    private function resolveColumn($name)
    {
        $info = $this->unresolvedColumns[$name];
        $column = $this->datagridFactory->createColumn($name, $info['type'], $this, $info['options']);
        $this->columns[$name] = $column;
        unset($this->unresolvedColumns[$name]);

        return $column;
    }

    /**
     * Converts all unresolved columns into {@link Column} instances.
     */
    private function resolveColumns()
    {
        foreach ($this->unresolvedColumns as $name => $info) {
            $this->columns[$name] = $this->datagridFactory->createColumn($name, $info['type'], $this, $info['options']);
        }

        $this->unresolvedColumns = [];
    }
}
