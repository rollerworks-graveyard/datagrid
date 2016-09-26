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

namespace Rollerworks\Component\Datagrid;

use Rollerworks\Component\Datagrid\Column\ColumnTypeInterface;
use Rollerworks\Component\Datagrid\Exception\InvalidArgumentException;

class PreloadedExtension implements DatagridExtensionInterface
{
    private $columnTypes = [];
    private $typeColumnExtensions = [];

    /**
     * Constructor.
     *
     * @param ColumnTypeInterface[] $types          The column-types that the extension
     *                                              should support
     * @param array[]               $typeExtensions The column-type extensions that the extension
     *                                              should support.
     *                                              Registered as [type => [ColumnTypeExtensionInterface object, ...]]
     */
    public function __construct(array $types, array $typeExtensions)
    {
        $this->columnTypes = $types;
        $this->typeColumnExtensions = $typeExtensions;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(string $name): ColumnTypeInterface
    {
        if (!isset($this->columnTypes[$name])) {
            throw new InvalidArgumentException(
                sprintf('The column-type "%s" can not be loaded by this extension.', $name)
            );
        }

        return $this->columnTypes[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function hasType(string $name): bool
    {
        return isset($this->columnTypes[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeExtensions(string $type): array
    {
        return isset($this->typeColumnExtensions[$type]) ? $this->typeColumnExtensions[$type] : [];
    }

    /**
     * {@inheritdoc}
     */
    public function hasTypeExtensions(string $type): bool
    {
        return !empty($this->typeColumnExtensions[$type]);
    }
}
