<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests\DateTime\Types\Ranges;

use DraculAid\PhpTools\DateTime\Dictionary\DateTimeFormats;
use DraculAid\PhpTools\DateTime\Types\Ranges\DateTimeExtendedRangeType;
use DraculAid\PhpTools\DateTime\Types\Ranges\DateTimeRangeType;
use DraculAid\PhpTools\DateTime\Types\Ranges\TimestampRangeType;
use DraculAid\PhpTools\tests\AbstractProjectTestCase;

/**
 * Test for {@see DateTimeExtendedRangeType}
 *
 * @run php tests/run.php tests/DateTime/Types/Ranges/DateTimeRangeTypeTest.php
 */
class DateTimeExtendedRangeTypeTest extends AbstractProjectTestCase
{
    /**
     * Test for {@see DateTimeRangeType::__construct()}
     * * * *
     * Test for {@see DateTimeRangeType::create()}
     * Test for {@see DateTimeRangeType::startGetTimestamp()}
     * Test for {@see DateTimeRangeType::finishGetTimestamp()}
     * Test for {@see DateTimeRangeType::startGetString()}
     * Test for {@see DateTimeRangeType::finishGetString()}
     * * * *
     * Test for {@see DateTimeRangeType::startClear()}
     * Test for {@see DateTimeRangeType::finishClear()}
     * Test for {@see DateTimeRangeType::isSet()}
     * * * *
     * Test for {@see DateTimeRangeType::startSet()}
     * Test for {@see DateTimeRangeType::finishSet()}
     * Test for {@see DateTimeRangeType::getSqlDateTime}
     * * * *
     * Test for {@see DateTimeRangeType::getLenght()}
     *
     * @return void
     */
    public function testRun(): void
    {
        $testRange = new DateTimeRangeType();
        self::assertNull($testRange->start);
        self::assertNull($testRange->finish);

        // * * *

        $testRange = DateTimeRangeType::create('2023-06-15 12:30:30.123', '2023-06-16 12:30:30.123');
        self::assertTimestamp(strtotime('2023-06-15 12:30:30'), $testRange->start->getTimestamp());
        self::assertTimestamp(strtotime('2023-06-16 12:30:30'), $testRange->finish->getTimestamp());
        self::assertTimestamp(strtotime('2023-06-15 12:30:30'), $testRange->startGetTimestamp());
        self::assertTimestamp(strtotime('2023-06-16 12:30:30'), $testRange->finishGetTimestamp());
        self::assertEquals('2023-06-15 12:30:30', $testRange->startGetString(DateTimeFormats::SQL_DATETIME));
        self::assertEquals('2023-06-16 12:30:30', $testRange->finishGetString(DateTimeFormats::SQL_DATETIME));

        // * * *

        self::assertEquals(true, $testRange->isSet());
        $testRange->startClear();
        self::assertEquals(-1, $testRange->isSet());
        $testRange->finishClear();
        self::assertEquals(false, $testRange->isSet());
        $testRange->startSet(123123);
        self::assertEquals(1, $testRange->isSet());

        // * * *

        $testRange = new DateTimeRangeType();
        self::assertEquals("", $testRange->getSqlDateTime('`date_column`'));

        $testRange->startSet('2023-06-15 12:30:30');
        self::assertEquals(" `date_column` >= '2023-06-15 12:30:30' ", $testRange->getSqlDateTime('`date_column`'));
        self::assertEquals(" `date_column` >= '12:30:30' ", $testRange->getSqlDateTime('`date_column`', DateTimeFormats::SQL_TIME));
        self::assertEquals(" `date_column` >= \"12:30:30\" ", $testRange->getSqlDateTime('`date_column`', DateTimeFormats::SQL_TIME, "\""));

        $testRange->finishSet('2023-06-16 12:30:30');
        self::assertEquals(" `date_column` BETWEEN '2023-06-15 12:30:30' AND '2023-06-16 12:30:30' ", $testRange->getSqlDateTime('`date_column`'));

        $testRange = DateTimeRangeType::create('2022-06-15 12:30:30', '2022-06-16 12:30:30');
        self::assertEquals(" `date_column` BETWEEN '2022-06-15 12:30:30' AND '2022-06-16 12:30:30' ", $testRange->getSqlDateTime('`date_column`'));

        // * * *

        $testRange = new DateTimeRangeType();
        self::assertEquals(0, $testRange->getLenght());

        $testRange->finishSet(1000);
        self::assertEquals(0, $testRange->getLenght());
        $testRange->startSet(2000);
        self::assertEquals(1000, $testRange->getLenght());
        $testRange->finishSet(4000);
        self::assertEquals(2000, $testRange->getLenght());
        $testRange->finishSet(0);
        self::assertEquals(2000, $testRange->getLenght());
        $testRange->startSet(0);
        $testRange->finishSet(500);
        self::assertEquals(500, $testRange->getLenght());
        $testRange->finish = null;
        self::assertEquals(0, $testRange->getLenght());

        // * * *

        $testRange = TimestampRangeType::create('2023-06-15 12:30:30.123', '2023-06-16 13:30:30.123');

        self::assertEquals('2023-06-15 12:30:30 - 2023-06-16 13:30:30', $testRange->getString());
        self::assertEquals('2023-06-15 12:30:30=2023-06-16 13:30:30', $testRange->getString(null, '='));
        self::assertEquals('12:30:30 - 13:30:30', $testRange->getString(false));
        self::assertEquals('2023-06-15 - 2023-06-16', $testRange->getString(true));
        self::assertEquals('2023=06=15---2023=06=16', $testRange->getString('Y=m=d', '---'));
    }
}
