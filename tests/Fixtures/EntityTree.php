<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Tests\Fixtures;

class EntityTree
{
    public $id;
    public $left = 'left';
    public $right = 'right';
    public $root = 'root';
    public $level = 'level';
    public $parent;

    public function __construct($id = null)
    {
        $this->id = $id;
    }

    public function getParent()
    {
        if (!isset($this->parent)) {
            $this->parent = new self('bar');
        }

        return $this->parent;
    }
}
