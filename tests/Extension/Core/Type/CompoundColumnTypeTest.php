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

use Rollerworks\Component\Datagrid\Column\CellView;
use Rollerworks\Component\Datagrid\Column\CompoundColumn;
use Rollerworks\Component\Datagrid\Datagrid;
use Rollerworks\Component\Datagrid\Extension\Core\Type\CompoundColumnType;
use Rollerworks\Component\Datagrid\Extension\Core\Type\NumberType;
use Rollerworks\Component\Datagrid\Extension\Core\Type\TextType;

class CompoundColumnTypeTest extends BaseTypeTest
{
    protected function getTestedType(): string
    {
        return CompoundColumnType::class;
    }

    public function testPassLabelToView()
    {
        /** @var CompoundColumn $rootColumn */
        $rootColumn = $this->factory->createColumn('ids', $this->getTestedType(), ['label' => 'Ids']);
        $column = $this->factory->createColumn(
            'key',
            TextType::class,
            ['label' => 'My label', 'parent_column' => $rootColumn]
        );

        $rootColumn->setColumns(['id' => $column]);
        $datagrid = new Datagrid('my_grid', [$rootColumn]);

        $object = new \stdClass();
        $object->key = ' foo ';
        $datagrid->setData([1 => $object]);

        $datagridView = $datagrid->createView();
        $view = $rootColumn->createHeaderView($datagridView);

        $this->assertSame('Ids', $view->label);
        self::assertViewVarsEquals(
            [
                'label_attr' => [],
                'header_attr' => [],
                'cell_attr' => [],
                'label_translation_domain' => null,
                'unique_block_prefix' => '_my_grid_ids',
                'block_prefixes' => ['compound_column', '_my_grid_ids'],
            ],
            $view
        );
    }

    public function testSubCellsToView()
    {
        /** @var CompoundColumn $column */
        $column = $this->factory->createColumn('actions', $this->getTestedType(), ['label' => 'Actions']);

        $columns = [];
        $columns['age'] = $this->factory->createColumn('age', NumberType::class, ['parent_column' => $column]);
        $columns['name'] = $this->factory->createColumn('name', TextType::class, ['parent_column' => $column, 'block_name' => 'my_named']);

        $column->setColumns($columns);

        $datagrid = new Datagrid('grid', [$column]);

        $object = new \stdClass();
        $object->key = ' foo ';
        $object->name = ' sheldon ';
        $object->age = 42;
        $datagrid->setData([1 => $object]);

        $datagridView = $datagrid->createView();

        $view = $column->createCellView($datagridView->columns['actions'], $object, 0);

        $this->assertDatagridCell('age', $view);
        $this->assertDatagridCell('name', $view);

        $this->assertEquals('42', $view->value['age']->value);
        $this->assertEquals(' sheldon ', $view->value['name']->value);
        $this->assertArrayNotHasKey('key', $view->value);

        $headerView = $columns['age']->createHeaderView($datagridView);

        $this->assertEquals('_grid_actions_age', $headerView->vars['unique_block_prefix']);
        $this->assertEquals(['column', 'number', '_grid_actions_age'], $headerView->vars['block_prefixes']);

        $headerView = $columns['name']->createHeaderView($datagridView);

        $this->assertEquals('_my_named', $headerView->vars['unique_block_prefix']);
        $this->assertEquals(['column', 'text', '_my_named'], $headerView->vars['block_prefixes']);
    }

    private function assertDatagridCell($name, CellView $view)
    {
        $this->assertInternalType('array', $view->value);
        $this->assertArrayHasKey($name, $view->value);
        $this->assertInstanceOf(CellView::class, $view->value[$name]);
    }
}
