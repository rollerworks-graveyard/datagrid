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

use Rollerworks\Component\Datagrid\Extension\Core\DataTransformer\EmptyValueTransformer;

class EmptyValueTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function testTransformWithDefaults()
    {
        $transformer = new EmptyValueTransformer();

        $this->assertEquals('foo', $transformer->transform('foo'));
        $this->assertEquals(' bar', $transformer->transform(' bar'));
    }

    public function testTransformEmptyValue()
    {
        $transformer = new EmptyValueTransformer('-');

        $this->assertEquals('foo', $transformer->transform('foo'));
        $this->assertEquals(' bar', $transformer->transform(' bar'));
        $this->assertEquals('0', $transformer->transform('0'));
        $this->assertEquals(0, $transformer->transform(0));

        $this->assertEquals('-', $transformer->transform(null));
        $this->assertEquals('-', $transformer->transform(false));
        $this->assertEquals('-', $transformer->transform(''));
    }

    public function testTransformEmptyValueWithArray()
    {
        $transformer = new EmptyValueTransformer('-');

        $this->assertEquals(['id' => 'val', 'name' => '-'], $transformer->transform(['id' => 'val', 'name' => null]));
        $this->assertEquals(['id' => '-', 'name' => '-'], $transformer->transform(['id' => '', 'name' => null]));
    }

    public function testTransformEmptyValuePerField()
    {
        $transformer = new EmptyValueTransformer(['id' => '-', 'name' => '?']);

        $this->assertEquals(['id' => '-', 'name' => 'val'], $transformer->transform(['id' => '', 'name' => 'val']));
        $this->assertEquals(['id' => '-', 'name' => '?'], $transformer->transform(['id' => '', 'name' => null]));
    }
}
