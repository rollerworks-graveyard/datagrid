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

use Rollerworks\Component\Datagrid\Exception\InvalidArgumentException;
use Rollerworks\Component\Datagrid\Exception\UnexpectedTypeException;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author FSi sp. z o.o. <info@fsi.pl>
 */
class DataRowset implements DataRowsetInterface
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param array|\Traversable $data
     *
     * @throws UnexpectedTypeException
     */
    public function __construct($data)
    {
        if (!is_array($data) && !$data instanceof \Traversable) {
            throw new UnexpectedTypeException($data, ['array', 'Traversable']);
        }

        foreach ($data as $id => $element) {
            $this->data[$id] = $element;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * {@inheritdoc}
     *
     * @return DatagridRowViewInterface current element from the rowset
     */
    public function current()
    {
        return current($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return key($this->data);
    }

    /**
     * {@inheritdoc}
     *
     * @return DatagridRowViewInterface|bool
     */
    public function next()
    {
        next($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        reset($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->key() !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * Get the row for the given offset.
     *
     * @param int $offset
     *
     * @throws \InvalidArgumentException
     *
     * @return DatagridRowViewInterface
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->data[$offset];
        }

        throw new InvalidArgumentException(sprintf('Row "%s" does not exist in rowset.', $offset));
    }

    /**
     * Does nothing.
     *
     * Required by the ArrayAccess implementation.
     *
     * @param string $offset
     * @param mixed  $value
     */
    public function offsetSet($offset, $value)
    {
    }

    /**
     * Does nothing.
     *
     * Required by the ArrayAccess implementation.
     *
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
    }
}
