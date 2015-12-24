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

use Rollerworks\Component\Datagrid\Column\ColumnTypeExtensionInterface;
use Rollerworks\Component\Datagrid\Column\ColumnTypeInterface;
use Rollerworks\Component\Datagrid\Exception\InvalidArgumentException;
use Rollerworks\Component\Datagrid\Exception\UnexpectedTypeException;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
abstract class AbstractDatagridExtension implements DatagridExtensionInterface
{
    /**
     * All column types extensions provided by Datagrid extension.
     *
     * @var array
     */
    private $columnTypesExtensions;

    /**
     * All column types provided by extension.
     *
     * @var array
     */
    private $columnTypes;

    /**
     * {@inheritdoc}
     */
    public function getColumnType($type)
    {
        if (null === $this->columnTypes) {
            $this->initColumnTypes();
        }

        if (!isset($this->columnTypes[$type])) {
            throw new InvalidArgumentException(sprintf('Column type "%s" can not be loaded by this extension.', $type));
        }

        return $this->columnTypes[$type];
    }

    /**
     * {@inheritdoc}
     */
    public function hasColumnType($type)
    {
        if (null === $this->columnTypes) {
            $this->initColumnTypes();
        }

        return isset($this->columnTypes[$type]);
    }

    /**
     * {@inheritdoc}
     */
    public function hasColumnTypeExtensions($type)
    {
        if (null === $this->columnTypesExtensions) {
            $this->initColumnTypesExtensions();
        }

        return isset($this->columnTypesExtensions[$type]);
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnTypeExtensions($type)
    {
        if (null === $this->columnTypesExtensions) {
            $this->initColumnTypesExtensions();
        }

        return isset($this->columnTypesExtensions[$type]) ? $this->columnTypesExtensions[$type] : [];
    }

    /**
     * {@inheritdoc}
     */
    public function registerListeners(DatagridInterface $datagrid)
    {
    }

    /**
     * If extension needs to provide new column types this function
     * should be overloaded in child class and return an array of ColumnTypeInterface
     * instances.
     *
     * @return ColumnTypeInterface[]
     *
     * @codeCoverageIgnore
     */
    protected function loadColumnTypes()
    {
        return [];
    }

    /**
     * If extension needs to load event subscribers this method should be overloaded in
     * child class and return array event subscribers.
     *
     * @return array
     *
     * @codeCoverageIgnore
     */
    protected function loadSubscribers()
    {
        return [];
    }

    /**
     * If extension needs to provide new column types this function
     * should be overloaded in child class and return array of DatagridColumnTypeInterface
     * instances.
     *
     * @return array
     *
     * @codeCoverageIgnore
     */
    protected function loadColumnTypesExtensions()
    {
        return [];
    }

    /**
     * @throws UnexpectedTypeException
     */
    private function initColumnTypes()
    {
        $this->columnTypes = [];

        foreach ($this->loadColumnTypes() as $type) {
            if (!$type instanceof ColumnTypeInterface) {
                throw new UnexpectedTypeException($type, ColumnTypeInterface::class);
            }

            $this->columnTypes[get_class($type)] = $type;
        }
    }

    /**
     * @throws UnexpectedTypeException
     */
    private function initColumnTypesExtensions()
    {
        $this->columnTypesExtensions = [];

        foreach ($this->loadColumnTypesExtensions() as $extension) {
            if (!$extension instanceof ColumnTypeExtensionInterface) {
                throw new UnexpectedTypeException($extension, ColumnTypeExtensionInterface::class);
            }

            $type = $extension->getExtendedType();

            $this->columnTypesExtensions[$type][] = $extension;
        }
    }
}
