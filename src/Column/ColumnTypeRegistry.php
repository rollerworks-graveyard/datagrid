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
     * @var DatagridExtensionInterface[]
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
     * @param ResolvedColumnTypeFactoryInterface $resolvedTypeFactory The factory for resolved form types
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
     * {@inheritdoc}
     */
    public function getType($name)
    {
        if (!isset($this->types[$name])) {
            $type = null;

            foreach ($this->extensions as $extension) {
                if ($extension->hasType($name)) {
                    $type = $extension->getType($name);

                    break;
                }
            }

            if (!$type) {
                // Support fully-qualified class names.
                if (class_exists($name) && in_array(ColumnTypeInterface::class, class_implements($name), true)) {
                    $type = new $name();
                } else {
                    throw new InvalidArgumentException(sprintf('Could not load type "%s"', $name));
                }
            }

            $this->types[$name] = $this->resolveType($type);
        }

        return $this->types[$name];
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * Wraps a type into a ResolvedFormTypeInterface implementation and connects
     * it with its parent type.
     *
     * @param ColumnTypeInterface $type The type to resolve
     *
     * @return ResolvedColumnTypeInterface The resolved type
     */
    private function resolveType(ColumnTypeInterface $type)
    {
        $parentType = $type->getParent();
        $fqcn = get_class($type);

        $typeExtensions = [];

        foreach ($this->extensions as $extension) {
            $typeExtensions = array_merge(
                $typeExtensions,
                $extension->getTypeExtensions($fqcn)
            );
        }

        return $this->resolvedTypeFactory->createResolvedType(
            $type,
            $typeExtensions,
            $parentType ? $this->getType($parentType) : null
        );
    }
}
