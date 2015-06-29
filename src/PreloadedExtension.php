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

class PreloadedExtension implements DatagridExtensionInterface
{
    /**
     * @var ColumnTypeInterface[]
     */
    private $columnTypes = [];

    /**
     * @var ColumnTypeExtensionInterface[]
     */
    private $typeColumnExtensions = [];

    /**
     * Creates a new preloaded extension.
     *
     * @param ColumnTypeInterface[]          $types          The column-types that the extension
     *                                                       should support.
     * @param ColumnTypeExtensionInterface[] $typeExtensions The column-type extensions that the extension
     *                                                       should support.
     */
    public function __construct(array $types, array $typeExtensions)
    {
        $this->columnTypes = $types;
        $this->typeColumnExtensions = $typeExtensions;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnType($name)
    {
        if (!isset($this->columnTypes[$name])) {
            throw new InvalidArgumentException(
                sprintf('The column-type "%s" can not be loaded by this extension', $name)
            );
        }

        return $this->columnTypes[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function hasColumnType($name)
    {
        return isset($this->columnTypes[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnTypeExtensions($name)
    {
        return isset($this->typeColumnExtensions[$name]) ? $this->typeColumnExtensions[$name] : [];
    }

    /**
     * {@inheritdoc}
     */
    public function hasColumnTypeExtensions($name)
    {
        return !empty($this->typeColumnExtensions[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function registerListeners(DatagridInterface $datagrid)
    {
        // no op
    }
}
