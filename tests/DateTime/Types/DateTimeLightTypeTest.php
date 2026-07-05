<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests\DateTime\Types;

use DraculAid\PhpTools\DateTime\Dictionary\DateTimeFormats;
use DraculAid\PhpTools\DateTime\Types\DateTimeLightType;
use DraculAid\PhpTools\tests\AbstractProjectTestCase;

/**
 * Test for {@coversDefaultClass DateTimeLightType}
 *
 * @run php tests/run.php tests/DateTime/Types/DateTimeLightTypeTest.php
 */
class DateTimeLightTypeTest extends AbstractProjectTestCase
{
    /**
     * Test for {@covers DateTimeLightType::getTimestamp()}
     * Test for {@covers DateTimeLightType::format()}
     * Test for {@covers DateTimeLightType::__toString()}
     *
     * Test for {@covers DateTimeLightType::$year}
     * Test for {@covers DateTimeLightType::$mon}
     * Test for {@covers DateTimeLightType::$day}
     * Test for {@covers DateTimeLightType::$hour}
     * Test for {@covers DateTimeLightType::$minute}
     * Test for {@covers DateTimeLightType::$second}
     *
     * Test for {@covers DateTimeLightType::setDate()}
     * Test for {@covers DateTimeLightType::setYear()}
     * Test for {@covers DateTimeLightType::setMon()}
     * Test for {@covers DateTimeLightType::setDay()}
     * Test for {@covers DateTimeLightType::setHour()}
     * Test for {@covers DateTimeLightType::setMinutes()}
     * Test for {@covers DateTimeLightType::setSecond()}
     *
     * @return void
     */
    public function testRun(): void
    {
        $testDate = new \DateTime('2022-10-20 12:15:40');

        $testObject = new DateTimeLightType($testDate);
        self::assertEquals(2022, $testObject->year);
        self::assertEquals(10, $testObject->mon);
        self::assertEquals(20, $testObject->day);
        self::assertEquals(12, $testObject->hour);
        self::assertEquals(15, $testObject->minute);
        self::assertEquals(40, $testObject->second);

        self::assertTimestamp($testDate->getTimestamp(), $testObject->getTimestamp());
        self::assertEquals($testDate->format(DateTimeFormats::VIEW_FOR_PEOPLE_WITH_TIMEZONE), $testObject->format(DateTimeFormats::VIEW_FOR_PEOPLE_WITH_TIMEZONE));
        self::assertEquals($testDate->format(DateTimeFormats::FUNCTIONS), $testObject->__toString());

        $testObject->year = 2023;
        self::assertEquals(2023, $testObject->year);
        $testObject->setYear(2022);
        self::assertEquals(2022, $testObject->year);

        $testObject->mon = 1;
        self::assertEquals(1, $testObject->mon);
        $testObject->setMon(12);
        self::assertEquals(12, $testObject->mon);

        $testObject->day = 1;
        self::assertEquals(1, $testObject->day);
        $testObject->setDay(31);
        self::assertEquals(31, $testObject->day);

        $testObject->hour = 0;
        self::assertEquals(0, $testObject->hour);
        $testObject->setHour(23);
        self::assertEquals(23, $testObject->hour);

        $testObject->minute = 0;
        self::assertEquals(0, $testObject->minute);
        $testObject->setMinute(59);
        self::assertEquals(59, $testObject->minute);

        $testObject->second = 0;
        self::assertEquals(0, $testObject->second);
        $testObject->setSecond(59);
        self::assertEquals(59, $testObject->second);
    }
}
