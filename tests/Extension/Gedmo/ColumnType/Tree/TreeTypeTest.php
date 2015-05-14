<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Tests\Extension\Gedmo\ColumnType;

use Rollerworks\Component\Datagrid\Extension\Gedmo\GedmoDoctrineExtension;
use Rollerworks\Component\Datagrid\Test\ColumnTypeTestCase;
use Rollerworks\Component\Datagrid\Tests\Fixtures\EntityManagerMock;
use Rollerworks\Component\Datagrid\Tests\Fixtures\EntityTree;
use Rollerworks\Component\Datagrid\Tests\Fixtures\EventManagerMock;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author FSi sp. z o.o. <info@fsi.pl>
 */
class TreeTypeTest extends ColumnTypeTestCase
{
    /**
     * @var \Doctrine\Common\Persistence\ManagerRegistry
     */
    protected $managerRegistry;

    protected function setUp()
    {
        if (!interface_exists('Doctrine\Common\Persistence\ManagerRegistry') || !class_exists('Gedmo\Tree\TreeListener')) {
            $this->markTestSkipped('Doctrine\Common\Persistence\ManagerRegistry and Gedmo\Tree\TreeListener are required for the "gedmo_tree" column type.');
        }

        $this->managerRegistry = $this->getMock('Doctrine\\Common\\Persistence\\ManagerRegistry');

        parent::setUp();
    }

    protected function getExtensions()
    {
        return [new GedmoDoctrineExtension($this->managerRegistry)];
    }

    protected function getTestedType()
    {
        return 'gedmo_tree';
    }

    public function testPassLabelToView()
    {
        $this->datagrid->addColumn('tree', $this->getTestedType(), ['label' => 'My label', 'field_mapping' => ['key']]);

        $object = new \stdClass();
        $object->key = ' foo ';
        $this->datagrid->setData([1 => $object]);

        $datagridView = $this->datagrid->createView();
        $this->assertEquals('My label', $datagridView->getColumn('tree')->label);
    }

    public function testInvalidValueThrowsException()
    {
        $this->datagrid->addColumn('tree', $this->getTestedType(), ['label' => 'My label', 'field_mapping' => ['key']]);

        $object = new \stdClass();
        $object->key = 'This is a string, not an object';
        $this->datagrid->setData([1 => $object]);

        $datagridView = $this->datagrid->createView();

        $this->setExpectedException('Rollerworks\Component\Datagrid\Exception\UnexpectedTypeException', 'Expected argument of type "object", "string" given');
        $datagridView->current();
    }

    public function testCellView()
    {
        $this->getManagerRegistry($this->managerRegistry);

        $this->datagrid->addColumn('tree', $this->getTestedType(), ['label' => 'My label', 'field_mapping' => ['key'], 'em' => 'default']);

        $object = new \stdClass();
        $object->key = new EntityTree('foo');
        $this->datagrid->setData([1 => $object]);

        $datagridView = $this->datagrid->createView();

        $this->assertEquals(
            [
                'row' => 1,
                'id' => 'foo',
                'root' => 'root',
                'parent' => 'bar',
                'left' => 'left',
                'right' => 'right',
                'level' => 'level',
                'children' => 2,
            ],
            $datagridView->current()->current()->attributes
        );
    }

    protected function getManagerRegistry($managerRegistry = null)
    {
        $managerRegistry = $managerRegistry ?: $this->getMock('Doctrine\\Common\\Persistence\\ManagerRegistry');
        $managerRegistry->expects($this->any())
            ->method('getManagerForClass')
            ->will($this->returnCallback(function () {
                $manager = $this->getMock('Doctrine\\Common\\Persistence\\ObjectManager');
                $manager->expects($this->any())
                    ->method('getMetadataFactory')
                    ->will($this->returnCallback(function () {
                        $metadataFactory = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadataFactory');
                        $metadataFactory->expects($this->any())
                            ->method('getMetadataFor')
                            ->will($this->returnCallback(function ($class) {
                                switch ($class) {
                                    case 'Rollerworks\\Component\\Datagrid\\Tests\\Fixtures\\EntityTree':
                                        $metadata = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
                                        $metadata->expects($this->any())
                                            ->method('getIdentifierFieldNames')
                                            ->will($this->returnValue([
                                                'id',
                                            ]));

                                        return $metadata;
                                }

                                return;
                            }));

                        $metadataFactory->expects($this->any())
                            ->method('getClassMetadata')
                            ->will($this->returnCallback(function ($class) use ($metadataFactory) {
                                return $metadataFactory->getMetadataFor($class);
                            }));

                        return $metadataFactory;
                    }));

                $manager->expects($this->any())
                    ->method('getClassMetadata')
                    ->will($this->returnCallback(function ($class) {
                        switch ($class) {
                            case 'Rollerworks\\Component\\Datagrid\\Tests\\Fixtures\\EntityTree':
                                $metadata = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
                                $metadata->expects($this->any())
                                    ->method('getIdentifierFieldNames')
                                    ->will($this->returnValue([
                                        'id',
                                    ]));
                                $metadata->isMappedSuperclass = false;
                                $metadata->rootEntityName = $class;

                                return $metadata;
                        }

                        return;
                    }));

                return $manager;
            }));

        $treeListener = $this->getMock('Gedmo\Tree\TreeListener');
        $strategy = $this->getMock('Gedmo\Tree\Strategy');

        $treeListener->expects($this->once())
            ->method('getStrategy')
            ->will($this->returnValue($strategy));

        $treeListener->expects($this->once())
            ->method('getConfiguration')
            ->will($this->returnValue([
                'left' => 'left',
                'right' => 'right',
                'root' => 'root',
                'level' => 'level',
                'parent' => 'parent',
            ]));

        $strategy->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('nested'));

        $evm = new EventManagerMock([$treeListener]);
        $em = new EntityManagerMock();
        $em->_setEventManager($evm);

        $managerRegistry->expects($this->any())
            ->method('getManager')
            ->will($this->returnValue($em));

        $managerRegistry->expects($this->any())
            ->method('getManagerForClass')
            ->with('Rollerworks\\Component\\Datagrid\\Tests\\Fixtures\\EntityTree')
            ->will($this->returnValue($em));

        return $managerRegistry;
    }
}
