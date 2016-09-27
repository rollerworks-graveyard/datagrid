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

namespace Rollerworks\Component\Datagrid\Util;

use Rollerworks\Component\Datagrid\Column\ColumnTypeExtensionInterface;
use Rollerworks\Component\Datagrid\Column\ColumnTypeInterface;
use Rollerworks\Component\Datagrid\Column\ColumnTypeRegistry;
use Rollerworks\Component\Datagrid\Column\ResolvedColumnTypeFactory;
use Rollerworks\Component\Datagrid\Column\ResolvedColumnTypeFactoryInterface;
use Rollerworks\Component\Datagrid\DatagridExtensionInterface;
use Rollerworks\Component\Datagrid\DatagridFactory;
use Rollerworks\Component\Datagrid\DatagridRegistry;
use Rollerworks\Component\Datagrid\DatagridRegistryInterface;
use Rollerworks\Component\Datagrid\PreloadedExtension;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
final class DatagridFactoryBuilder
{
    private $resolvedTypeFactory;
    private $datagridRegistry;
    private $extensions = [];
    private $types = [];
    private $typeExtensions = [];

    /**
     * @param ResolvedColumnTypeFactoryInterface $resolvedTypeFactory
     *
     * @return DatagridFactoryBuilder
     */
    public function setResolvedTypeFactory(ResolvedColumnTypeFactoryInterface $resolvedTypeFactory): self
    {
        $this->resolvedTypeFactory = $resolvedTypeFactory;

        return $this;
    }

    /**
     * @param DatagridRegistryInterface $datagridRegistry
     *
     * @return DatagridFactoryBuilder
     */
    public function setDatagridRegistry(DatagridRegistryInterface $datagridRegistry): self
    {
        $this->datagridRegistry = $datagridRegistry;

        return $this;
    }

    /**
     * @param DatagridExtensionInterface $extension
     *
     * @return DatagridFactoryBuilder
     */
    public function addExtension(DatagridExtensionInterface $extension): self
    {
        $this->extensions[] = $extension;

        return $this;
    }

    /**
     * @param DatagridExtensionInterface[] $extensions
     *
     * @return DatagridFactoryBuilder
     */
    public function addExtensions($extensions): self
    {
        $this->extensions = array_merge($this->extensions, $extensions);

        return $this;
    }

    /**
     * @param ColumnTypeInterface $type
     *
     * @return DatagridFactoryBuilder
     */
    public function addType(ColumnTypeInterface $type): self
    {
        $this->types[get_class($type)] = $type;

        return $this;
    }

    /**
     * @param ColumnTypeInterface[] $types
     *
     * @return DatagridFactoryBuilder
     */
    public function addTypes(array $types): self
    {
        foreach ($types as $type) {
            $this->types[get_class($type)] = $type;
        }

        return $this;
    }

    /**
     * @param ColumnTypeExtensionInterface $typeExtension
     *
     * @return DatagridFactoryBuilder
     */
    public function addTypeExtension(ColumnTypeExtensionInterface $typeExtension): self
    {
        $this->typeExtensions[$typeExtension->getExtendedType()][] = $typeExtension;

        return $this;
    }

    /**
     * @param ColumnTypeExtensionInterface[] $typeExtensions
     *
     * @return DatagridFactoryBuilder
     */
    public function addTypeExtensions(array $typeExtensions): self
    {
        foreach ($typeExtensions as $typeExtension) {
            $this->typeExtensions[$typeExtension->getExtendedType()][] = $typeExtension;
        }

        return $this;
    }

    /**
     * @return DatagridFactory
     */
    public function getDatagridFactory(): DatagridFactory
    {
        $extensions = $this->extensions;

        if (count($this->types) > 0 || count($this->typeExtensions) > 0) {
            $extensions[] = new PreloadedExtension($this->types, $this->typeExtensions);
        }

        $typesRegistry = new ColumnTypeRegistry(
            $extensions,
            $this->resolvedTypeFactory ?: new ResolvedColumnTypeFactory()
        );

        return new DatagridFactory($typesRegistry, $this->datagridRegistry ?: new DatagridRegistry());
    }
}
