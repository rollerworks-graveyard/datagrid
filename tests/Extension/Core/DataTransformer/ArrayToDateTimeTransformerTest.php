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

use Rollerworks\Component\Datagrid\Extension\Core\DataTransformer\ArrayToDateTimeTransformer;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class ArrayToDateTimeTransformerTest extends DateTimeTestCase
{
    /**
     * @expectedException \Rollerworks\Component\Datagrid\Exception\TransformationFailedException
     */
    public function testTransformRequiresDateTime()
    {
        $transformer = new ArrayToDateTimeTransformer();
        $transformer->transform('12345');
    }

    public function testTransform()
    {
        $transformer = new ArrayToDateTimeTransformer('UTC', 'UTC');

        $input = [
            'year' => 2010,
            'month' => 2,
            'day' => 3,
            'hour' => 4,
            'minute' => 5,
            'second' => 6,
        ];

        $output = new \DateTime('2010-02-03 04:05:06 UTC');

        $this->assertDateTimeEquals($output, $transformer->transform($input));
    }

    public function testTransformWithSomeZero()
    {
        $transformer = new ArrayToDateTimeTransformer('UTC', 'UTC');

        $input = [
            'year' => 2010,
            'month' => 2,
            'day' => 3,
            'hour' => 4,
            'minute' => 0,
            'second' => 0,
        ];

        $output = new \DateTime('2010-02-03 04:00:00 UTC');

        $this->assertDateTimeEquals($output, $transformer->transform($input));
    }

    public function testTransformCompletelyEmpty()
    {
        $transformer = new ArrayToDateTimeTransformer();

        $input = [
            'year' => '',
            'month' => '',
            'day' => '',
            'hour' => '',
            'minute' => '',
            'second' => '',
        ];

        $this->assertNull($transformer->transform($input));
    }

    public function testTransformCompletelyEmptySubsetOfFields()
    {
        $transformer = new ArrayToDateTimeTransformer(null, null, ['year', 'month', 'day']);

        $input = [
            'year' => '',
            'month' => '',
            'day' => '',
        ];

        $this->assertNull($transformer->transform($input));
    }

    /**
     * @expectedException \Rollerworks\Component\Datagrid\Exception\TransformationFailedException
     */
    public function testTransformPartiallyEmptyYear()
    {
        $transformer = new ArrayToDateTimeTransformer();
        $transformer->transform([
            'month' => 2,
            'day' => 3,
            'hour' => 4,
            'minute' => 5,
            'second' => 6,
        ]);
    }

    /**
     * @expectedException \Rollerworks\Component\Datagrid\Exception\TransformationFailedException
     */
    public function testTransformPartiallyEmptyMonth()
    {
        $transformer = new ArrayToDateTimeTransformer();
        $transformer->transform([
            'year' => 2010,
            'day' => 3,
            'hour' => 4,
            'minute' => 5,
            'second' => 6,
        ]);
    }

    /**
     * @expectedException \Rollerworks\Component\Datagrid\Exception\TransformationFailedException
     */
    public function testTransformPartiallyEmptyDay()
    {
        $transformer = new ArrayToDateTimeTransformer();
        $transformer->transform([
            'year' => 2010,
            'month' => 2,
            'hour' => 4,
            'minute' => 5,
            'second' => 6,
        ]);
    }

    /**
     * @expectedException \Rollerworks\Component\Datagrid\Exception\TransformationFailedException
     */
    public function testTransformPartiallyEmptyHour()
    {
        $transformer = new ArrayToDateTimeTransformer();
        $transformer->transform([
            'year' => 2010,
            'month' => 2,
            'day' => 3,
            'minute' => 5,
            'second' => 6,
        ]);
    }

    /**
     * @expectedException \Rollerworks\Component\Datagrid\Exception\TransformationFailedException
     */
    public function testTransformPartiallyEmptyMinute()
    {
        $transformer = new ArrayToDateTimeTransformer();
        $transformer->transform([
            'year' => 2010,
            'month' => 2,
            'day' => 3,
            'hour' => 4,
            'second' => 6,
        ]);
    }

    /**
     * @expectedException \Rollerworks\Component\Datagrid\Exception\TransformationFailedException
     */
    public function testTransformPartiallyEmptySecond()
    {
        $transformer = new ArrayToDateTimeTransformer();
        $transformer->transform([
            'year' => 2010,
            'month' => 2,
            'day' => 3,
            'hour' => 4,
            'minute' => 5,
        ]);
    }

    public function testTransformNull()
    {
        $transformer = new ArrayToDateTimeTransformer();

        $this->assertNull($transformer->transform(null));
    }

    public function testTransformDifferentTimezones()
    {
        $transformer = new ArrayToDateTimeTransformer('America/New_York', 'Asia/Hong_Kong');

        $input = [
            'year' => 2010,
            'month' => 2,
            'day' => 3,
            'hour' => 4,
            'minute' => 5,
            'second' => 6,
        ];

        $output = new \DateTime('2010-02-03 04:05:06 Asia/Hong_Kong');
        $output->setTimezone(new \DateTimeZone('America/New_York'));

        $this->assertDateTimeEquals($output, $transformer->transform($input));
    }

    public function testTransformToDifferentTimezone()
    {
        $transformer = new ArrayToDateTimeTransformer('Asia/Hong_Kong', 'UTC');

        $input = [
            'year' => 2010,
            'month' => 2,
            'day' => 3,
            'hour' => 4,
            'minute' => 5,
            'second' => 6,
        ];

        $output = new \DateTime('2010-02-03 04:05:06 UTC');
        $output->setTimezone(new \DateTimeZone('Asia/Hong_Kong'));

        $this->assertDateTimeEquals($output, $transformer->transform($input));
    }

    /**
     * @expectedException \Rollerworks\Component\Datagrid\Exception\TransformationFailedException
     */
    public function testTransformRequiresArray()
    {
        $transformer = new ArrayToDateTimeTransformer();
        $transformer->transform('12345');
    }

    /**
     * @expectedException \Rollerworks\Component\Datagrid\Exception\TransformationFailedException
     */
    public function testTransformWithNegativeYear()
    {
        $transformer = new ArrayToDateTimeTransformer();
        $transformer->transform([
            'year' => -1,
            'month' => 2,
            'day' => 3,
            'hour' => 4,
            'minute' => 5,
            'second' => 6,
        ]);
    }

    /**
     * @expectedException \Rollerworks\Component\Datagrid\Exception\TransformationFailedException
     */
    public function testTransformWithNegativeMonth()
    {
        $transformer = new ArrayToDateTimeTransformer();
        $transformer->transform([
            'year' => 2010,
            'month' => -1,
            'day' => 3,
            'hour' => 4,
            'minute' => 5,
            'second' => 6,
        ]);
    }

    /**
     * @expectedException \Rollerworks\Component\Datagrid\Exception\TransformationFailedException
     */
    public function testTransformWithNegativeDay()
    {
        $transformer = new ArrayToDateTimeTransformer();
        $transformer->transform([
            'year' => 2010,
            'month' => 2,
            'day' => -1,
            'hour' => 4,
            'minute' => 5,
            'second' => 6,
        ]);
    }

    /**
     * @expectedException \Rollerworks\Component\Datagrid\Exception\TransformationFailedException
     */
    public function testTransformWithNegativeHour()
    {
        $transformer = new ArrayToDateTimeTransformer();
        $transformer->transform([
            'year' => 2010,
            'month' => 2,
            'day' => 3,
            'hour' => -1,
            'minute' => 5,
            'second' => 6,
        ]);
    }

    /**
     * @expectedException \Rollerworks\Component\Datagrid\Exception\TransformationFailedException
     */
    public function testTransformWithNegativeMinute()
    {
        $transformer = new ArrayToDateTimeTransformer();
        $transformer->transform([
            'year' => 2010,
            'month' => 2,
            'day' => 3,
            'hour' => 4,
            'minute' => -1,
            'second' => 6,
        ]);
    }

    /**
     * @expectedException \Rollerworks\Component\Datagrid\Exception\TransformationFailedException
     */
    public function testTransformWithNegativeSecond()
    {
        $transformer = new ArrayToDateTimeTransformer();
        $transformer->transform([
            'year' => 2010,
            'month' => 2,
            'day' => 3,
            'hour' => 4,
            'minute' => 5,
            'second' => -1,
        ]);
    }

    /**
     * @expectedException \Rollerworks\Component\Datagrid\Exception\TransformationFailedException
     */
    public function testTransformWithInvalidMonth()
    {
        $transformer = new ArrayToDateTimeTransformer();
        $transformer->transform([
            'year' => 2010,
            'month' => 13,
            'day' => 3,
            'hour' => 4,
            'minute' => 5,
            'second' => 6,
        ]);
    }

    /**
     * @expectedException \Rollerworks\Component\Datagrid\Exception\TransformationFailedException
     */
    public function testTransformWithInvalidDay()
    {
        $transformer = new ArrayToDateTimeTransformer();
        $transformer->transform([
            'year' => 2010,
            'month' => 2,
            'day' => 31,
            'hour' => 4,
            'minute' => 5,
            'second' => 6,
        ]);
    }

    /**
     * @expectedException \Rollerworks\Component\Datagrid\Exception\TransformationFailedException
     */
    public function testTransformWithStringDay()
    {
        $transformer = new ArrayToDateTimeTransformer();
        $transformer->transform([
            'year' => 2010,
            'month' => 2,
            'day' => 'bazinga',
            'hour' => 4,
            'minute' => 5,
            'second' => 6,
        ]);
    }

    /**
     * @expectedException \Rollerworks\Component\Datagrid\Exception\TransformationFailedException
     */
    public function testTransformWithStringMonth()
    {
        $transformer = new ArrayToDateTimeTransformer();
        $transformer->transform([
            'year' => 2010,
            'month' => 'bazinga',
            'day' => 31,
            'hour' => 4,
            'minute' => 5,
            'second' => 6,
        ]);
    }

    /**
     * @expectedException \Rollerworks\Component\Datagrid\Exception\TransformationFailedException
     */
    public function testTransformWithStringYear()
    {
        $transformer = new ArrayToDateTimeTransformer();
        $transformer->transform([
            'year' => 'bazinga',
            'month' => 2,
            'day' => 31,
            'hour' => 4,
            'minute' => 5,
            'second' => 6,
        ]);
    }
}
