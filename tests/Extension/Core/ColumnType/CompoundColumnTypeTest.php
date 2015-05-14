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

class CompoundColumnTypeTest extends BaseTypeTest
{
    protected function getTestedType()
    {
        return 'compound_column';
    }

    public function testPassLabelToView()
    {
        $column = $this->factory->createColumn('id', $this->getTestedType(), $this->datagrid, ['label' => 'My label', 'field_mapping' => ['key'], 'columns' => []]);

        $object = new \stdClass();
        $object->key = ' foo ';
        $this->datagrid->setData([1 => $object]);

        $datagridView = $this->datagrid->createView();
        $view = $column->createHeaderView($datagridView);

        $this->assertSame('My label', $view->label);
    }

    public function testWithAutoMapping()
    {
        \Locale::setDefault('en');

        $options = [
            'label' => 'Birthday',
            'value_glue' => null,
        ];

        $columns = [];
        $columns[] = $this->factory->createColumn('age', 'number', $this->datagrid, ['label' => 'My label', 'field_mapping' => ['age']]);
        $columns[] = $this->factory->createColumn('date', 'datetime', $this->datagrid, ['label' => 'My label', 'field_mapping' => ['birthDate'], 'time_format' => \IntlDateFormatter::NONE]);
        $options['columns'] = $columns;

        $column = $this->factory->createColumn('birthday', 'compound_column', $this->datagrid, $options);

        $object = new \stdClass();
        $object->age = 6;
        $object->birthDate = new \DateTime('2007-10-02', new \DateTimeZone('Europe/Amsterdam'));
        $data = [1 => $object];

        $this->datagrid->setData($data);
        $datagridView = $this->datagrid->createView();

        $view = $column->createCellView($datagridView, $data[1], 1);
        $this->assertEquals(['6', 'Oct 1, 2007'], $view->value);
    }

    public function testWithAutoMappingAndFormatter()
    {
        \Locale::setDefault('en');

        $options = [
            'label' => 'Birthday',
            'value_format' => '%s',
            'value_glue' => ' / ',
        ];

        $columns = [];
        $columns[] = $this->factory->createColumn('age', 'number', $this->datagrid, ['label' => 'My label', 'field_mapping' => ['age']]);
        $columns[] = $this->factory->createColumn('date', 'datetime', $this->datagrid, ['label' => 'My label', 'field_mapping' => ['birthDate'], 'time_format' => \IntlDateFormatter::NONE]);
        $options['columns'] = $columns;

        $column = $this->factory->createColumn('birthday', 'compound_column', $this->datagrid, $options);

        $object = new \stdClass();
        $object->age = 6;
        $object->birthDate = new \DateTime('2007-10-02', new \DateTimeZone('Europe/Amsterdam'));
        $data = [1 => $object];

        $this->datagrid->setData($data);
        $datagridView = $this->datagrid->createView();

        $view = $column->createCellView($datagridView, $data[1], 1);
        $this->assertEquals('6 / Oct 1, 2007', $view->value);
    }

    public function testInvalidColumnGivesException()
    {
        \Locale::setDefault('en');

        $options = [
            'label' => 'Birthday',
            'value_glue' => null,
            'field_mapping' => ['age'],
        ];

        $columns = [];
        $columns[] = $this->factory->createColumn('age', 'number', $this->datagrid, ['label' => 'My label', 'field_mapping' => ['age']]);
        $columns[] = false;
        $options['columns'] = $columns;

        $this->setExpectedException(
             'Rollerworks\Component\Datagrid\Exception\UnexpectedTypeException',
             'Expected argument of type "Rollerworks\Component\Datagrid\Column\ColumnInterface", "boolean" given'
        );

        $this->factory->createColumn('birthday', 'compound_column', $this->datagrid, $options);
    }

    public function testWithAutoMappingInvalidColumnGivesException()
    {
        \Locale::setDefault('en');

        $options = [
            'label' => 'Birthday',
            'value_glue' => null,
        ];

        $columns = [];
        $columns[] = $this->factory->createColumn('age', 'number', $this->datagrid, ['label' => 'My label', 'field_mapping' => ['age']]);
        $columns[] = false;
        $options['columns'] = $columns;

        $this->setExpectedException(
             'Rollerworks\Component\Datagrid\Exception\UnexpectedTypeException',
             'Expected argument of type "Rollerworks\Component\Datagrid\Column\ColumnInterface", "boolean" given'
        );

        $this->factory->createColumn('birthday', 'compound_column', $this->datagrid, $options);
    }
}
