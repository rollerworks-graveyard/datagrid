<?php declare(strict_types=1);

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
        $transformer = new ValueFormatTransformer();

        $this->assertEquals('foo', $transformer->transform('foo'));
        $this->assertEquals(' bar', $transformer->transform(' bar'));
        $this->assertEquals('', $transformer->transform(null));
        $this->assertEquals('', $transformer->transform(''));
    }

    public function testTransformWithFormatter()
    {
        $format = function ($values) {
            return $values['id'].'/%/'.$values['name'];
        };

        $transformer = new ValueFormatTransformer(null, $format);

        // Don't test none-array values as mixing these is not supported
        $this->assertEquals('1/%/who', $transformer->transform(['id' => '1', 'name' => 'who']));
    }

    public function testTransformWithFormatterArrayValue()
    {
        $format = function ($field) {
            return '{{ '.$field.' }}';
        };

        $transformer = new ValueFormatTransformer(null, $format);

        $this->assertEquals('{{ name }}', $transformer->transform('name'));
    }

    public function testTransformWithFormatterArrayValueAndGlue()
    {
        $transformer = new ValueFormatTransformer(', ', '{{ %s }}');

        $this->assertEquals('{{ 1 }}, {{ who }}', $transformer->transform(['id' => '1', 'name' => 'who']));
    }

    public function testTransformWithClosureFormatterArrayValueAndGlue()
    {
        $format = function ($field) {
            return '{{ '.$field.' }}';
        };

        $transformer = new ValueFormatTransformer(', ', $format);

        $this->assertEquals('{{ 1 }}, {{ who }}', $transformer->transform(['id' => '1', 'name' => 'who']));
    }
}
