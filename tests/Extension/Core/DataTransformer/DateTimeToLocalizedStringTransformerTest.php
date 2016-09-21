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

use Rollerworks\Component\Datagrid\Extension\Core\DataTransformer\DateTimeToLocalizedStringTransformer;
use Symfony\Component\Intl\Util\IntlTestHelper;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class DateTimeToLocalizedStringTransformerTest extends DateTimeTestCase
{
    protected $dateTime;
    protected $dateTimeWithoutSeconds;

    protected function setUp()
    {
        parent::setUp();

        // Since we test against "de_AT", we need the full implementation
        IntlTestHelper::requireFullIntl($this);

        \Locale::setDefault('de_AT');

        $this->dateTime = new \DateTime('2010-02-03 04:05:06 UTC');
        $this->dateTimeWithoutSeconds = new \DateTime('2010-02-03 04:05:00 UTC');
    }

    protected function tearDown()
    {
        $this->dateTime = null;
        $this->dateTimeWithoutSeconds = null;
    }

    public static function assertEquals($expected, $actual, $message = '', $delta = 0, $maxDepth = 10, $canonicalize = false, $ignoreCase = false)
    {
        if ($expected instanceof \DateTime && $actual instanceof \DateTime) {
            $expected = $expected->format('c');
            $actual = $actual->format('c');
        }

        parent::assertEquals($expected, $actual, $message, $delta, $maxDepth, $canonicalize, $ignoreCase);
    }

    public function dataProvider()
    {
        return [
            [\IntlDateFormatter::SHORT, null, null, '03.02.2010, 04:05', '2010-02-03 04:05:00 UTC'],
            [\IntlDateFormatter::MEDIUM, null, null, '03.02.2010, 04:05', '2010-02-03 04:05:00 UTC'],
            [\IntlDateFormatter::LONG, null, null, '3. Februar 2010 um 04:05', '2010-02-03 04:05:00 UTC'],
            [\IntlDateFormatter::FULL, null, null, 'Mittwoch, 3. Februar 2010 um 04:05', '2010-02-03 04:05:00 UTC'],
            [\IntlDateFormatter::SHORT, \IntlDateFormatter::NONE, null, '03.02.2010', '2010-02-03 00:00:00 UTC'],
            [\IntlDateFormatter::MEDIUM, \IntlDateFormatter::NONE, null, '03.02.2010', '2010-02-03 00:00:00 UTC'],
            [\IntlDateFormatter::LONG, \IntlDateFormatter::NONE, null, '3. Februar 2010', '2010-02-03 00:00:00 UTC'],
            [\IntlDateFormatter::FULL, \IntlDateFormatter::NONE, null, 'Mittwoch, 3. Februar 2010', '2010-02-03 00:00:00 UTC'],
            [null, \IntlDateFormatter::SHORT, null, '03.02.2010, 04:05', '2010-02-03 04:05:00 UTC'],
            [null, \IntlDateFormatter::MEDIUM, null, '03.02.2010, 04:05:06', '2010-02-03 04:05:06 UTC'],
            [null, \IntlDateFormatter::LONG, null, '03.02.2010, 04:05:06 GMT', '2010-02-03 04:05:06 UTC'],
            // see below for extra test case for time format FULL
            [\IntlDateFormatter::NONE, \IntlDateFormatter::SHORT, null, '04:05', '1970-01-01 04:05:00 UTC'],
            [\IntlDateFormatter::NONE, \IntlDateFormatter::MEDIUM, null, '04:05:06', '1970-01-01 04:05:06 UTC'],
            [\IntlDateFormatter::NONE, \IntlDateFormatter::LONG, null, '04:05:06 GMT', '1970-01-01 04:05:06 UTC'],
            [null, null, 'yyyy-MM-dd HH:mm:00', '2010-02-03 04:05:00', '2010-02-03 04:05:00 UTC'],
            [null, null, 'yyyy-MM-dd HH:mm', '2010-02-03 04:05', '2010-02-03 04:05:00 UTC'],
            [null, null, 'yyyy-MM-dd HH', '2010-02-03 04', '2010-02-03 04:00:00 UTC'],
            [null, null, 'yyyy-MM-dd', '2010-02-03', '2010-02-03 00:00:00 UTC'],
            [null, null, 'yyyy-MM', '2010-02', '2010-02-01 00:00:00 UTC'],
            [null, null, 'yyyy', '2010', '2010-01-01 00:00:00 UTC'],
            [null, null, 'dd-MM-yyyy', '03-02-2010', '2010-02-03 00:00:00 UTC'],
            [null, null, 'HH:mm:ss', '04:05:06', '1970-01-01 04:05:06 UTC'],
            [null, null, 'HH:mm:00', '04:05:00', '1970-01-01 04:05:00 UTC'],
            [null, null, 'HH:mm', '04:05', '1970-01-01 04:05:00 UTC'],
            [null, null, 'HH', '04', '1970-01-01 04:00:00 UTC'],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testTransform($dateFormat, $timeFormat, $pattern, $output, $input)
    {
        $transformer = new DateTimeToLocalizedStringTransformer(
            'UTC',
            'UTC',
            $dateFormat,
            $timeFormat,
            \IntlDateFormatter::GREGORIAN,
            $pattern
        );

        $input = new \DateTime($input);

        $this->assertEquals($output, $transformer->transform($input));
    }

    public function testTransformFullTime()
    {
        $transformer = new DateTimeToLocalizedStringTransformer('UTC', 'UTC', null, \IntlDateFormatter::FULL);

        $this->assertEquals('03.02.2010, 04:05:06 GMT', $transformer->transform($this->dateTime));
    }

    public function testTransformToDifferentLocale()
    {
        \Locale::setDefault('en_US');

        $transformer = new DateTimeToLocalizedStringTransformer('UTC', 'UTC');

        $this->assertEquals('Feb 3, 2010, 4:05 AM', $transformer->transform($this->dateTime));
    }

    public function testTransformEmpty()
    {
        $transformer = new DateTimeToLocalizedStringTransformer();

        $this->assertSame('', $transformer->transform(null));
    }

    public function testTransformWithDifferentTimezones()
    {
        $transformer = new DateTimeToLocalizedStringTransformer('America/New_York', 'Asia/Hong_Kong');

        $input = new \DateTime('2010-02-03 04:05:06 America/New_York');

        $dateTime = clone $input;
        $dateTime->setTimezone(new \DateTimeZone('Asia/Hong_Kong'));

        $this->assertEquals($dateTime->format('d.m.Y, H:i'), $transformer->transform($input));
    }

    public function testTransformWithDifferentPatterns()
    {
        $transformer = new DateTimeToLocalizedStringTransformer('UTC', 'UTC', \IntlDateFormatter::FULL, \IntlDateFormatter::FULL, \IntlDateFormatter::GREGORIAN, 'MM*yyyy*dd HH|mm|ss');

        $this->assertEquals('02*2010*03 04|05|06', $transformer->transform($this->dateTime));
    }

    /**
     * @expectedException \Rollerworks\Component\Datagrid\Exception\TransformationFailedException
     */
    public function testTransformRequiresValidDateTime()
    {
        $transformer = new DateTimeToLocalizedStringTransformer();
        $transformer->transform('2010-01-01');
    }

    /**
     * @expectedException \Rollerworks\Component\Datagrid\Exception\UnexpectedTypeException
     */
    public function testValidateDateFormatOption()
    {
        new DateTimeToLocalizedStringTransformer(null, null, 'foobar');
    }

    /**
     * @expectedException \Rollerworks\Component\Datagrid\Exception\UnexpectedTypeException
     */
    public function testValidateTimeFormatOption()
    {
        new DateTimeToLocalizedStringTransformer(null, null, null, 'foobar');
    }
}
