<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Column;

use Rollerworks\Component\Datagrid\DatagridExtensionInterface;
use Rollerworks\Component\Datagrid\Exception\ExceptionInterface;
use Rollerworks\Component\Datagrid\Exception\InvalidArgumentException;
use Rollerworks\Component\Datagrid\Exception\UnexpectedTypeException;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class ColumnTypeRegistry implements ColumnTypeRegistryInterface
{
    /**
     * Extensions.
     *
     * @var DatagridExtensionInterface[] An array of FormExtensionInterface
     */
    private $extensions = [];

    /**
     * @var array
     */
    private $types = [];

    /**
     * @var ResolvedColumnTypeFactoryInterface
     */
    private $resolvedTypeFactory;

    /**
     * Constructor.
     *
     * @param DatagridExtensionInterface[]       $extensions          An array of DatagridExtensionInterface
     * @param ResolvedColumnTypeFactoryInterface $resolvedTypeFactory The factory for resolved form types.
     *
     * @throws UnexpectedTypeException if an extension does not implement DatagridExtensionInterface
     */
    public function __construct(array $extensions, ResolvedColumnTypeFactoryInterface $resolvedTypeFactory)
    {
        foreach ($extensions as $extension) {
            if (!$extension instanceof DatagridExtensionInterface) {
                throw new UnexpectedTypeException($extension, 'Rollerworks\Component\Datagrid\DatagridExtensionInterface');
            }
        }

        $this->extensions = $extensions;
        $this->resolvedTypeFactory = $resolvedTypeFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function getType($name)
    {
        if (!is_string($name)) {
            throw new UnexpectedTypeException($name, 'string');
        }

        if (!isset($this->types[$name])) {
            /** @var ColumnTypeInterface $type */
            $type = null;

            foreach ($this->extensions as $extension) {
                /* @var DatagridExtensionInterface $extension */
                if ($extension->hasColumnType($name)) {
                    $type = $extension->getColumnType($name);
                    break;
                }
            }

            if (!$type) {
                throw new InvalidArgumentException(sprintf('Could not load column type "%s".', $name));
            }

            $this->resolveAndAddType($type);
        }

        return $this->types[$name];
    }

    /**
     * {@inheritDoc}
     */
    public function hasType($name)
    {
        if (isset($this->types[$name])) {
            return true;
        }

        try {
            $this->getType($name);
        } catch (ExceptionInterface $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * Wraps a type into a ResolvedFormTypeInterface implementation and connects
     * it with its parent type.
     *
     * @param ColumnTypeInterface $type The type to resolve.
     *
     * @return ResolvedColumnTypeInterface The resolved type.
     */
    private function resolveAndAddType(ColumnTypeInterface $type)
    {
        $parentType = $type->getParent();

        if ($parentType instanceof ColumnTypeInterface) {
            $this->resolveAndAddType($parentType);
            $parentType = $parentType->getName();
        }

        $typeExtensions = [];

        foreach ($this->extensions as $extension) {
            /* @var DatagridExtensionInterface $extension */

            if ($extension->hasColumnTypeExtensions($type->getName())) {
                $typeExtensions = array_merge(
                    $typeExtensions, $extension->getColumnTypeExtensions($type->getName())
                );
            }
        }

        $this->types[$type->getName()] = $this->resolvedTypeFactory->createResolvedType(
            $type, $typeExtensions, $parentType ? $this->getType($parentType) : null
        );
    }
}
