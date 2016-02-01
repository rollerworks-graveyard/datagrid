<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Tests\Extension\Core\Type;

use Rollerworks\Component\Datagrid\Extension\Core\Type\BooleanType;

class BooleanTest extends BaseTypeTest
{
    protected function getTestedType()
    {
        return BooleanType::class;
    }

    public function testBasicValue()
    {
        $options = [
            'true_value' => 'true',
            'false_value' => 'false',
            'data_provider' => function ($data) { return (array) $data; },
        ];

        $this->assertCellValueEquals('true', true, $options);
        $this->assertCellValueEquals('false', false, $options);
    }

    public function testValueWithTrueValuesInArray()
    {
        $options = [
            'true_value' => 'true',
            'false_value' => 'false',
            'data_provider' => function ($data) { return (array) $data; },
        ];

        $object = new \stdClass();
        $object->key = true;
        $object->key2 = true;
        $data = [1 => $object];

        $this->assertCellValueEquals('true', $data, $options);
    }

    public function testValueWithMixedValuesInArray()
    {
        $options = [
            'true_value' => 'true',
            'false_value' => 'false',
            'data_provider' => function ($data) { return (array) $data; },
        ];

        $object = new \stdClass();
        $object->key = true;
        $object->key2 = 1;
        $object->key3 = new \DateTime();
        $data = [1 => $object];

        $this->assertCellValueEquals('true', $data, $options);
        $this->assertCellValueNotEquals('false', $data, $options);
    }

    public function testValueWithFalseValuesInArray()
    {
        $options = [
            'true_value' => 'true',
            'false_value' => 'false',
            'data_provider' => function ($data) { return (array) $data; },
        ];

        $object = new \stdClass();
        $object->key = false;
        $object->key2 = false;
        $data = [1 => $object];

        $this->assertCellValueNotEquals('true', $data, $options);
        $this->assertCellValueEquals('false', $data, $options);
    }

    public function testValueWithMixedValuesAndFalseInArray()
    {
        $options = [
            'true_value' => 'true',
            'false_value' => 'false',
            'data_provider' => function ($data) { return (array) $data; },
        ];

        $object = new \stdClass();
        $object->key = true;
        $object->key2 = 1;
        $object->key3 = new \DateTime();
        $object->key4 = false;
        $data = [1 => $object];

        $this->assertCellValueEquals('false', $data, $options);
        $this->assertCellValueNotEquals('true', $data, $options);
    }
}
