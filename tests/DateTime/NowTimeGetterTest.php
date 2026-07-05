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

use DraculAid\PhpTools\DateTime\Clock\FrozenBigClock;
use DraculAid\PhpTools\DateTime\Clock\RealBigClock;
use DraculAid\PhpTools\DateTime\Dictionary\DateTimeFormats;
use DraculAid\PhpTools\DateTime\NowTimeGetter;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@coversDefaultClass NowTimeGetter}. Это не полный тест, ввиду сложности отслеживания минут и секунд
 *
 * @run php tests/run.php tests/DateTime/NowTimeGetterTest.php
 */
class NowTimeGetterTest extends TestCase
{
    /**
     * Test for {@covers NowTimeGetter::sqlDate()}
     * Test for {@covers NowTimeGetter::sqlTime}
     * Test for {@covers NowTimeGetter::sqlDateTime}
     * Test for {@covers NowTimeGetter::getYear()}
     * Test for {@covers NowTimeGetter::getYearDay()}
     * Test for {@covers NowTimeGetter::getYearDay2()}
     * Test for {@covers NowTimeGetter::getMon}
     * Test for {@covers NowTimeGetter::getMon2}
     * Test for {@covers NowTimeGetter::getMonDay}
     * Test for {@covers NowTimeGetter::getMonDay2}
     * Test for {@covers NowTimeGetter::getWeek()}
     * Test for {@covers NowTimeGetter::getWeek2()}
     * Test for {@covers NowTimeGetter::getWeekDay()}
     * Test for {@covers NowTimeGetter::getWeekDayUSA()}
     * Test for {@covers NowTimeGetter::getHour()}
     */
    public function testRun(): void
    {
        $this->testForTime(time());

        $newClock = new FrozenBigClock(new \DateTimeImmutable('2005-12-15 23:59:59'));
        NowTimeGetter::setClock($newClock);
        $this->testForTime($newClock->timestamp());

        // Возвращаем часы, работающие с реальным временем
        NowTimeGetter::setClock(new RealBigClock());
    }

    private function testForTime(int $nowTime): void
    {
        self::assertEquals(date(DateTimeFormats::SQL_DATE, $nowTime), NowTimeGetter::sqlDate());
        self::assertEquals(date(DateTimeFormats::SQL_TIME, $nowTime), NowTimeGetter::sqlTime());
        self::assertEquals(date(DateTimeFormats::SQL_DATETIME, $nowTime), NowTimeGetter::sqlDateTime());

        $yearDayInt = getdate($nowTime)['yday'] + 1;
        $yearDayString = str_repeat('0', 3 - strlen((string)$yearDayInt)) . $yearDayInt;
        self::assertEquals(date('Y', $nowTime), NowTimeGetter::getYear());
        self::assertEquals($yearDayInt, NowTimeGetter::getYearDay());
        self::assertEquals($yearDayString, NowTimeGetter::getYearDay2());

        self::assertEquals(date('n', $nowTime), NowTimeGetter::getMon());
        self::assertEquals(date('m', $nowTime), NowTimeGetter::getMon2());
        self::assertEquals(date('j', $nowTime), NowTimeGetter::getMonDay());
        self::assertEquals(date('d', $nowTime), NowTimeGetter::getMonDay2());

        $weekInt = date('W', $nowTime);
        $weekString = str_repeat('0', 2 - strlen($weekInt)) . $weekInt;
        self::assertEquals($weekInt, NowTimeGetter::getWeek());
        self::assertEquals($weekString, NowTimeGetter::getWeek2());
        self::assertEquals(date('N', $nowTime), NowTimeGetter::getWeekDay());
        self::assertEquals(getdate($nowTime)['wday'], NowTimeGetter::getWeekDayUSA());

        self::assertEquals((int)date('G', $nowTime), NowTimeGetter::getHour());
        self::assertEquals(date('H', $nowTime), NowTimeGetter::getHour2());
    }
}
