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
use DraculAid\PhpTools\DateTime\Dictionary\DateTimeFormats;
use DraculAid\PhpTools\DateTime\Dictionary\TimestampConstants;
use DraculAid\PhpTools\DateTime\TimestampHelper;
use DraculAid\PhpTools\DateTime\Types\GetTimestampInterface;
use DraculAid\PhpTools\DateTime\Types\TimestampType;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@coversDefaultClass DateTimeHelper}
 *
 * @run php tests/run.php tests/DateTime/DateTimeHelperTest.php
 */
class DateTimeHelperTest extends TestCase
{
    /**
     * Test for {@covers DateTimeHelper::getTimezoneOffsetSec()}
     *
     * @return void
     */
    public function testGetTimezoneOffsetSec(): void
    {
        $testFunction = DateTimeHelper::getTimezoneOffsetSec(...);

        self::assertEquals((int)Date('Z'), $testFunction());
        self::assertEquals((int)Date('Z'), $testFunction(null));

        self::assertEquals(0, $testFunction('UTC'));
        self::assertEquals(-10*60*60, $testFunction('HST'));
        self::assertEquals(3*60*60, $testFunction('MSK'));
    }

    /**
     * Test for {@covers DateTimeHelper::getDateArray()}
     * Test for {@covers DateTimeHelper::isValidDateArray()}
     *
     * @return void
     *
     * @psalm-suppress InvalidOperand Псалм ругается на недопустимые арифметические операции, причина, члены операций теоретически могут быть разными типами, на практике такого быть не может
     */
    public function testGetDateArrayAndIsValidDateArray(): void
    {
        $testFunctionIsValidDateArray = DateTimeHelper::isValidDateArray(...);
        $testFunctionGetDateArray = DateTimeHelper::getDateArray(...);

        $dateArray = [
            'year' => 2023, 'mon' => 2, 'yday' => 35, 'mday' => 5, 'wday' => 0,
            'hours' => 12, 'minutes' => 30,  'seconds' => 30,
            'month' => 'February', 'weekday' => 'Sunday',
            0 => mktime(12, 30, 30, 2, 5, 2023)
        ];

        self::assertTrue($testFunctionIsValidDateArray($dateArray));

        foreach ($dateArray as $key => $value)
        {
            $testArray = $dateArray;
            unset($testArray[$key]);
            self::assertFalse($testFunctionIsValidDateArray($testArray));
        }

        // * * *

        self::assertEquals(getdate(time()), $testFunctionGetDateArray());
        self::assertEquals(getdate(time()), $testFunctionGetDateArray(null));
        self::assertEquals($dateArray, $testFunctionGetDateArray($dateArray[0]));
        self::assertEquals($dateArray, $testFunctionGetDateArray($dateArray[0] + 0.123456));
        self::assertEquals(
            $dateArray,
            $testFunctionGetDateArray("{$dateArray['year']}-{$dateArray['mon']}-{$dateArray['mday']} {$dateArray['hours']}:{$dateArray['minutes']}:{$dateArray['seconds']}")
        );
        self::assertEquals(
            $dateArray,
            $testFunctionGetDateArray($dateArray)
        );
        self::assertEquals(
            $dateArray,
            $testFunctionGetDateArray(new \DateTime("{$dateArray['year']}-{$dateArray['mon']}-{$dateArray['mday']} {$dateArray['hours']}:{$dateArray['minutes']}:{$dateArray['seconds']}"))
        );
        self::assertEquals(
            $dateArray,
            $testFunctionGetDateArray(new \DateTimeImmutable("{$dateArray['year']}-{$dateArray['mon']}-{$dateArray['mday']} {$dateArray['hours']}:{$dateArray['minutes']}:{$dateArray['seconds']}"))
        );
        self::assertEquals(
            $dateArray,
            $testFunctionGetDateArray(new TimestampType("{$dateArray['year']}-{$dateArray['mon']}-{$dateArray['mday']} {$dateArray['hours']}:{$dateArray['minutes']}:{$dateArray['seconds']}"))
        );
        self::assertEquals(
            $dateArray,
            $testFunctionGetDateArray(
                new class($dateArray) implements GetTimestampInterface {
                    private array $dateArray;
                    public function __construct(array $dateArray) {
                        $this->dateArray = $dateArray;
                    }
                    public function getTimestamp(): int {
                        return TimestampHelper::getdateArrayToTimestamp($this->dateArray);
                    }
                    public function format(string $format = ''): string {
                        return "Не реализованно, для теста и не надо";
                    }
                }
            )
        );
    }

    /**
     * Test for {@covers DateTimeHelper::getTimeString()}
     *
     * @return void
     */
    public function testGetTimeString(): void
    {
        $testFunction = DateTimeHelper::getTimeString(...);

        self::assertEquals(date(DateTimeFormats::SQL_TIME), $testFunction());
        self::assertEquals(date(DateTimeFormats::SQL_TIME), $testFunction(null));
        self::assertEquals('00:00:00', $testFunction(false));
        self::assertEquals('23:59:59', $testFunction(true));
        self::assertEquals(date(DateTimeFormats::SQL_TIME), $testFunction(time()));
        /** @psalm-suppress InvalidOperand Псалм ругается на недопустимые арифметические операции, причина, члены операций теоретически могут быть разными типами, на практике такого быть не может */
        self::assertEquals(date(DateTimeFormats::SQL_TIME), $testFunction(time() + 0.123456));
        self::assertEquals('00:00:00', $testFunction(new \DateTime('Now midnight')));
        self::assertEquals('00:00:00', $testFunction(new \DateTimeImmutable('Now midnight')));
    }

    /**
     * Test for {@covers DateTimeHelper::getTimeInt()}
     * Test for {@covers DateTimeHelper::getDaySecFromDateTime()}
     *
     * @return void
     */
    public function testGetTimeIntAndGetDaySecFromDateTime(): void
    {
        $testFunctionGetDaySecFromDateTime = DateTimeHelper::getDaySecFromDateTime(...);

        self::assertEquals(0, $testFunctionGetDaySecFromDateTime("2021-07-15 0:00:00.123567"));
        self::assertEquals(7820, $testFunctionGetDaySecFromDateTime("2021-07-15 2:10:20.123567"));

        // * * *

        $testFunctionGetTimeInt = DateTimeHelper::getTimeInt(...);

        self::assertEquals($testFunctionGetDaySecFromDateTime(), $testFunctionGetTimeInt());
        self::assertEquals(123456, $testFunctionGetTimeInt(123456));
        self::assertEquals(123456, $testFunctionGetTimeInt(123456.789));
        self::assertEquals(0, $testFunctionGetTimeInt(false));
        self::assertEquals(TimestampConstants::DAY_SEC - 1, $testFunctionGetTimeInt(true));
        self::assertEquals(0, $testFunctionGetTimeInt(new \DateTime('Now midnight')));
        self::assertEquals(0, $testFunctionGetTimeInt(new \DateTimeImmutable('Now midnight')));
    }
}
