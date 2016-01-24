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

use Rollerworks\Component\Datagrid\DatagridViewInterface;
use Rollerworks\Component\Datagrid\Extension\Core\ColumnType\DateTimeType;
use Rollerworks\Component\Datagrid\Extension\Core\ColumnType\NumberType;
use Rollerworks\Component\Datagrid\Extension\Core\ColumnType\TextType;
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

        $datagrid->addColumn($this->factory->createColumn('id', NumberType::class, $datagrid, ['label' => '#', 'data_provider' => function ($data) { return $data['id']; }]));
        $datagrid->addColumn($this->factory->createColumn('name', TextType::class, $datagrid, ['label' => 'Name', 'data_provider' => function ($data) { return $data['name']; }]));
        $datagrid->addColumn($this->factory->createColumn('email', TextType::class, $datagrid, ['label' => 'Email', 'data_provider' => function ($data) { return $data['email']; }]));
        $datagrid->addColumn($this->factory->createColumn('regdate', DateTimeType::class, $datagrid, ['label' => 'regdate', 'data_provider' => function ($data) { return $data['regdate']; }]));
        $datagrid->addColumn($this->factory->createColumn('last_modified', DateTimeType::class, $datagrid, ['label' => 'last_modified', 'data_provider' => function ($data) { return $data['lastModified']; }]));
        $datagrid->addColumn(
            $this->factory->createColumn(
                'status',
                TextType::class,
                $datagrid,
                [
                    'label' => 'last_modified',
                    'data_provider' => function ($data) { return $data['lastModified']; },
                    'value_format' => function ($value) {
                        return $value === 1 ? 'active' : 'deactivated';
                    },
                ]
            )
        );
        $datagrid->addColumn($this->factory->createColumn('group', TextType::class, $datagrid, ['label' => 'group', 'data_provider' => function ($data) { return $data['group']; }]));

        $datagrid->addColumn(
            $this->factory->createColumn(
                'actions',
                'compound_column',
                $datagrid,
                [
                    'data_provider' => function ($data) { return $data; },
                    'label' => 'Actions',
                    'columns' => [
                        'modify' => $this->factory->createColumn(
                            'modify',
                            'action',
                            $datagrid,
                            [
                                'label' => 'Modify',
                                'data_provider' => function ($data) { return $data['id']; },
                                'uri_scheme' => 'entity/{id}/modify',
                            ]
                        ),
                        'delete' => $this->factory->createColumn(
                            'delete',
                            'action',
                            $datagrid,
                            [
                                'label' => 'Delete',
                                'data_provider' => function ($data) { return $data['id']; },
                                'uri_scheme' => 'entity/{id}/delete',
                            ]
                        ),
                    ],
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
                'group' => 'Default',
            ];
        }

        $datagrid->setData($data);
        $this->assertInstanceOf(DatagridViewInterface::class, $datagrid->createView());
    }
}
