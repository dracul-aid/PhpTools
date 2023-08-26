<?php declare(strict_types=1);

namespace DraculAid\PhpTools\tests\DateTime\Types\Ranges;

use DraculAid\PhpTools\DateTime\Dictionary\DateTimeFormats;
use DraculAid\PhpTools\DateTime\Types\Ranges\TimestampRangeType;
use DraculAid\PhpTools\tests\AbstractProjectTestCase;

/**
 * Test for {@see TimestampRangeType}
 *
 * @run php tests/run.php tests/DateTime/Types/Ranges/TimestampRangeTypeTest.php
 */
class TimestampRangeTypeTest extends AbstractProjectTestCase
{
    /**
     * Test for {@see TimestampRangeType::__construct()}
     * * * *
     * Test for {@see TimestampRangeType::create()}
     * Test for {@see TimestampRangeType::startGetTimestamp()}
     * Test for {@see TimestampRangeType::finishGetTimestamp()}
     * Test for {@see TimestampRangeType::startGetString()}
     * Test for {@see TimestampRangeType::finishGetString()}
     * * * *
     * Test for {@see TimestampRangeType::startClear()}
     * Test for {@see TimestampRangeType::finishClear()}
     * Test for {@see TimestampRangeType::isSet()}
     * * * *
     * Test for {@see TimestampRangeType::startSet()}
     * Test for {@see TimestampRangeType::finishSet()}
     * Test for {@see TimestampRangeType::getSqlDateTime}
     * * * *
     * Test for {@see TimestampRangeType::getLenght()}
     * 
     * @return void
     */
    public function testRun(): void
    {
        $testRange = new TimestampRangeType();
        self::assertNull($testRange->start);
        self::assertNull($testRange->finish);

        // * * *

        $testRange = TimestampRangeType::create('2023-06-15 12:30:30.123', '2023-06-16 12:30:30.123');
        self::assertTimestamp(strtotime('2023-06-15 12:30:30'), $testRange->start);
        self::assertTimestamp(strtotime('2023-06-16 12:30:30'), $testRange->finish);
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
        $testRange->start = 123123123;
        self::assertEquals(1, $testRange->isSet());

        // * * *

        $testRange = new TimestampRangeType();
        self::assertEquals("", $testRange->getSqlDateTime('`date_column`'));

        $testRange->startSet('2023-06-15 12:30:30');
        self::assertEquals(" `date_column` >= '2023-06-15 12:30:30' ", $testRange->getSqlDateTime('`date_column`'));
        self::assertEquals(" `date_column` >= '12:30:30' ", $testRange->getSqlDateTime('`date_column`', DateTimeFormats::SQL_TIME));
        self::assertEquals(" `date_column` >= \"12:30:30\" ", $testRange->getSqlDateTime('`date_column`', DateTimeFormats::SQL_TIME, "\""));

        $testRange->finishSet('2023-06-16 12:30:30');
        self::assertEquals(" `date_column` BETWEEN '2023-06-15 12:30:30' AND '2023-06-16 12:30:30' ", $testRange->getSqlDateTime('`date_column`'));

        $testRange = TimestampRangeType::create('2022-06-15 12:30:30', '2022-06-16 12:30:30');
        self::assertEquals(" `date_column` BETWEEN '2022-06-15 12:30:30' AND '2022-06-16 12:30:30' ", $testRange->getSqlDateTime('`date_column`'));

        // * * *

        $testRange = new TimestampRangeType();
        self::assertEquals(0, $testRange->getLenght());

        $testRange->finishSet(1000);
        self::assertEquals(0, $testRange->getLenght());
        $testRange->startSet(2000);
        self::assertEquals(1000, $testRange->getLenght());
        $testRange->finish = 4000;
        self::assertEquals(2000, $testRange->getLenght());
        $testRange->finish = 0;
        self::assertEquals(2000, $testRange->getLenght());
        $testRange->start = 0;
        $testRange->finish = 500;
        self::assertEquals(500, $testRange->getLenght());
        $testRange->finish = null;
        self::assertEquals(0, $testRange->getLenght());
    }
}
