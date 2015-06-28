<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Tests;

use Prophecy\Argument;
use Rollerworks\Component\Datagrid\DatagridViewInterface;
use Rollerworks\Component\Datagrid\Test\DatagridPerformanceTestCase;

class DatagridPerformanceTest extends DatagridPerformanceTestCase
{
    /**
     * This test case is realistic in collection rows where each
     * row contains the same data.
     *
     * This is most common use-case, showing more rows
     * on the page is likely to hit memory limits (for the data set itself).
     * And more rows will give display problems.
     *
     * @group benchmark
     */
    public function testGenerateViewWith100RowsAnd10Columns()
    {
        $this->setMaxRunningTime(1);

        $datagrid = $this->factory->createDatagrid('test');

        $datagrid->addColumn($this->factory->createColumn('id', 'number', $datagrid, ['label' => '#', 'field_mapping' => ['id']]));
        $datagrid->addColumn($this->factory->createColumn('name', 'text', $datagrid, ['label' => 'Name', 'field_mapping' => ['name']]));
        $datagrid->addColumn($this->factory->createColumn('email', 'text', $datagrid, ['label' => 'Email', 'field_mapping' => ['email']]));
        $datagrid->addColumn($this->factory->createColumn('regdate', 'datetime', $datagrid, ['label' => 'regdate', 'field_mapping' => ['regdate']]));
        $datagrid->addColumn($this->factory->createColumn('last_modified', 'datetime', $datagrid, ['label' => 'last_modified', 'field_mapping' => ['lastModified']]));
        $datagrid->addColumn(
            $this->factory->createColumn(
                'status',
                'text',
                $datagrid,
                [
                    'label' => 'last_modified',
                    'field_mapping' => ['lastModified'],
                    'value_format' => function ($value) {
                        return $value === 1 ? 'active' : 'deactivated';
                    }
                ]
            )
        );
        $datagrid->addColumn($this->factory->createColumn('group', 'text', $datagrid, ['label' => 'group', 'field_mapping' => ['group']]));

        $datagrid->addColumn(
            $this->factory->createColumn(
                'actions',
                'compound_column',
                $datagrid,
                [
                    'label' => 'Actions',
                    'columns' => [
                        'modify' => $this->factory->createColumn(
                            'modify',
                            'action',
                            $datagrid,
                            [
                                'label' => 'Modify',
                                'field_mapping' => ['id' => 'id'],
                                'uri_scheme' => 'entity/{id}/modify',
                            ]
                        ),
                        'delete' => $this->factory->createColumn(
                            'delete',
                            'action',
                            $datagrid,
                            [
                                'label' => 'Delete',
                                'field_mapping' => ['id' => 'id'],
                                'uri_scheme' => 'entity/{id}/delete',
                            ]
                        ),
                    ]
                ]
            )
        );

        $data = [];

        for ($i = 0; $i < 100; ++$i) {
            $data[] = [
                'id' => $i,
                'name' => 'Who',
                'email' => 'me@example.com',
                'regdate' => new \DateTime(),
                'last_modified' => new \DateTime(),
                'status' => mt_rand(0, 1),
                'group' => 'Default'
            ];
        }

        $datagrid->setData($data);
        $this->assertInstanceOf(DatagridViewInterface::class, $datagrid->createView());
    }
}
