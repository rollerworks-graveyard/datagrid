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
use Rollerworks\Component\Datagrid\Exception\InvalidConfigurationException;
use Rollerworks\Component\Datagrid\Extension\Core\Type\ActionType;

class ActionTypeTest extends BaseTypeTest
{
    protected function getTestedType(): string
    {
        return ActionType::class;
    }

    public function testPassLabelToView()
    {
        $column = $this->factory->createColumn(
            'edit',
            $this->getTestedType(),
            [
                'content' => 'My label',
                'data_provider' => function ($data) {
                    return ['key' => $data->key];
                },
                'uri_scheme' => '/entity/{key}/edit',
            ]
        );

        $datagrid = new Datagrid('grid', [$column]);

        $object = new \stdClass();
        $object->key = ' foo ';
        $datagrid->setData([1 => $object]);

        $datagridView = $datagrid->createView();
        $view = $column->createHeaderView($datagridView);

        $this->assertEquals('My label', $view->label);
    }

    /** @test */
    public function it_renders_with_an_url()
    {
        $options = [
            'url' => '/entity/1/edit',
            'content' => 'edit',
            'data_provider' => function ($data) {
                return ['key' => $data->key];
            },
            'attr' => ['class' => 'i-edit'],
            'url_attr' => ['data-new-window' => true],
        ];

        $expectedAttributesAttributes = [
            'url' => '/entity/1/edit',
            'content' => 'edit',
            'attr' => ['class' => 'i-edit'],
            'url_attr' => ['data-new-window' => true],
        ];

        $this->assertCellValueEquals(['key' => 42], 42, $options, $expectedAttributesAttributes);
    }

    /** @test */
    public function it_resolves_an_uri_scheme()
    {
        $options = [
            'uri_scheme' => '/entity/{key}/edit',
            'content' => 'edit',
            'data_provider' => function ($data) {
                return ['key' => $data->key];
            },
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

    /** @test */
    public function it_evaluates_uri_scheme_as_closure()
    {
        $options = [
            'content' => 'Delete',
            'data_provider' => function ($data) {
                return ['key' => $data->key];
            },
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

    /** @test */
    public function it_evaluates_content_as_closure()
    {
        $options = [
            'uri_scheme' => '/entity/{key}/delete',
            'data_provider' => function ($data) {
                return ['key' => $data->key];
            },
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

    /** @test */
    public function it_adds_redirect_to_url()
    {
        $options = [
            'uri_scheme' => '/entity/{key}/edit',
            'content' => 'edit',
            'data_provider' => function ($data) {
                return ['key' => $data->key];
            },
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

    /** @test */
    public function it_appends_redirect_to_url_with_existing_query_string()
    {
        $options = [
            'uri_scheme' => '/entity/{key}/edit?foo=bar',
            'content' => 'delete',
            'data_provider' => function ($data) {
                return ['key' => $data->key];
            },
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

    /** @test */
    public function it_evaluates_redirect_as_closure()
    {
        $options = [
            'uri_scheme' => '/entity/{key}/edit',
            'content' => 'edit',
            'data_provider' => function ($data) {
                return ['key' => $data->key];
            },
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

    /** @test */
    public function it_passes_all_provided_values()
    {
        $options = [
            'uri_scheme' => '/entity/{id}/edit?name={username}',
            'content' => 'edit',
            'data_provider' => function ($data) {
                return ['id' => $data->id, 'username' => $data->name];
            },
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

    /** @test */
    public function url_or_uri_schema_must_be_provided()
    {
        $column = $this->factory->createColumn(
            'id',
            $this->getTestedType(),
            [
                'url' => null,
                'uri_scheme' => null,
                'content' => 'edit',
                'label' => 'My label',
                'data_provider' => function ($data) {
                    return ['id' => $data->id, 'username' => $data->name];
                },
            ]
        );

        $datagrid = new Datagrid('grid', [$column]);

        $object = new \stdClass();
        $object->id = 50;
        $object->name = 'sheldon';
        $data = [1 => $object];

        $datagrid->setData($data);

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Action needs an "url" or "uri_scheme" but none is provided.');

        $datagrid->createView();
    }
}
