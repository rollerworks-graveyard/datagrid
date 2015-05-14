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

use Rollerworks\Component\Datagrid\Extension\Core\DataTransformer\TrimTransformer;

class TrimTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function testTransform()
    {
        $transformer = new TrimTransformer();

        $this->assertEquals('foo', $transformer->transform(' foo '));
    }
}
