<?php declare(strict_types=1);

namespace DraculAid\PhpTools\tests\DateTime\Types\PhpExtended;

use DraculAid\PhpTools\DateTime\Dictionary\DateTimeFormats;
use DraculAid\PhpTools\DateTime\Types\PhpExtended\DateTimeExtendedType;
use DraculAid\PhpTools\DateTime\Types\TimestampType;
use DraculAid\PhpTools\tests\AbstractProjectTestCase;

/**
 * Test for {@see DateTimeExtendedType}
 *
 * @run php tests/run.php tests/DateTime/Types/PhpExtended/DateTimeExtendedTypeTest.php
 */
class DateTimeExtendedTypeTest extends AbstractProjectTestCase
{
    /**
     * Test for {@see DateTimeExtendedType::getTimestamp()}
     *
     * @return void
     */
    public function testGetTimezoneFunctions(): void
    {
        // Список часовых поясов https://en.wikipedia.org/wiki/List_of_tz_database_time_zones
        self::assertEquals(0, (new DateTimeExtendedType('2018-09-05 1:02:08.123456 UTC'))->getTimezoneOffsetSec());
        self::assertEquals(0, (new DateTimeExtendedType('2018-09-05 1:02:08.123456 GMT'))->getTimezoneOffsetSec());
        self::assertEquals(-10*60*60, (new DateTimeExtendedType('2018-09-05 1:02:08.123456 HST'))->getTimezoneOffsetSec()); // Гавайи
        self::assertEquals(8*60*60, (new DateTimeExtendedType('2018-09-05 1:02:08.123456 +08'))->getTimezoneOffsetSec()); // Сингапур
    }

    /**
     * Test for {@see DateTimeExtendedType::getYear()}
     * Test for {@see DateTimeExtendedType::getMon()}
     * Test for {@see DateTimeExtendedType::getMonDay()}
     * Test for {@see DateTimeExtendedType::getHours()}
     * Test for {@see DateTimeExtendedType::getMinutes()}
     * Test for {@see DateTimeExtendedType::getSeconds()}
     * Test for {@see DateTimeExtendedType::getWeekDay()}
     * Test for {@see DateTimeExtendedType::getWeekDayUsa()}
     * Test for {@see DateTimeExtendedType::getWeek()}
     * Test for {@see DateTimeExtendedType::getYearDay()}
     * Test for {@see DateTimeExtendedType::getMS()}
     * Test for {@see DateTimeExtendedType::getTimeSecondFormat()}
     * Test for {@see DateTimeExtendedType::getSqlString()}
     */
    public function testGets(): void
    {
        // 5 сентября 2018 года - это "среда", 36 неделя, 247 день года
        $dateString = '2018-09-05 1:02:08.123456';
        $dateObject = new DateTimeExtendedType($dateString);

        // * * * Проверки работы функций, без аргументов (т.е. поведение по умолчанию)

        self::assertEquals(2018, $dateObject->getYear());
        self::assertEquals(9, $dateObject->getMon());
        self::assertEquals(5, $dateObject->getMonDay());
        self::assertEquals(1, $dateObject->getHours());
        self::assertEquals(2, $dateObject->getMinutes());
        self::assertEquals(8, $dateObject->getSeconds());
        self::assertEquals(3, $dateObject->getWeekDay());
        self::assertEquals(4, $dateObject->getWeekDayUsa());
        self::assertEquals(36, $dateObject->getWeek());
        self::assertEquals(248, $dateObject->getYearDay());

        // * * * Проверка работы функций, с отчетом номеров (дней, часов...) от 0-ля

        self::assertEquals(8, $dateObject->getMon(0));
        self::assertEquals(4, $dateObject->getMonDay(0));
        self::assertEquals(1, $dateObject->getHours(0));
        self::assertEquals(2, $dateObject->getMinutes(0));
        self::assertEquals(8, $dateObject->getSeconds(0));
        self::assertEquals(2, $dateObject->getWeekDay(0));
        self::assertEquals(3, $dateObject->getWeekDayUsa(0));
        self::assertEquals(35, $dateObject->getWeek(0));
        self::assertEquals(247, $dateObject->getYearDay(0));

        // * * * Проверка работы функций, с отчетом номеров (дней, часов...) от 1-цы

        self::assertEquals(9, $dateObject->getMon(1));
        self::assertEquals(5, $dateObject->getMonDay(1));
        self::assertEquals(2, $dateObject->getHours(1));
        self::assertEquals(3, $dateObject->getMinutes(1));
        self::assertEquals(9, $dateObject->getSeconds(1));
        self::assertEquals(3, $dateObject->getWeekDay(1));
        self::assertEquals(4, $dateObject->getWeekDayUsa(1));
        self::assertEquals(36, $dateObject->getWeek(1));
        self::assertEquals(248, $dateObject->getYearDay(1));

        // * * * Получение микросекунд и милисекунд

        self::assertEquals(123456, $dateObject->getMS());
        self::assertEquals(123456, $dateObject->getMS(true));
        self::assertEquals(123, $dateObject->getMS(false));
        self::assertEquals(0.123456, $dateObject->getMS(true, true));
        self::assertEquals(0.123, $dateObject->getMS(false, true));
        self::assertEquals(0, $dateObject->getMS(0));
        self::assertEquals(1, $dateObject->getMS(1));
        self::assertEquals(12, $dateObject->getMS(2));
        self::assertEquals(123456, $dateObject->getMS(8));

        // * * * преобразование в SQL строку

        $dateObject = new DateTimeExtendedType($dateString);

        self::assertEquals(date(DateTimeFormats::SQL_DATETIME, strtotime($dateString)), $dateObject->getSqlString());
        self::assertEquals(date(DateTimeFormats::SQL_DATE, strtotime($dateString)), $dateObject->getSqlString(true));
        self::assertEquals(date(DateTimeFormats::SQL_TIME, strtotime($dateString)), $dateObject->getSqlString(false));
        self::assertEquals(date(DateTimeFormats::VIEW_FOR_PEOPLE, strtotime($dateString)), $dateObject->getSqlString(DateTimeFormats::VIEW_FOR_PEOPLE));

        // * * * Кол-во секунд с начала суток

        self::assertEquals(0, (new DateTimeExtendedType('2020-06-03 0:00:00'))->getTimeSecondFormat());
        self::assertEquals(30, (new DateTimeExtendedType('2020-06-03 0:00:30'))->getTimeSecondFormat());
        self::assertEquals(90, (new DateTimeExtendedType('2020-06-03 0:01:30'))->getTimeSecondFormat());
        self::assertEquals(2*60*60 + 5*60 + 20, (new DateTimeExtendedType('2020-06-03 2:05:20'))->getTimeSecondFormat());
    }

