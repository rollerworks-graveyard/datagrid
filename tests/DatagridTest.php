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

namespace Rollerworks\Component\Datagrid\Tests;

use Rollerworks\Component\Datagrid\Datagrid;
use Rollerworks\Component\Datagrid\Exception\BadMethodCallException;
use Rollerworks\Component\Datagrid\Extension\Core\Type\TextType;
use Rollerworks\Component\Datagrid\Test\MockTestCase;
use Rollerworks\Component\Datagrid\Tests\Fixtures\Entity;

class DatagridTest extends MockTestCase
{
    const GRID_NAME = 'users';

    /**
     * @var Datagrid
     */
    private $datagrid;

    protected function setUp()
    {
        $this->datagrid = new Datagrid(self::GRID_NAME, [$this->createColumn()]);
    }

    /**
     * @expectedException \Rollerworks\Component\Datagrid\Exception\InvalidArgumentException
     */
    public function testInvalidColumnThrowsException()
    {
        new Datagrid(self::GRID_NAME, [$this->createColumn(), null]);
    }

    public function testGetName()
    {
        self::assertSame(self::GRID_NAME, $this->datagrid->getName());
    }

    public function testHasColumn()
    {
        self::assertTrue($this->datagrid->hasColumn('foo'));
        self::assertTrue($this->datagrid->hasColumnType(TextType::class));

        self::assertFalse($this->datagrid->hasColumn('foo2'));
        self::assertFalse($this->datagrid->hasColumnType('this_type_cant_exists'));
    }

    public function testSetData()
    {
        $data = [
            new Entity('entity1'),
            new Entity('entity2'),
        ];

        $this->datagrid->setData($data);

        self::assertSame($data, $this->datagrid->getData());
    }

    public function testSetDataWithArrayAsSource()
    {
        $data = [
            ['some', 'data'],
            ['next', 'data'],
        ];

        $this->datagrid->setData($data);

        self::assertSame($data, $this->datagrid->getData());
    }

    public function testSetDataShouldOnlyBeCalledOnce()
    {
        $data = [
            new Entity('entity1'),
        ];

        $this->datagrid->setData($data);

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Datagrid::setData() can only be called once.');

        $this->datagrid->setData($data);
    }

    public function testCreateView()
    {
        $gridData = [
            new Entity('entity1'),
            new Entity('entity2'),
        ];

        $this->datagrid->setData($gridData);

        $view = $this->datagrid->createView();

        self::assertEquals(
            '_users',
            $view->vars['unique_block_prefix']
        );

        self::assertEquals(
            [
                'datagrid',
                '_users',
            ],
            $view->vars['block_prefixes']
        );

        self::assertEquals(
            [
                'unique_block_prefix' => '_users_row',
                'block_prefixes' => ['datagrid_row', '_users_row'],
            ],
            $view->vars['row_vars']
        );

        self::assertTrue($view->hasColumn('foo'));
        self::assertFalse($view->hasColumn('foo2'));
    }
}
