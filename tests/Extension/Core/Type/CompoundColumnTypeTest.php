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

        // Comparing this is to much work at this stage.
        unset($view->vars['_sub_headers']);

        self::assertSame('Ids', $view->label);
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

        $row = $datagridView->rows[1]->cells;

        // Actions (root) column
        $view = $row['actions'];

        self::assertDatagridCompoundCell('age', ['unique_block_prefix' => '_grid_actions', 'row' => 1], $view);
        self::assertDatagridCompoundCell('name', ['unique_block_prefix' => '_grid_actions', 'row' => 1], $view);

        self::assertArrayHasKey('age', $view->value);
        self::assertArrayHasKey('name', $view->value);
        self::assertArrayNotHasKey('key', $view->value);

        // Internal cells
        $cells = $view->value;

        self::assertDatagridCell(
            'age',
            '42',
            [
                'unique_block_prefix' => '_grid_actions_age',
                'block_prefixes' => ['column', 'number', '_grid_actions_age'],
                'cache_key' => '_grid_actions_age_number',
                'row' => 1,
                'compound' => true,
            ],
            $cells['age']
        );

        self::assertDatagridCell(
            'name',
            ' sheldon ',
            [
                'unique_block_prefix' => '_my_named',
                'block_prefixes' => ['column', 'text', '_my_named'],
                'cache_key' => '_my_named_text',
                'row' => 1,
                'compound' => true,
            ],
            $cells['name']
        );
    }

    private static function assertDatagridCell($name, $value, array $vars, CellView $view)
    {
        self::assertEquals($name, $view->name);
        self::assertEquals($value, $view->value);

        if ([] !== $vars) {
            self::assertViewVarsEquals($vars, $view);
        }
    }

    private function assertDatagridCompoundCell($name, array $vars, CellView $view)
    {
        self::assertInternalType('array', $view->value);
        self::assertArrayHasKey($name, $view->value);
        self::assertInstanceOf(CellView::class, $view->value[$name]);

        if ([] !== $vars) {
            self::assertViewVarsEquals($vars, $view);
        }
    }
}