    /**
     * Test for {@see DateTimeExtendedType::setDateValues()}
     *
     * @todo Добавить тесты, с високосным годом, Добавить тесты с сменой месяца в котором другое кол-во дней
     */
    public function testSets(): void
    {
        // 5 сентября 2018 года - это "среда", 36 неделя, 247 день года
        $dateObject = new DateTimeExtendedType('2018-09-05 1:02:08.123456');

        $dateObject->setDateValues();
        self::assertEquals('2018-09-05 01:02:08.123456 ' . $dateObject->getTimezone()->getName(), (string)$dateObject);

        // * * *

        // 5 сентября 2018 года - это "среда", 36 неделя, 247 день года
        $dateObject = new DateTimeExtendedType('2018-09-05 1:02:08.123456');

        $dateObject->setDateValues(null);
        self::assertEquals('2018-09-05 01:02:08.123456 ' . $dateObject->getTimezone()->getName(), (string)$dateObject);
        $dateObject->setDateValues(12);
        self::assertEquals('2018-09-12 01:02:08.123456 ' . $dateObject->getTimezone()->getName(), (string)$dateObject);
        $dateObject->setDateValues(0);
        self::assertEquals('2018-09-01 01:02:08.123456 ' . $dateObject->getTimezone()->getName(), (string)$dateObject);
        $dateObject->setDateValues(35);
        self::assertEquals('2018-09-30 01:02:08.123456 ' . $dateObject->getTimezone()->getName(), (string)$dateObject);

        // * * *

        // 5 сентября 2018 года - это "среда", 36 неделя, 247 день года
        $dateObject = new DateTimeExtendedType('2018-09-21 01:02:08.123456');

        $dateObject->setDateValues(null, null);
        self::assertEquals('2018-09-21 01:02:08.123456 ' . $dateObject->getTimezone()->getName(), (string)$dateObject);
        $dateObject->setDateValues(null, 6);
        self::assertEquals('2018-06-21 01:02:08.123456 ' . $dateObject->getTimezone()->getName(), (string)$dateObject);
        $dateObject->setDateValues(null, 0);
        self::assertEquals('2018-01-21 01:02:08.123456 ' . $dateObject->getTimezone()->getName(), (string)$dateObject);
        $dateObject->setDateValues(null, null);
        self::assertEquals('2018-01-21 01:02:08.123456 ' . $dateObject->getTimezone()->getName(), (string)$dateObject);
        $dateObject->setDateValues(null, null);
        self::assertEquals('2018-01-21 01:02:08.123456 ' . $dateObject->getTimezone()->getName(), (string)$dateObject);
        $dateObject->setDateValues(null, null, 2017);
        self::assertEquals('2017-01-21 01:02:08.123456 ' . $dateObject->getTimezone()->getName(), (string)$dateObject);
    }

