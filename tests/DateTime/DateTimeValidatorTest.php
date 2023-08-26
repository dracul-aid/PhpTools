<?php declare(strict_types=1);

namespace DraculAid\PhpTools\tests\DateTime;

use DraculAid\PhpTools\DateTime\DateTimeValidator;
use DraculAid\PhpTools\DateTime\Dictionary\DateConstants;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see DateTimeValidator}
 *
 * @run php tests/run.php tests/DateTime/DateTimeValidatorTest.php
 */
class DateTimeValidatorTest extends TestCase
{
    /**
     * Test for {@see DateTimeValidator::year()}
     * Test for {@see DateTimeValidator::yearDay()}
     * Test for {@see DateTimeValidator::mon()}
     * Test for {@see DateTimeValidator::week()}
     * Test for {@see DateTimeValidator::weekDay()}
     * Test for {@see DateTimeValidator::yearDay()}
     * Test for {@see DateTimeValidator::day()}
     * Test for {@see DateTimeValidator::hour()}
     * Test for {@see DateTimeValidator::minute()}
     * Test for {@see DateTimeValidator::second()}
     */
    public function testRun(): void
    {
        self::assertTrue(DateTimeValidator::year(2050));
        self::assertTrue(DateTimeValidator::year(0));
        self::assertTrue(DateTimeValidator::year(-250));
        self::assertFalse(DateTimeValidator::year(10000));

        self::assertTrue(DateTimeValidator::yearDay(1));
        self::assertTrue(DateTimeValidator::yearDay(366));
        self::assertFalse(DateTimeValidator::yearDay(367));
        self::assertFalse(DateTimeValidator::yearDay(0));

        self::assertTrue(DateTimeValidator::mon(1));
        self::assertTrue(DateTimeValidator::mon(12));
        self::assertFalse(DateTimeValidator::mon(0));
        self::assertFalse(DateTimeValidator::mon(13));

        self::assertTrue(DateTimeValidator::week(1));
        self::assertTrue(DateTimeValidator::week(52));
        self::assertFalse(DateTimeValidator::week(0));
        self::assertFalse(DateTimeValidator::week(53));

        self::assertTrue(DateTimeValidator::weekDay(1));
        self::assertTrue(DateTimeValidator::weekDay(7));
        self::assertFalse(DateTimeValidator::weekDay(0));
        self::assertFalse(DateTimeValidator::weekDay(8));

        self::assertTrue(DateTimeValidator::day(1));
        self::assertTrue(DateTimeValidator::day(31));
        self::assertFalse(DateTimeValidator::day(0));
        self::assertFalse(DateTimeValidator::day(32));

        self::assertTrue(DateTimeValidator::hour(0));
        self::assertTrue(DateTimeValidator::hour(23));
        self::assertFalse(DateTimeValidator::hour(-1));
        self::assertFalse(DateTimeValidator::hour(24));

        self::assertTrue(DateTimeValidator::minute(0));
        self::assertTrue(DateTimeValidator::minute(59));
        self::assertFalse(DateTimeValidator::minute(-1));
        self::assertFalse(DateTimeValidator::minute(60));

        self::assertTrue(DateTimeValidator::second(0));
        self::assertTrue(DateTimeValidator::second(59));
        self::assertFalse(DateTimeValidator::second(-1));
        self::assertFalse(DateTimeValidator::second(60));
    }

    /**
     * Test for {@see DateTimeValidator::isValidDateAndTime()}
     */
    public function test(): void
    {
        self::assertTrue(DateTimeValidator::isValidDateAndTime(2023, 6, 10, 0, 0, 0));
        self::assertTrue(DateTimeValidator::isValidDateAndTime(2023, 6, 10, 23, 59, 59));

        self::assertFalse(DateTimeValidator::isValidDateAndTime(2023, 13, 10, 12, 30, 30));
        self::assertFalse(DateTimeValidator::isValidDateAndTime(2023, 0, 10, 12, 30, 30));
        self::assertFalse(DateTimeValidator::isValidDateAndTime(2023, 2, 29, 12, 30, 30));
        self::assertFalse(DateTimeValidator::isValidDateAndTime(2023, 4, 31, 12, 30, 30));
        self::assertFalse(DateTimeValidator::isValidDateAndTime(2023, 4, 15, -1, 30, 30));
        self::assertFalse(DateTimeValidator::isValidDateAndTime(2023, 4, 15, 12, -1, 30));
        self::assertFalse(DateTimeValidator::isValidDateAndTime(2023, 4, 15, 12, 30, -1));
        self::assertFalse(DateTimeValidator::isValidDateAndTime(2023, 4, 15, 24, 30, 30));
        self::assertFalse(DateTimeValidator::isValidDateAndTime(2023, 4, 15, 12, 60, 30));
        self::assertFalse(DateTimeValidator::isValidDateAndTime(2023, 4, 15, 12, 30, 60));
    }

