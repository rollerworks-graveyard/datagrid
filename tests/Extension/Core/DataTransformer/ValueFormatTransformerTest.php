<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Tests\Extension\Core\DataTransformer;

use Rollerworks\Component\Datagrid\Extension\Core\DataTransformer\ValueFormatTransformer;

class ValueFormatTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function testTransformWithDefaults()
    {
        $transformer = new ValueFormatTransformer();

        $this->assertEquals('foo', $transformer->transform('foo'));
        $this->assertEquals(' bar', $transformer->transform(' bar'));
    }

    public function testTransformEmptyValue()
    {
        $transformer = new ValueFormatTransformer('-');

        $this->assertEquals('foo', $transformer->transform('foo'));
        $this->assertEquals(' bar', $transformer->transform(' bar'));
        $this->assertEquals('-', $transformer->transform(null));
        $this->assertEquals('-', $transformer->transform(''));
    }

    public function testTransformEmptyValueWithArray()
    {
        $transformer = new ValueFormatTransformer('-', ',');

        $this->assertEquals('foo', $transformer->transform('foo'));
        $this->assertEquals('-', $transformer->transform(null));
        $this->assertEquals('-', $transformer->transform(''));
        $this->assertEquals('-,-', $transformer->transform(['id' => '', 'name' => null]));
    }

    public function testTransformEmptyValuePerFieldWithArray()
    {
        $transformer = new ValueFormatTransformer(['id' => '0', 'name' => 'NV'], ',', null, ['id' => 'user.id', 'name' => 'name']);

        $this->assertEquals('foo', $transformer->transform('foo'));
        $this->assertEquals('', $transformer->transform(null));
        $this->assertEquals('', $transformer->transform(''));
        $this->assertEquals('0,NV', $transformer->transform(['id' => '', 'name' => null]));
    }

    public function testTransformEmptyValueWithArrayAndFormatter()
    {
        $transformer = new ValueFormatTransformer('-', null, '%s/%s');

        // Don't test none-array values as mixing these is not supported
        $this->assertEquals('1/who', $transformer->transform(['id' => '1', 'name' => 'who']));
        $this->assertEquals('-/-', $transformer->transform(['id' => '', 'name' => null]));
    }

    public function testTransformWithFormatter()
    {
        $format = function ($values) {
            return $values['id'].'/%/'.$values['name'];
        };

        $transformer = new ValueFormatTransformer('-', null, $format, ['id', 'name']);

        // Don't test none-array values as mixing these is not supported
        $this->assertEquals('1/%/who', $transformer->transform(['id' => '1', 'name' => 'who']));
        $this->assertEquals('-/%/-', $transformer->transform(['id' => '', 'name' => null]));
    }

    public function testTransformWithFormatterAndArray()
    {
        $format = function ($field) {
            return '{{ '.$field.' }}';
        };

        $transformer = new ValueFormatTransformer('-', null, $format);

        $this->assertEquals('{{ name }}', $transformer->transform('name'));
        $this->assertEquals('{{ - }}', $transformer->transform(null));
    }

    public function testTransformWithFormatterAndArrayAndGlue()
    {
        $transformer = new ValueFormatTransformer('-', ', ', '{{ %s }}');

        $this->assertEquals('{{ 1 }}, {{ who }}', $transformer->transform(['id' => '1', 'name' => 'who']));
        $this->assertEquals('{{ - }}, {{ - }}', $transformer->transform(['id' => '', 'name' => null]));
    }

    public function testTransformWithClosureFormatterAndArrayAndGlue()
    {
        $format = function ($field) {
            return '{{ '.$field.' }}';
        };

        $transformer = new ValueFormatTransformer('-', ', ', $format);

        $this->assertEquals('{{ 1 }}, {{ who }}', $transformer->transform(['id' => '1', 'name' => 'who']));
        $this->assertEquals('{{ - }}, {{ - }}', $transformer->transform(['id' => '', 'name' => null]));
    }
}
