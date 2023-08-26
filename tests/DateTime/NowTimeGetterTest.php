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

use DraculAid\PhpTools\DateTime\NowTimeGetter;
use DraculAid\PhpTools\DateTime\Dictionary\DateTimeFormats;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see NowTimeGetter}. Это не полный тест, ввиду сложности отслеживания минут и секунд
 *
 * @run php tests/run.php tests/DateTime/NowTimeGetterTest.php
 */
class NowTimeGetterTest extends TestCase
{
    /**
     * Test for {@see NowTimeGetter::sqlDate()}
     * Test for {@see NowTimeGetter::sqlTime}
     * Test for {@see NowTimeGetter::sqlDateTime}
     * Test for {@see NowTimeGetter::getYear()}
     * Test for {@see NowTimeGetter::getYearDay()}
     * Test for {@see NowTimeGetter::getYearDay2()}
     * Test for {@see NowTimeGetter::getMon}
     * Test for {@see NowTimeGetter::getMon2}
     * Test for {@see NowTimeGetter::getMonDay}
     * Test for {@see NowTimeGetter::getMonDay2}
     * Test for {@see NowTimeGetter::getWeek()}
     * Test for {@see NowTimeGetter::getWeek2()}
     * Test for {@see NowTimeGetter::getWeekDay()}
     * Test for {@see NowTimeGetter::getWeekDayUSA()}
     * Test for {@see NowTimeGetter::getHour()}
     */
    public function testRun(): void
    {
        $nowTime = time();

        self::assertEquals(date(DateTimeFormats::SQL_DATE, $nowTime), NowTimeGetter::sqlDate());
        self::assertEquals(date(DateTimeFormats::SQL_TIME, $nowTime), NowTimeGetter::sqlTime());
        self::assertEquals(date(DateTimeFormats::SQL_DATETIME, $nowTime), NowTimeGetter::sqlDateTime());

        $yearDayInt = (int)getdate($nowTime)['yday'] + 1;
        $yearDayString = str_repeat('0', 3 - strlen((string)$yearDayInt)) . $yearDayInt;
        self::assertEquals(date('Y', $nowTime), NowTimeGetter::getYear());
        self::assertEquals($yearDayInt, NowTimeGetter::getYearDay());
        self::assertEquals($yearDayString, NowTimeGetter::getYearDay2());

        self::assertEquals(date('n', $nowTime), NowTimeGetter::getMon());
        self::assertEquals(date('m', $nowTime), NowTimeGetter::getMon2());
        self::assertEquals(date('j', $nowTime), NowTimeGetter::getMonDay());
        self::assertEquals(date('d', $nowTime), NowTimeGetter::getMonDay2());

        $weekInt = date('W', $nowTime);
        $weekString = str_repeat('0', 2 - strlen((string)$weekInt)) . $weekInt;
        self::assertEquals($weekInt, NowTimeGetter::getWeek());
        self::assertEquals($weekString, NowTimeGetter::getWeek2());
        self::assertEquals(date('N', $nowTime), NowTimeGetter::getWeekDay());
        self::assertEquals((int)getdate($nowTime)['wday'], NowTimeGetter::getWeekDayUSA());

        self::assertEquals((int)date('G', $nowTime), NowTimeGetter::getHour());
        self::assertEquals(date('H', $nowTime), NowTimeGetter::getHour2());



    }
}
