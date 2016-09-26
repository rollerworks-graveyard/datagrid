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

use Rollerworks\Component\Datagrid\DatagridView;
use Rollerworks\Component\Datagrid\Extension\Core\Type\ActionType;
use Rollerworks\Component\Datagrid\Extension\Core\Type\DateTimeType;
use Rollerworks\Component\Datagrid\Extension\Core\Type\NumberType;
use Rollerworks\Component\Datagrid\Extension\Core\Type\TextType;
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

        $datagrid = $this->factory->createDatagridBuilder();

        $datagrid->add('id', NumberType::class, ['data_provider' => function ($data) {
            return $data['id'];
        }]);
        $datagrid->add('name', TextType::class, ['data_provider' => function ($data) {
            return $data['name'];
        }]);
        $datagrid->add('email', TextType::class, ['data_provider' => function ($data) {
            return $data['email'];
        }]);
        $datagrid->add('regdate', DateTimeType::class, ['data_provider' => function ($data) {
            return $data['regdate'];
        }]);
        $datagrid->add('lastModified', DateTimeType::class, ['data_provider' => function ($data) {
            return $data['lastModified'];
        }]);
        $datagrid->add(
            'status',
            TextType::class,
            [
                'label' => 'last_modified',
                'data_provider' => function ($data) {
                    return $data['lastModified'];
                },
                'value_format' => function ($value) {
                    return $value === 1 ? 'active' : 'deactivated';
                },
            ]
        );
        $datagrid->add('group', TextType::class);

        $datagrid
            ->createCompound(
                    'actions',
                    [
                        'label' => 'Actions',
                        'data_provider' => function ($data) {
                            return ['id' => $data['id']];
                        },
                    ]
                )
                ->add(
                    'modify',
                    ActionType::class,
                    [
                        'label' => 'Modify',
                        'uri_scheme' => 'entity/{id}/modify',
                    ]
                )
                ->add(
                    'delete',
                    ActionType::class,
                    [
                        'label' => 'Delete',
                        'data_provider' => function ($data) {
                            return ['id' => $data['id']];
                        },
                        'uri_scheme' => 'entity/{id}/delete',
                    ]
                )
            ->end()
        ;

        $data = [];

        for ($i = 0; $i < 100; ++$i) {
            $data[] = [
                'id' => $i,
                'name' => 'Who',
                'email' => 'me@example.com',
                'regdate' => new \DateTime(),
                'lastModified' => new \DateTime(),
                'status' => mt_rand(0, 1),
                'group' => 'Default',
            ];
        }

        $datagrid = $datagrid->getDatagrid('test');

        $datagrid->setData($data);
        $this->assertInstanceOf(DatagridView::class, $datagrid->createView());
    }

    /**
     * This test case is realistic in collection rows where each
     * row contains the same data.
     *
     * Columns data-provider is automatically configured.
     *
     * This is most common use-case, showing more rows
     * on the page is likely to hit memory limits (for the data set itself).
     * And more rows will give display problems.
     *
     * @group benchmark
     */
    public function testGenerateViewWith100RowsAnd10ColumnsAutoDataProvider()
    {
        $this->setMaxRunningTime(1);

        $datagrid = $this->factory->createDatagridBuilder();

        $datagrid->add('id', NumberType::class);
        $datagrid->add('name', TextType::class);
        $datagrid->add('email', TextType::class);
        $datagrid->add('regdate', DateTimeType::class);
        $datagrid->add('lastModified', DateTimeType::class);
        $datagrid->add(
            'status',
            TextType::class,
            [
                'value_format' => function ($value) {
                    return $value === 1 ? 'active' : 'deactivated';
                },
            ]
        );
        $datagrid->add('group', TextType::class);

        $datagrid
            ->createCompound(
                    'actions',
                    [
                        'label' => 'Actions',
                        'data_provider' => function ($data) {
                            return ['id' => $data['id']];
                        },
                    ]
                )
                ->add(
                    'modify',
                    ActionType::class,
                    [
                        'label' => 'Modify',
                        'uri_scheme' => 'entity/{id}/modify',
                    ]
                )
                ->add(
                    'delete',
                    ActionType::class,
                    [
                        'label' => 'Delete',
                        'data_provider' => function ($data) {
                            return ['id' => $data['id']];
                        },
                        'uri_scheme' => 'entity/{id}/delete',
                    ]
                )
            ->end()
        ;

        $data = [];

        for ($i = 0; $i < 100; ++$i) {
            $data[] = [
                'id' => $i,
                'name' => 'Who',
                'email' => 'me@example.com',
                'regdate' => new \DateTime(),
                'lastModified' => new \DateTime(),
                'status' => mt_rand(0, 1),
                'group' => 'Default',
            ];
        }

        $datagrid = $datagrid->getDatagrid('test');

        $datagrid->setData($data);
        $this->assertInstanceOf(DatagridView::class, $datagrid->createView());
    }
}
