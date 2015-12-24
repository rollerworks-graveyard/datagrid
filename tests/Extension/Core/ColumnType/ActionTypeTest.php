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

use Rollerworks\Component\Datagrid\Extension\Core\ColumnType\ActionType;

class ActionTypeTest extends BaseTypeTest
{
    protected function getTestedType()
    {
        return ActionType::class;
    }

    public function testPassLabelToView()
    {
        $column = $this->factory->createColumn(
            'edit',
            $this->getTestedType(),
            $this->datagrid,
            [
                'content' => 'My label',
                'field_mapping' => ['key'],
                'uri_scheme' => '/entity/{key}/edit',
            ]
        );

        $object = new \stdClass();
        $object->key = ' foo ';
        $this->datagrid->setData([1 => $object]);

        $datagridView = $this->datagrid->createView();
        $view = $column->createHeaderView($datagridView);

        $this->assertEquals('My label', $view->label);
    }

    public function testActionWithAttr()
    {
        $options = [
            'uri_scheme' => '/entity/{key}/edit',
            'content' => 'edit',
            'attr' => ['class' => 'i-edit'],
            'url_attr' => ['data-new-window' => true],
        ];

        $expectedAttributesAttributes = [
            'url' => '/entity/42/edit',
            'content' => 'edit',
            'attr' => ['class' => 'i-edit'],
            'url_attr' => ['data-new-window' => true],
        ];

        $this->assertCellValueEquals(['key' => 42], 42, $options, $expectedAttributesAttributes);
    }

    public function testActionsWithUriAsClosure()
    {
        $options = [
            'content' => 'Delete',
            'uri_scheme' => function ($values) {
                return '/entity/'.$values['key'].'/delete';
            },
        ];

        $expectedAttributes = [
            'url' => '/entity/42/delete',
            'content' => 'Delete',
            'attr' => [],
            'url_attr' => [],
        ];

        $this->assertCellValueEquals(['key' => 42], 42, $options, $expectedAttributes);
    }

    public function testActionsWithContentAsClosure()
    {
        $options = [
            'uri_scheme' => '/entity/{key}/delete',
            'content' => function ($values) {
                return 'Delete #'.$values['key'];
            },
        ];

        $expectedAttributes = [
            'content' => 'Delete #42',
            'url' => '/entity/42/delete',
            'attr' => [],
            'url_attr' => [],
        ];

        $this->assertCellValueEquals(['key' => 42], 42, $options, $expectedAttributes);
    }

    public function testActionsWithRedirectUri()
    {
        $options = [
            'uri_scheme' => '/entity/{key}/edit',
            'content' => 'edit',
            'redirect_uri' => '/entity/list',
        ];

        $expectedAttributes = [
            'url' => '/entity/42/edit?redirect_uri=%2Fentity%2Flist',
            'content' => 'edit',
            'attr' => [],
            'url_attr' => [],
        ];

        $this->assertCellValueEquals(['key' => 42], 42, $options, $expectedAttributes);
    }

    public function testActionsWithRedirectUriWithExistingQueryStringInUrl()
    {
        $options = [
            'uri_scheme' => '/entity/{key}/edit?foo=bar',
            'content' => 'delete',
            'redirect_uri' => '/entity/list?filter=something',
        ];

        $expectedAttributes = [
            'url' => '/entity/42/edit?foo=bar&redirect_uri=%2Fentity%2Flist%3Ffilter%3Dsomething',
            'content' => 'delete',
            'attr' => [],
            'url_attr' => [],
        ];

        $this->assertCellValueEquals(['key' => 42], 42, $options, $expectedAttributes);
    }

    public function testActionsWithRedirectUriAsClosure()
    {
        $options = [
            'uri_scheme' => '/entity/{key}/edit',
            'content' => 'edit',
            'redirect_uri' => function ($values) {
                return '/entity/list/?last-entity='.$values['key'];
            },
        ];

        $expectedAttributes = [
            'url' => '/entity/42/edit?redirect_uri=%2Fentity%2Flist%2F%3Flast-entity%3D42',
            'content' => 'edit',
            'attr' => [],
            'url_attr' => [],
        ];

        $this->assertCellValueEquals(['key' => 42], 42, $options, $expectedAttributes);
    }

    public function testActionsWithMultipleFields()
    {
        $options = [
            'uri_scheme' => '/entity/{id}/edit?name={username}',
            'content' => 'edit',
            'field_mapping' => ['id' => 'id', 'username' => 'name'],
        ];

        $expectedAttributes = [
            'url' => '/entity/50/edit?name=sheldon',
            'content' => 'edit',
            'attr' => [],
            'url_attr' => [],
        ];

        $object = new \stdClass();
        $object->id = 50;
        $object->name = 'sheldon';

        $data = [1 => $object];

        $this->assertCellValueEquals(['id' => 50, 'username' => 'sheldon'], $data, $options, $expectedAttributes);
    }
}
