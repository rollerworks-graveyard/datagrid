<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Tests\Extension\Core\ColumnType;

class ActionTypeTest extends BaseTypeTest
{
    protected function getTestedType()
    {
        return 'action';
    }

    public function testPassLabelToView()
    {
        $column = $this->factory->createColumn('id', $this->getTestedType(), $this->datagrid, ['label' => 'My label', 'field_mapping' => ['key'], 'actions' => ['edit' => ['uri_scheme' => '/entity/%d/edit']]]);

        $object = new \stdClass();
        $object->key = ' foo ';
        $this->datagrid->setData([1 => $object]);

        $datagridView = $this->datagrid->createView();
        $view = $column->createHeaderView($datagridView);

        $this->assertEquals('My label', $view->label);
    }

    public function testActions()
    {
        $options = [
            'actions' => [
                'edit' => [
                    'uri_scheme' => '/entity/%d/edit',
                    'label' => 'edit',
                    'attr' => ['class' => 'i-edit'],
                ],
                'delete' => [
                    'uri_scheme' => '/entity/%d/delete',
                    'label' => 'Remove',
                ],
            ],
        ];

        $expected = [
            'edit' => [
                'url' => '/entity/42/edit',
                'label' => 'edit',
                'attr' => ['class' => 'i-edit'],
                'value' => [42],
            ],
            'delete' => [
                'url' => '/entity/42/delete',
                'label' => 'Remove',
                'attr' => [],
                'value' => [42],
            ],
        ];

        $this->assertCellValueEquals($expected, 42, $options);
    }

    public function testActionsWithUriAsClosure()
    {
        $options = [
            'actions' => [
                'edit' => [
                    'uri_scheme' => '/entity/%d/edit',
                    'label' => 'edit',
                ],
                'delete' => [
                    'uri_scheme' => function ($name, $label, $value) {
                        return '/entity/'.$value[0].'/delete?name='.$name.'&label='.$label;
                    },
                    'label' => 'Remove',
                ],
            ],
        ];

        $expected = [
            'edit' => [
                'url' => '/entity/42/edit',
                'label' => 'edit',
                'attr' => [],
                'value' => [42],
            ],
            'delete' => [
                'url' => '/entity/42/delete?name=delete&label=Remove',
                'label' => 'Remove',
                'attr' => [],
                'value' => [42],
            ],
        ];

        $this->assertCellValueEquals($expected, 42, $options);
    }

    public function testActionsWithLabelAsClosure()
    {
        $options = [
            'actions' => [
                'edit' => [
                    'uri_scheme' => '/entity/%d/edit',
                    'label' => 'edit',
                ],
                'delete' => [
                    'uri_scheme' => '/entity/%d/delete',
                    'label' => function ($name, $value) {
                        return ucfirst($name).' #'.$value[0];
                    },
                ],
            ],
        ];

        $expected = [
            'edit' => [
                'url' => '/entity/42/edit',
                'label' => 'edit',
                'attr' => [],
                'value' => [42],
            ],
            'delete' => [
                'url' => '/entity/42/delete',
                'label' => 'Delete #42',
                'attr' => [],
                'value' => [42],
            ],
        ];

        $this->assertCellValueEquals($expected, 42, $options);
    }

    public function testActionsWithRedirectUri()
    {
        $options = [
            'actions' => [
                'edit' => [
                    'uri_scheme' => '/entity/%d/edit',
                    'label' => 'edit',
                    'redirect_uri' => '/entity/list',
                ],
                'delete' => [
                    'uri_scheme' => '/entity/%d/delete?ask-confirm=true',
                    'label' => 'Remove',
                    'redirect_uri' => '/entity/list',
                ],
            ],
        ];

        $expected = [
            'edit' => [
                'url' => '/entity/42/edit?redirect_uri=%2Fentity%2Flist',
                'label' => 'edit',
                'attr' => [],
                'value' => [42],
            ],
            'delete' => [
                'url' => '/entity/42/delete?ask-confirm=true&redirect_uri=%2Fentity%2Flist',
                'label' => 'Remove',
                'attr' => [],
                'value' => [42],
            ],
        ];

        $this->assertCellValueEquals($expected, 42, $options);
    }

    public function testActionsWithRedirectUriAsClosure()
    {
        $options = [
            'actions' => [
                'edit' => [
                    'uri_scheme' => '/entity/%d/edit',
                    'label' => 'edit',
                    'redirect_uri' => function ($name, $label, $value) {
                        return '/entity/list/?value='.$value[0].'&name='.$name.'&label='.$label;
                    },
                ],
                'delete' => [
                    'uri_scheme' => '/entity/%d/delete',
                    'label' => 'Remove',
                ],
            ],
        ];

        $expected = [
            'edit' => [
                'url' => '/entity/42/edit?redirect_uri=%2Fentity%2Flist%2F%3Fvalue%3D42%26name%3Dedit%26label%3Dedit',
                'label' => 'edit',
                'attr' => [],
                'value' => [42],
            ],
            'delete' => [
                'url' => '/entity/42/delete',
                'label' => 'Remove',
                'attr' => [],
                'value' => [42],
            ],
        ];

        $this->assertCellValueEquals($expected, 42, $options);
    }

    public function testActionsWithMultipleFields()
    {
        $options = [
            'actions' => [
                'edit' => [
                    'uri_scheme' => '/entity/%d/edit?name=%s',
                    'label' => 'edit',
                ],
                'delete' => [
                    'uri_scheme' => '/entity/%d/delete?name=%s',
                    'label' => 'delete',
                ],
            ],
            'field_mapping' => ['id' => 'id', 'name' => 'name'],
        ];

        $expected = [
            'edit' => [
                'url' => '/entity/50/edit?name=sheldon',
                'value' => ['id' => 50, 'name' => 'sheldon'],
                'label' => 'edit',
                'attr' => [],
            ],
            'delete' => [
                'url' => '/entity/50/delete?name=sheldon',
                'value' => ['id' => 50, 'name' => 'sheldon'],
                'label' => 'delete',
                'attr' => [],
            ],
        ];

        $object = new \stdClass();
        $object->id = 50;
        $object->name = 'sheldon';
        $data = [1 => $object];

        $this->assertCellValueEquals($expected, $data, $options);
    }
}
