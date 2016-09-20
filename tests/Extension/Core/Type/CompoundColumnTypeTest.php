<?php declare(strict_types=1);

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
use Rollerworks\Component\Datagrid\DatagridView;
use Rollerworks\Component\Datagrid\Exception\UnexpectedTypeException;
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
        $column = $this->factory->createColumn('id', $this->getTestedType(), ['label' => 'My label', 'columns' => []]);
        $datagrid = $this->factory->createDatagrid('grid', [$column]);

        $object = new \stdClass();
        $object->key = ' foo ';
        $datagrid->setData([1 => $object]);

        $datagridView = $datagrid->createView();
        $view = $column->createHeaderView($datagridView);

        $this->assertSame('My label', $view->label);
    }

    public function testSubCellsToView()
    {
        $columns = [];
        $columns['age'] = $this->factory->createColumn('age', NumberType::class);
        $columns['name'] = $this->factory->createColumn('name', TextType::class);

        $column = $this->factory->createColumn(
            'actions',
            $this->getTestedType(),
            ['columns' => $columns]
        );

        $datagrid = $this->factory->createDatagrid('grid', [$column]);

        $object = new \stdClass();
        $object->key = ' foo ';
        $object->name = ' sheldon ';
        $object->age = 42;
        $datagrid->setData([1 => $object]);

        $datagridView = $datagrid->createView();

        $view = $column->createCellView($datagridView, $object, 0);

        $this->assertDatagridCell('age', $view);
        $this->assertDatagridCell('name', $view);

        $this->assertEquals('42', $view->value['age']->value);
        $this->assertEquals(' sheldon ', $view->value['name']->value);
        $this->assertArrayNotHasKey('key', $view->value);
    }

    private function assertDatagridCell($name, CellView $view)
    {
        $this->assertInternalType('array', $view->value);
        $this->assertArrayHasKey($name, $view->value);
        $this->assertInstanceOf(CellView::class, $view->value[$name]);
    }

    public function testInvalidColumnGivesException()
    {
        $options = [
            'label' => 'Birthday',
        ];

        $object = new \stdClass();
        $object->key = ' foo ';
        $object->name = ' sheldon ';
        $object->age = 42;

        $columns = [];
        $columns['age'] = $this->factory->createColumn('age', NumberType::class);
        $columns['foo'] = false;
        $options['columns'] = $columns;

        $datagridView = $this->getMockBuilder(DatagridView::class)->disableOriginalConstructor()->getMock();

        $this->setExpectedException(
             UnexpectedTypeException::class,
             'Expected argument of type "Rollerworks\Component\Datagrid\Column\ColumnInterface", "boolean" given'
        );

        $this->factory->createColumn('birthday', CompoundColumnType::class, $options)->createCellView(
            $datagridView,
            $object,
            0
        );
    }
}
