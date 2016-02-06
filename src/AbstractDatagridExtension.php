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
    private $typesExtensions;

    /**
     * All column types provided by extension.
     *
     * @var array
     */
    private $types;

    /**
     * {@inheritdoc}
     */
    public function getType($type)
    {
        if (null === $this->types) {
            $this->initColumnTypes();
        }

        if (!isset($this->types[$type])) {
            throw new InvalidArgumentException(sprintf('Column type "%s" can not be loaded by this extension.', $type));
        }

        return $this->types[$type];
    }

    /**
     * {@inheritdoc}
     */
    public function hasType($type)
    {
        if (null === $this->types) {
            $this->initColumnTypes();
        }

        return isset($this->types[$type]);
    }

    /**
     * {@inheritdoc}
     */
    public function hasTypeExtensions($type)
    {
        if (null === $this->typesExtensions) {
            $this->initTypesExtensions();
        }

        return isset($this->typesExtensions[$type]);
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeExtensions($type)
    {
        if (null === $this->typesExtensions) {
            $this->initTypesExtensions();
        }

        return isset($this->typesExtensions[$type]) ? $this->typesExtensions[$type] : [];
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
    protected function loadTypes()
    {
        return [];
    }

    /**
     * If extension needs to provide new column types this function
     * should be overloaded in child class and return array of ColumnTypeInterface
     * instances.
     *
     * @return array
     *
     * @codeCoverageIgnore
     */
    protected function loadTypesExtensions()
    {
        return [];
    }

    /**
     * @throws UnexpectedTypeException
     */
    private function initColumnTypes()
    {
        $this->types = [];

        foreach ($this->loadTypes() as $type) {
            if (!$type instanceof ColumnTypeInterface) {
                throw new UnexpectedTypeException($type, ColumnTypeInterface::class);
            }

            $this->types[get_class($type)] = $type;
        }
    }

    /**
     * @throws UnexpectedTypeException
     */
    private function initTypesExtensions()
    {
        $this->typesExtensions = [];

        foreach ($this->loadTypesExtensions() as $extension) {
            if (!$extension instanceof ColumnTypeExtensionInterface) {
                throw new UnexpectedTypeException($extension, ColumnTypeExtensionInterface::class);
            }

            $type = $extension->getExtendedType();

            $this->typesExtensions[$type][] = $extension;
        }
    }
}