    /**
     * Test for {@see DateTimeExtendedType::set()}
     *
     */
    public function testSet(): void
    {
        // 5 сентября 2018 года - это "среда", 36 неделя, 247 день года
        $dateObject = new DateTimeExtendedType('2018-09-05 1:02:08');
        self::assertEquals('2018-09-05 01:02:08', $dateObject->format(DateTimeFormats::VIEW_FOR_PEOPLE));

        $dateObject->set('2019-09-05 01:02:08');
        self::assertEquals('2019-09-05 01:02:08', $dateObject->format(DateTimeFormats::VIEW_FOR_PEOPLE));

        $dateObject->set(strtotime('2017-09-05 01:02:08'));
        self::assertEquals('2017-09-05 01:02:08', $dateObject->format(DateTimeFormats::VIEW_FOR_PEOPLE));

        $dateObject->set(getdate(strtotime('2017-09-05 01:02:08')));
        self::assertEquals('2017-09-05 01:02:08', $dateObject->format(DateTimeFormats::VIEW_FOR_PEOPLE));

        $dateObject->set(null);
        self::assertEquals(time(), $dateObject->getTimestamp());

        $dateObject->set(new \DateTime('2017-09-05 01:02:08'));
        self::assertEquals('2017-09-05 01:02:08', $dateObject->format(DateTimeFormats::VIEW_FOR_PEOPLE));

        $dateObject->set(new TimestampType('2017-09-05 01:02:08'));
        self::assertEquals('2017-09-05 01:02:08', $dateObject->format(DateTimeFormats::VIEW_FOR_PEOPLE));
    }

    /**
     * Test for {@see DateTimeExtendedType::setWeekDay()}
     *
     * @todo Добавить проверки на разные стартовые дни года
     */
    public function testSetWeekDay(): void
    {
        // 5 сентября 2018 года - это "среда", 36 неделя, 247 день года
        $dateObject = new DateTimeExtendedType('2018-09-05 02:02:08');

        $dateObject->setWeekDay(null, null, null);
        self::assertEquals('2018-09-05 02:02:08', $dateObject->format(DateTimeFormats::VIEW_FOR_PEOPLE));

        $dateObject->setWeekDay(null, null, 1);
        self::assertEquals('2018-09-03 02:02:08', $dateObject->format(DateTimeFormats::VIEW_FOR_PEOPLE));

        $dateObject->setWeekDay(null, 37, null);
        self::assertEquals('2018-09-10 02:02:08', $dateObject->format(DateTimeFormats::VIEW_FOR_PEOPLE));

        $dateObject->setWeekDay(null, 38, 3);
        self::assertEquals('2018-09-19 02:02:08', $dateObject->format(DateTimeFormats::VIEW_FOR_PEOPLE));

        $dateObject->setWeekDay(null, 37, 10);
        self::assertEquals('2018-09-19 02:02:08', $dateObject->format(DateTimeFormats::VIEW_FOR_PEOPLE));

        $dateObject->setWeekDay(2018, 37, 10);
        self::assertEquals('2018-09-19 02:02:08', $dateObject->format(DateTimeFormats::VIEW_FOR_PEOPLE));

        $dateObject->setWeekDay(2023, 1, 1, false);
        self::assertEquals('2023-01-02 00:00:00', $dateObject->format(DateTimeFormats::VIEW_FOR_PEOPLE));

        $dateObject->setWeekDay(2022, 1, 1, true);
        self::assertEquals('2022-01-03 23:59:59', $dateObject->format(DateTimeFormats::VIEW_FOR_PEOPLE));
    }

    /**
     * Test for {@see DateTimeExtendedType::moveWeek()}
     */
    public function testMoveWeek(): void
    {
        // 5 сентября 2018 года - это "среда", 36 неделя, 247 день года
        $dateObject = new DateTimeExtendedType('2018-09-05 02:02:08');

        $dateObject->moveWeek(2);
        self::assertEquals('2018-09-19 02:02:08', $dateObject->format(DateTimeFormats::VIEW_FOR_PEOPLE));

        $dateObject->moveWeek(2);
        self::assertEquals('2018-10-03 02:02:08', $dateObject->format(DateTimeFormats::VIEW_FOR_PEOPLE));

        $dateObject->moveWeek(-1);
        self::assertEquals('2018-09-26 02:02:08', $dateObject->format(DateTimeFormats::VIEW_FOR_PEOPLE));

        // * * *

        // 5 сентября 2018 года - это "среда", 36 неделя, 247 день года
        $dateObject = new DateTimeExtendedType('2018-09-05 02:02:08');

        $dateObject->moveWeek(0, 1);
        self::assertEquals('2018-09-03 02:02:08', $dateObject->format(DateTimeFormats::VIEW_FOR_PEOPLE));

        $dateObject->moveWeek(0, 10);
        self::assertEquals('2018-09-12 02:02:08', $dateObject->format(DateTimeFormats::VIEW_FOR_PEOPLE));

        $dateObject->moveWeek(-1, 2, false);
        self::assertEquals('2018-09-04 00:00:00', $dateObject->format(DateTimeFormats::VIEW_FOR_PEOPLE));
        $dateObject->moveWeek(0, 1, true);
        self::assertEquals('2018-09-03 23:59:59', $dateObject->format(DateTimeFormats::VIEW_FOR_PEOPLE));
    }
}