    /**
     * Test for {@see DateTimeValidator::validMonAndDay()}
     *
     * @return void
     */
    public function testValidMonAndDay(): void
    {
        $year = 2022; $mon = 12; $day = 5;
        self::assertTrue(DateTimeValidator::validMonAndDay($year, $mon, $day));
        self::assertEquals(2022, $year);
        self::assertEquals(12, $mon);
        self::assertEquals(5, $day);

        $year = 0; $mon = 1; $day = 5;
        self::assertTrue(DateTimeValidator::validMonAndDay($year, $mon, $day));
        self::assertEquals(0, $year);
        self::assertEquals(1, $mon);
        self::assertEquals(5, $day);

        $year = -1000000; $mon = 1; $day = 1;
        self::assertFalse(DateTimeValidator::validMonAndDay($year, $mon, $day));
        self::assertEquals(-9999, $year);
        self::assertEquals(1, $mon);
        self::assertEquals(1, $day);

        $year = 2020; $mon = 0; $day = 1;
        self::assertFalse(DateTimeValidator::validMonAndDay($year, $mon, $day));
        self::assertEquals(2020, $year);
        self::assertEquals(1, $mon);
        self::assertEquals(1, $day);

        $year = 2020; $mon = 1; $day = 0;
        self::assertFalse(DateTimeValidator::validMonAndDay($year, $mon, $day));
        self::assertEquals(2020, $year);
        self::assertEquals(1, $mon);
        self::assertEquals(1, $day);

        $year = 10000; $mon = 10; $day = 15;
        self::assertFalse(DateTimeValidator::validMonAndDay($year, $mon, $day));
        self::assertEquals(9999, $year);
        self::assertEquals(10, $mon);
        self::assertEquals(15, $day);

        $year = 2020; $mon = 13; $day = 15;
        self::assertFalse(DateTimeValidator::validMonAndDay($year, $mon, $day));
        self::assertEquals(2020, $year);
        self::assertEquals(12, $mon);
        self::assertEquals(15, $day);

        $year = 2020; $mon = 12; $day = 32;
        self::assertFalse(DateTimeValidator::validMonAndDay($year, $mon, $day));
        self::assertEquals(2020, $year);
        self::assertEquals(12, $mon);
        self::assertEquals(31, $day);
    }

    /**
     * Test for {@see DateTimeValidator::validTime()}
     *
     * @return void
     */
    public function testValidTime(): void
    {
        $h = 12; $m = 30; $s = 40;
        self::assertTrue(DateTimeValidator::validTime($h, $m, $s));
        self::assertEquals(12, $h);
        self::assertEquals(30, $m);
        self::assertEquals(40, $s);

        $h = 0; $m = 0; $s = 0;
        self::assertTrue(DateTimeValidator::validTime($h, $m, $s));
        self::assertEquals(0, $h);
        self::assertEquals(0, $m);
        self::assertEquals(0, $s);

        $h = 23; $m = 59; $s = 59;
        self::assertTrue(DateTimeValidator::validTime($h, $m, $s));
        self::assertEquals(23, $h);
        self::assertEquals(59, $m);
        self::assertEquals(59, $s);

        $h = -1; $m = -1; $s = -1;
        self::assertFalse(DateTimeValidator::validTime($h, $m, $s));
        self::assertEquals(0, $h);
        self::assertEquals(0, $m);
        self::assertEquals(0, $s);

        $h = 24; $m = 60; $s = 60;
        self::assertFalse(DateTimeValidator::validTime($h, $m, $s));
        self::assertEquals(23, $h);
        self::assertEquals(59, $m);
        self::assertEquals(59, $s);
    }

    /**
     * Test for {@see DateTimeValidator::getValidDayOfMon()}
     *
     * @return void
     */
    public function testGetValidDayOfMon(): void
    {
        self::assertEquals(1, DateTimeValidator::getValidDayOfMon(2021, 6, -1));
        self::assertEquals(1, DateTimeValidator::getValidDayOfMon(2021, 6, 0));
        self::assertEquals(1, DateTimeValidator::getValidDayOfMon(2021, 6, 1));

        // * * * для месяцев, кроме февраля

        foreach (DateConstants::MON_DAY_COUNT_LIST as $mon => $maxDay)
        {
            self::assertEquals($maxDay, DateTimeValidator::getValidDayOfMon(2021, $mon, $maxDay), "Error for {$mon} mon (max {$maxDay} days, test " . $maxDay . ')');
            self::assertEquals($maxDay, DateTimeValidator::getValidDayOfMon(2021, $mon, $maxDay + 1), "Error for {$mon} mon (max {$maxDay} days, test " . ($maxDay+1) . ')');
        }

        // * * * для февраля

        self::assertEquals(28, DateTimeValidator::getValidDayOfMon(2021, 2, 28));
        self::assertEquals(28, DateTimeValidator::getValidDayOfMon(2021, 2, 29));
        self::assertEquals(28, DateTimeValidator::getValidDayOfMon(2021, 2, 30));

        self::assertEquals(28, DateTimeValidator::getValidDayOfMon(2020, 2, 28));
        self::assertEquals(29, DateTimeValidator::getValidDayOfMon(2020, 2, 29));
        self::assertEquals(29, DateTimeValidator::getValidDayOfMon(2020, 2, 30));
    }
}
