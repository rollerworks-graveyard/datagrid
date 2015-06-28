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

class TextTypeTest extends BaseTypeTest
{
    public function testTrimOption()
    {
        $this->assertCellValueEquals('foo', 'foo', ['trim' => true]);
    }

    public function testFormatOption()
    {
        $this->assertCellValueEquals(' - foo - ', 'foo', ['value_format' => ' - %s - ']);
    }

    public function testFormatAndTrimOption()
    {
        $options = [
            'trim' => true,
            'value_format' => ' -%s- ',
        ];

        $this->assertCellValueEquals(' -foo- ', ' foo ', $options);
    }

    public function testFormatOptionWithArray()
    {
        $object = new \stdClass();
        $object->key = 'foo';
        $object->key2 = 'bar';
        $data = [1 => $object];

        $options = [
            'field_mapping' => ['foo' => 'key', 'key2'],
            'value_format' => ' - %s - ',
            'value_glue' => ',',
        ];

        $this->assertCellValueEquals(' - foo - , - bar - ', $data, $options);
    }

    public function testEmptyValueOption()
    {
        $object = new \stdClass();
        $object->key = 'foo';
        $object->key2 = null;
        $data = [1 => $object];

        $options = [
            'field_mapping' => ['key', 'key2'],
            'value_format' => ' - %s - ',
            'empty_value' => '?',
            'value_glue' => ',',
        ];

        $this->assertCellValueEquals(' - foo - , - ? - ', $data, $options);
    }

    public function testEmptyValueAsArrayOption()
    {
        $object = new \stdClass();
        $object->key = 'foo';
        $object->key2 = null;
        $data = [1 => $object];

        $options = [
            'field_mapping' => ['key', 'key2'],
            'value_format' => ' - %s - ',
            'empty_value' => ['key' => '?1', 'key2' => '?2'],
            'value_glue' => ',',
        ];

        $this->assertCellValueEquals(' - foo - , - ?2 - ', $data, $options);
    }

    protected function getTestedType()
    {
        return 'text';
    }
}
