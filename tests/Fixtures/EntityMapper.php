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

namespace Rollerworks\Component\Datagrid\Tests\Fixtures;

class EntityMapper
{
    public $id;

    private $private_id;

    private $name;

    private $surname;

    private $collection;

    private $private_collection;

    private $ready;

    private $protected_ready;

    private $tags = [];

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    protected function setProtectedName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function setPrivateId($id)
    {
        $this->private_id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    protected function getSurname()
    {
        return $this->surname;
    }

    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    public function hasCollection()
    {
        return isset($this->collection);
    }

    public function setCollection($collection)
    {
        $this->collection = $collection;
    }

    public function setPrivateCollection($collection)
    {
        $this->private_collection = $collection;
    }

    public function hasPrivateCollection()
    {
        return isset($this->privatecollection);
    }

    public function setReady($ready)
    {
        $this->ready = (bool) $ready;
    }

    public function isReady()
    {
        return $this->ready;
    }

    public function setProtectedReady($ready)
    {
        $this->protected_ready = (bool) $ready;
    }

    protected function isProtectedReady()
    {
        return $this->protected_ready;
    }

    public function addTag($tag)
    {
        $this->tags[] = $tag;
    }

    public function getTags()
    {
        return $this->tags;
    }

    protected function addProtectedTag($tag)
    {
        $this->tags[] = $tag;
    }
}
