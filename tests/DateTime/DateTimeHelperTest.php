<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests\DateTime;

use DraculAid\PhpTools\DateTime\DateTimeHelper;
use DraculAid\PhpTools\DateTime\TimestampHelper;
use DraculAid\PhpTools\DateTime\Dictionary\DateTimeFormats;
use DraculAid\PhpTools\DateTime\Dictionary\TimestampConstants;
use DraculAid\PhpTools\DateTime\Types\TimestampType;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see DateTimeHelper}
 *
 * @run php tests/run.php tests/DateTime/DateTimeHelperTest.php
 */
class DateTimeHelperTest extends TestCase
{
    /**
     * Test for {@see DateTimeHelper::getTimezoneOffsetSec()}
     *
     * @return void
     */
    public function testGetTimezoneOffsetSec(): void
    {
        self::assertEquals((int)Date('Z'), DateTimeHelper::getTimezoneOffsetSec());
        self::assertEquals((int)Date('Z'), DateTimeHelper::getTimezoneOffsetSec(null));

        self::assertEquals(0, DateTimeHelper::getTimezoneOffsetSec('UTC'));
        self::assertEquals(-10*60*60, DateTimeHelper::getTimezoneOffsetSec('HST'));
        self::assertEquals(3*60*60, DateTimeHelper::getTimezoneOffsetSec('MSK'));
    }

    /**
     * Test for {@see DateTimeHelper::getDateArray()}
     * Test for {@see DateTimeHelper::isValidDateArray()}
     *
     * @return void
     */
    public function testGetDateArrayAndIsValidDateArray(): void
    {
        $dateArray = [
            'year' => 2023, 'mon' => 2, 'yday' => 35, 'mday' => 5, 'wday' => 0,
            'hours' => 12, 'minutes' => 30,  'seconds' => 30,
            'month' => 'February', 'weekday' => 'Sunday',
            0 => mktime(12, 30, 30, 2, 5, 2023)
        ];

        self::assertTrue(DateTimeHelper::isValidDateArray($dateArray));

        foreach ($dateArray as $key => $value)
        {
            $testArray = $dateArray;
            unset($testArray[$key]);
            self::assertFalse(DateTimeHelper::isValidDateArray($testArray));
        }

        // * * *

        self::assertEquals(getdate(time()), DateTimeHelper::getDateArray());
        self::assertEquals(getdate(time()), DateTimeHelper::getDateArray(null));
        self::assertEquals($dateArray, DateTimeHelper::getDateArray($dateArray[0]));
        self::assertEquals($dateArray, DateTimeHelper::getDateArray($dateArray[0] + 0.123456));
        self::assertEquals(
            $dateArray,
            DateTimeHelper::getDateArray("{$dateArray['year']}-{$dateArray['mon']}-{$dateArray['mday']} {$dateArray['hours']}:{$dateArray['minutes']}:{$dateArray['seconds']}")
        );
        self::assertEquals(
            $dateArray,
            DateTimeHelper::getDateArray($dateArray)
        );
        self::assertEquals(
            $dateArray,
            DateTimeHelper::getDateArray(new \DateTime("{$dateArray['year']}-{$dateArray['mon']}-{$dateArray['mday']} {$dateArray['hours']}:{$dateArray['minutes']}:{$dateArray['seconds']}"))
        );
        self::assertEquals(
            $dateArray,
            DateTimeHelper::getDateArray(new \DateTimeImmutable("{$dateArray['year']}-{$dateArray['mon']}-{$dateArray['mday']} {$dateArray['hours']}:{$dateArray['minutes']}:{$dateArray['seconds']}"))
        );
        self::assertEquals(
            $dateArray,
            DateTimeHelper::getDateArray(new TimestampType("{$dateArray['year']}-{$dateArray['mon']}-{$dateArray['mday']} {$dateArray['hours']}:{$dateArray['minutes']}:{$dateArray['seconds']}"))
        );
        self::assertEquals(
            $dateArray,
            DateTimeHelper::getDateArray(
                new class($dateArray) {
                    private array $dateArray;
                    public function __construct($dateArray) {
                        $this->dateArray = $dateArray;
                    }
                    public function getTimestamp() {
                        return TimestampHelper::getdateArrayToTimestamp($this->dateArray);
                    }
                }
            )
        );
    }

    /**
     * Test for {@see DateTimeHelper::getTimeString()}
     *
     * @return void
     */
    public function testGetTimeString(): void
    {
        self::assertEquals(date(DateTimeFormats::SQL_TIME), DateTimeHelper::getTimeString());
        self::assertEquals(date(DateTimeFormats::SQL_TIME), DateTimeHelper::getTimeString(null));
        self::assertEquals('00:00:00', DateTimeHelper::getTimeString(false));
        self::assertEquals('23:59:59', DateTimeHelper::getTimeString(true));
        self::assertEquals(date(DateTimeFormats::SQL_TIME), DateTimeHelper::getTimeString(time()));
        self::assertEquals(date(DateTimeFormats::SQL_TIME), DateTimeHelper::getTimeString(time() + 0.123456));
        self::assertEquals('00:00:00', DateTimeHelper::getTimeString(new \DateTime('Now midnight')));
        self::assertEquals('00:00:00', DateTimeHelper::getTimeString(new \DateTimeImmutable('Now midnight')));
    }

    /**
     * Test for {@see DateTimeHelper::getTimeInt()}
     * Test for {@see DateTimeHelper::getDaySecFromDateTime()}
     *
     * @return void
     */
    public function testGetTimeIntAndGetDaySecFromDateTime(): void
    {
        self::assertEquals(0, DateTimeHelper::getDaySecFromDateTime("2021-07-15 0:00:00.123567"));
        self::assertEquals(7820, DateTimeHelper::getDaySecFromDateTime("2021-07-15 2:10:20.123567"));

        // * * *

        self::assertEquals(DateTimeHelper::getDaySecFromDateTime(), DateTimeHelper::getTimeInt());
        self::assertEquals(123456, DateTimeHelper::getTimeInt(123456));
        self::assertEquals(123456, DateTimeHelper::getTimeInt(123456.789));
        self::assertEquals(0, DateTimeHelper::getTimeInt(false));
        self::assertEquals(TimestampConstants::DAY_SEC - 1, DateTimeHelper::getTimeInt(true));
        self::assertEquals(0, DateTimeHelper::getTimeInt(new \DateTime('Now midnight')));
        self::assertEquals(0, DateTimeHelper::getTimeInt(new \DateTimeImmutable('Now midnight')));
    }
}
