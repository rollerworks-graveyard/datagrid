<?php

declare(strict_types=1);

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Tests\Extension\Core\DataTransformer;

use Rollerworks\Component\Datagrid\Extension\Core\DataTransformer\TimestampToDateTimeTransformer;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class TimestampToDateTimeTransformerTest extends DateTimeTestCase
{
    public function testTransform()
    {
        $transformer = new TimestampToDateTimeTransformer('UTC', 'UTC');

        $output = new \DateTime('2010-02-03 04:05:06 UTC');
        $input = $output->format('U');

        $this->assertDateTimeEquals($output, $transformer->transform($input));
    }

    public function testTransformEmpty()
    {
        $transformer = new TimestampToDateTimeTransformer();

        $this->assertNull($transformer->transform(null));
    }

    public function testTransformWithDifferentTimezones()
    {
        $transformer = new TimestampToDateTimeTransformer('Asia/Hong_Kong', 'America/New_York');

        $output = new \DateTime('2010-02-03 04:05:06 America/New_York');
        $input = $output->format('U');
        $output->setTimezone(new \DateTimeZone('Asia/Hong_Kong'));

        $this->assertDateTimeEquals($output, $transformer->transform($input));
    }

    public function testTransformExpectsValidTimestamp()
    {
        $transformer = new TimestampToDateTimeTransformer();

        $this->setExpectedException('Rollerworks\Component\Datagrid\Exception\TransformationFailedException');

        $transformer->transform('2010-2010-2010');
    }
}
