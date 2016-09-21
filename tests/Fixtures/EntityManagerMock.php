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

use Doctrine\ORM\EntityManager;

class EntityManagerMock extends EntityManager
{
    protected $eventManager;

    protected $metadataFactory;

    public function __construct()
    {
    }

    public function _setEventManager($eventManager)
    {
        $this->eventManager = $eventManager;
    }

    public function _setMetadataFactory($metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
    }

    public function getMetadataFactory()
    {
        return $this->metadataFactory;
    }

    public function getEventManager()
    {
        return $this->eventManager;
    }

    public function getClassMetadata($className)
    {
        return;
    }

    public function getRepository($entityName)
    {
        return new EntityRepositoryMock();
    }
}
