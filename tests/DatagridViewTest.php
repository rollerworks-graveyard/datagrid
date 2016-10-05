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

use Rollerworks\Component\Datagrid\Column\HeaderView;
use Rollerworks\Component\Datagrid\DatagridView;
use Rollerworks\Component\Datagrid\Exception\InvalidArgumentException;
use Rollerworks\Component\Datagrid\Test\MockTestCase;
use Rollerworks\Component\Datagrid\Tests\Fixtures\Entity;

class DatagridViewTest extends MockTestCase
{
    /**
     * @var DatagridView
     */
    private $gridView;

    protected function setUp()
    {
        $datagrid = $this->createDatagrid(
            [$column = $this->createColumn()],
            [
                new Entity('entity1'),
                new Entity('entity2'),
            ]
        );

        $this->gridView = new DatagridView($datagrid);
        $this->gridView->init($datagrid);
    }

    public function testHasColumn()
    {
        $this->assertTrue($this->gridView->hasColumn('foo'));
        $this->assertFalse($this->gridView->hasColumn('bar'));

        $this->assertInstanceOf(HeaderView::class, $this->gridView->getColumn('foo'));
    }

    public function testGetColumn()
    {
        $this->assertInstanceOf(HeaderView::class, $this->gridView->getColumn('foo'));
    }

    public function testThrowsExceptionWhenGettingUnRegisteredColumn()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->gridView->getColumn('bar');
    }

    public function testCountReturnsNumberOfRows()
    {
        $this->assertCount(2, $this->gridView);
    }
}
