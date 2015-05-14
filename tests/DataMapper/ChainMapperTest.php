<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Tests\DataMapper;

use Rollerworks\Component\Datagrid\DataMapper\ChainMapper;
use Rollerworks\Component\Datagrid\Exception\DataMappingException;

class ChainMapperTest extends \PHPUnit_Framework_TestCase
{
    public function testWithInvalidMappers()
    {
        $this->setExpectedException('Rollerworks\Component\Datagrid\Exception\InvalidArgumentException', 'Mapper needs to implement Rollerworks\Component\Datagrid\DataMapper\DataMapperInterface');

        new ChainMapper([
            'foo',
            'bar',
        ]);
    }

    public function testMappersInChainWithEmptyMappersArray()
    {
        $this->setExpectedException('Rollerworks\Component\Datagrid\Exception\InvalidArgumentException', 'There must be at least one mapper in the chain.');
        new ChainMapper([]);
    }

    public function testGetDataFromTwoMappers()
    {
        $mapper1 = $this->getMock('Rollerworks\Component\Datagrid\DataMapper\DataMapperInterface');
        $mapper2 = $this->getMock('Rollerworks\Component\Datagrid\DataMapper\DataMapperInterface');

        $mapper1->expects($this->once())
               ->method('getData')
               ->will($this->throwException(new DataMappingException()));

        $mapper2->expects($this->once())
               ->method('getData')
               ->will($this->returnValue('foo'));

        $chain = new ChainMapper([$mapper1, $mapper2]);

        $this->assertSame('foo', $chain->getData('foo', 'bar'));
    }

    public function testSetDataWithTwoMappers()
    {
        $mapper1 = $this->getMock('Rollerworks\Component\Datagrid\DataMapper\DataMapperInterface');
        $mapper2 = $this->getMock('Rollerworks\Component\Datagrid\DataMapper\DataMapperInterface');

        $mapper1->expects($this->once())
               ->method('setData')
               ->will($this->throwException(new DataMappingException()));

        $mapper2->expects($this->once())
               ->method('setData')
               ->with('foo', 'bar', 'test')
               ->will($this->returnValue(true));

        $chain = new ChainMapper([$mapper1, $mapper2]);

        $this->assertTrue($chain->setData('foo', 'bar', 'test'));
    }
}
