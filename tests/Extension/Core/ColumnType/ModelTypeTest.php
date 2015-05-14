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

class ModelTypeTest extends BaseTypeTest
{
    public function testPassLabelToView()
    {
        $column = $this->factory->createColumn('id', $this->getTestedType(), $this->datagrid, ['label' => 'My label', 'field_mapping' => ['key'], 'model_fields' => []]);

        $object = new \stdClass();
        $object->key = ' foo ';
        $this->datagrid->setData([1 => $object]);

        $datagridView = $this->datagrid->createView();
        $view = $column->createHeaderView($datagridView);

        $this->assertSame('My label', $view->label);
    }

    public function testValueOfModelWithModelFormat()
    {
        $options = [
            'field_mapping' => ['key2'],
            'model_fields' => ['firstName', 'lastName'],
            'model_value_format' => '%s - %s',
        ];

        $childObject = new \stdClass();
        $childObject->firstName = 'Sheldon';
        $childObject->lastName = 'Cooper';

        $object = new \stdClass();
        $object->id = 849;
        $object->key2 = $childObject;
        $data = [1 => $object];

        $this->assertCellValueEquals(['Sheldon - Cooper'], $data, $options);
    }

    public function testValueOfModelWithModelFormatAndFormat()
    {
        $options = [
            'field_mapping' => ['key2'],
            'model_fields' => ['firstName', 'lastName'],
            'model_value_format' => '%s - %s',
            'value_format' => '%s',
        ];

        $childObject = new \stdClass();
        $childObject->firstName = 'Sheldon';
        $childObject->lastName = 'Cooper';

        $object = new \stdClass();
        $object->id = 849;
        $object->key2 = $childObject;
        $data = [1 => $object];

        $this->assertCellValueEquals('Sheldon - Cooper', $data, $options);
    }

    public function testValueOfListModelWithFormat()
    {
        $options = [
            'field_mapping' => ['key2'],
            'model_fields' => ['firstName', 'lastName'],
            'model_value_format' => '%s - %s',
            'value_format' => '%s, %s',
        ];

        $childObject = new \stdClass();
        $childObject->firstName = 'Sheldon';
        $childObject->lastName = 'Cooper';

        $childObject2 = new \stdClass();
        $childObject2->firstName = 'Doctor';
        $childObject2->lastName = 'Who';

        $object = new \stdClass();
        $object->id = 849;
        $object->key2 = [$childObject, $childObject2];
        $data = [1 => $object];

        $this->assertCellValueEquals('Sheldon - Cooper, Doctor - Who', $data, $options);
    }

    public function testValueOfListModelWithGlue()
    {
        $options = [
            'field_mapping' => ['key2'],
            'model_fields' => ['firstName', 'lastName'],
            'model_value_format' => '%s - %s',
            'value_glue' => ', ',
        ];

        $childObject = new \stdClass();
        $childObject->firstName = 'Sheldon';
        $childObject->lastName = 'Cooper';

        $childObject2 = new \stdClass();
        $childObject2->firstName = 'Doctor';
        $childObject2->lastName = 'Who';

        $object = new \stdClass();
        $object->id = 849;
        $object->key2 = [$childObject, $childObject2];
        $data = [1 => $object];

        $this->assertCellValueEquals('Sheldon - Cooper, Doctor - Who', $data, $options);
    }

    protected function getTestedType()
    {
        return 'model';
    }
}
