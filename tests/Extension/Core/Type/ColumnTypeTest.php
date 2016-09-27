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
use Rollerworks\Component\Datagrid\Exception\DataProviderException;
use Rollerworks\Component\Datagrid\Extension\Core\Type\ColumnType;
use Symfony\Component\PropertyAccess\PropertyPath;

class ColumnTypeTest extends BaseTypeTest
{
    public function testPassLabelAndOtherToView()
    {
        $column = $this->factory->createColumn(
            'id',
            $this->getTestedType(),
            [
                'label' => 'My label',
                'label_attr' => ['class' => 'info'],
                'header_attr' => ['class' => 'striped'],
                'cell_attr' => ['class' => 'striped'],
                'label_translation_domain' => 'messages',
                'data_provider' => function ($data) {
                    return $data->key;
                },
            ]
        );

        $datagrid = new Datagrid('my_grid', [$column]);

        $object = new \stdClass();
        $object->key = new \DateTime();

        $datagrid->setData([1 => $object]);

        $view = $datagrid->createView();
        $view = $column->createHeaderView($view);

        $this->assertSame('My label', $view->label);
        $this->assertEquals(
            [
                'label_attr' => [
                    'class' => 'info',
                ],
                'header_attr' => [
                    'class' => 'striped',
                ],
                'cell_attr' => [
                    'class' => 'striped',
                ],
                'label_translation_domain' => 'messages',
                'unique_block_prefix' => '_my_grid_id',
                'block_prefixes' => ['column', '_my_grid_id'],
            ],
            $view->attributes
        );
    }

    public function testCustomBlockName()
    {
        $column = $this->factory->createColumn(
            'id',
            $this->getTestedType(),
            [
                'label' => 'My label',
                'block_name' => 'my_crazy_column',
                'data_provider' => function ($data) {
                    return $data->key;
                },
            ]
        );

        $datagrid = new Datagrid('my_grid', [$column]);

        $object = new \stdClass();
        $object->key = new \DateTime();

        $datagrid->setData([1 => $object]);

        $view = $datagrid->createView();
        $view = $column->createHeaderView($view);

        $this->assertSame('My label', $view->label);
        $this->assertEquals(
            [
                'label_attr' => [],
                'header_attr' => [],
                'cell_attr' => [],
                'label_translation_domain' => null,
                'unique_block_prefix' => '_my_crazy_column',
                'block_prefixes' => ['column', '_my_crazy_column'],
            ],
            $view->attributes
        );
    }

    public function testAutoConfigurationDataProvider()
    {
        $object = new \stdClass();
        $object->key = 'foo';
        $object->key2 = 'bar';
        $data = [1 => $object];

        $column1 = $this->factory->createColumn('key', ColumnType::class);
        $column2 = $this->factory->createColumn('key2', ColumnType::class);

        $datagrid = new Datagrid('grid', [$column1, $column2]);

        $datagrid->setData($data);
        $datagridView = $datagrid->createView();

        $view1 = $column1->createCellView($datagridView, $object, 1);
        $view2 = $column2->createCellView($datagridView, $object, 1);

        $this->assertEquals($object->key, $view1->value);
        $this->assertEquals($object->key2, $view2->value);
    }

    /** @test */
    public function it_converts_a_string_to_a_dataProvider_closure()
    {
        $object = new \stdClass();
        $object->key = 'foo';
        $object->key2 = 'bar';
        $data = [1 => $object];

        $column1 = $this->factory->createColumn('key', ColumnType::class);
        $column2 = $this->factory->createColumn('foo', ColumnType::class, ['data_provider' => 'key2']);

        $datagrid = new Datagrid('grid', [$column1, $column2]);

        $datagrid->setData($data);
        $datagridView = $datagrid->createView();

        $view1 = $column1->createCellView($datagridView, $object, 1);
        $view2 = $column2->createCellView($datagridView, $object, 1);

        $this->assertEquals($object->key, $view1->value);
        $this->assertEquals($object->key2, $view2->value);
    }

    /** @test */
    public function it_converts_a_propertyPath_to_a_dataProvider_closure()
    {
        $object = new \stdClass();
        $object->key = 'foo';
        $object->key2 = 'bar';
        $data = [1 => $object];

        $column1 = $this->factory->createColumn('key', ColumnType::class);
        $column2 = $this->factory->createColumn(
            'foo',
            ColumnType::class,
            ['data_provider' => new PropertyPath('key2')]
        );

        $datagrid = new Datagrid('grid', [$column1, $column2]);

        $datagrid->setData($data);
        $datagridView = $datagrid->createView();

        $view1 = $column1->createCellView($datagridView, $object, 1);
        $view2 = $column2->createCellView($datagridView, $object, 1);

        $this->assertEquals($object->key, $view1->value);
        $this->assertEquals($object->key2, $view2->value);
    }

    public function testAutoConfigurationDataProviderFailsForUnsupportedPath()
    {
        $object = new \stdClass();
        $object->key = 'foo';
        $object->key2 = 'bar';
        $data = [1 => $object];

        $column = $this->factory->createColumn('key3', ColumnType::class);

        $datagrid = new Datagrid('grid', [$column]);
        $datagrid->setData($data);

        $this->expectException(DataProviderException::class);
        $this->expectExceptionMessage('Unable to get value for column "key3".');

        $datagrid->createView();
    }

    public function testStringDataProviderFailsForUnsupportedPath()
    {
        $object = new \stdClass();
        $object->key = 'foo';
        $object->key2 = 'bar';
        $data = [1 => $object];

        $column = $this->factory->createColumn('key2', ColumnType::class, ['data_provider' => 'key3']);

        $datagrid = new Datagrid('grid', [$column]);
        $datagrid->setData($data);

        $this->expectException(DataProviderException::class);
        $this->expectExceptionMessage('Unable to get value for column "key2" with property-path "key3".');

        $datagrid->createView();
    }

    public function testInvalidPropertyPathProviderFailsWithHelpfulMessage()
    {
        $object = new \stdClass();
        $object->key = 'foo';
        $object->key2 = 'bar';
        $data = [1 => $object];

        $column = $this->factory->createColumn('key2', ColumnType::class, ['data_provider' => '][key3']);

        $datagrid = new Datagrid('grid', [$column]);
        $datagrid->setData($data);

        $this->expectException(DataProviderException::class);
        $this->expectExceptionMessage(
            'Invalid property-path for column "key2" with message: Could not parse property path "][key3"'
        );

        $datagrid->createView();
    }

    protected function getTestedType(): string
    {
        return ColumnType::class;
    }
}
