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

use Rollerworks\Component\Datagrid\DatagridRowView;
use Rollerworks\Component\Datagrid\Test\MockTestCase;

class DatagridRowViewTest extends MockTestCase
{
    /** @test */
    public function it_creates_the_cells()
    {
        $source = [5 => new \stdClass()];

        $datagrid = $this->createDatagrid([$column = $this->createColumn()], $source);
        $datagridView = $this->createDatagridView($datagrid, ['row_vars' => ['class' => 'my-row']]);

        $view = new DatagridRowView($datagridView, $datagrid->getColumns(), $source, 5);

        self::assertSame(5, $view->index);
        self::assertSame($source[5], $view->source[5]);
        self::assertSame($datagridView, $view->datagrid);
        self::assertSame([], $view->vars);
    }
}
