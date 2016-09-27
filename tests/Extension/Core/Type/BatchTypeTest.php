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

namespace Rollerworks\Component\Datagrid\Tests\Extension\Core\Type;

use Rollerworks\Component\Datagrid\Datagrid;
use Rollerworks\Component\Datagrid\Extension\Core\Type\BatchType;
use Rollerworks\Component\Datagrid\Test\ColumnTypeTestCase;

class BatchTypeTest extends ColumnTypeTestCase
{
    /** @test */
    public function configures_batch_element_name_and_id()
    {
        $object1 = new \stdClass();
        $object1->key = 'b2-3b-fa-31-31:fb';

        $object2 = new \stdClass();
        $object2->key = '12-3b-fa-31-31-fb';

        $data = [1 => $object1, 2 => $object2];

        $column = $this->factory->createColumn('key', BatchType::class);
        $datagrid = new Datagrid('my_grid', [$column]);

        $datagrid->setData($data);
        $datagridView = $datagrid->createView();

        $view1 = $column->createCellView($datagridView, $object1, 1);
        $view2 = $column->createCellView($datagridView, $object2, 2);

        self::assertEquals(
            [
                'row' => 1,
                'datagrid_name' => 'my_grid',
                'selection_name' => 'my_grid[key][]',
                'selection_id' => 'my_grid-key__b2-3b-fa-31-31-fb',
            ],
            $view1->attributes
        );

        self::assertEquals(
            [
                'row' => 2,
                'datagrid_name' => 'my_grid',
                'selection_name' => 'my_grid[key][]',
                'selection_id' => 'my_grid-key__12-3b-fa-31-31-fb',
            ],
            $view2->attributes
        );
    }

    protected function getTestedType(): string
    {
        return BatchType::class;
    }
}
