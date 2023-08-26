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
use DraculAid\PhpTools\DateTime\Types\TimestampType;
use DraculAid\PhpTools\tests\AbstractProjectTestCase;

/**
 * Test for {@see TimestampType}
 *
 * @run php tests/run.php tests/DateTime/Types/TimestampTest.php
 */
class TimestampTypeTest extends AbstractProjectTestCase
{
    /**
     * Test for {@see TimestampType::__construct()}
     * Test for {@see TimestampType::__toString()}
     * Test for {@see TimestampType::setTimestamp()}
     * Test for {@see TimestampType::getTimestamp()}
     * Test for {@see TimestampType::format()}
     *
     * @return void
     */
    public function testRun(): void
    {
        $timeObject = new \DateTime('2023-06-15 12:30:30.123456');

        self::assertTimestamp($timeObject->getTimestamp(), (new TimestampType('2023-06-15 12:30:30'))->getTimestamp());
        self::assertTimestamp($timeObject->getTimestamp(), (new TimestampType('2023-06-15 12:30:30.123456'))->getTimestamp());
        self::assertTimestamp($timeObject->getTimestamp(), (new TimestampType($timeObject->getTimestamp()))->getTimestamp());
        self::assertTimestamp($timeObject->getTimestamp(), (new TimestampType(getdate($timeObject->getTimestamp())))->getTimestamp());

        // * * *

        $testObject = new TimestampType('2023-06-15 12:30:30.123456');
        self::assertTimestamp(strtotime('2023-06-15 12:30:30'), $testObject->getTimestamp());

        $testTimestamp = strtotime('2022-02-21 10:20:30');
        $testObject->setTimestamp('2022-02-21 10:20:30');
        self::assertTimestamp($testTimestamp, $testObject->getTimestamp());

        // * * *

        $testObject = new TimestampType('2023-06-15 12:30:30');
        self::assertEquals('2023-06-15', $testObject->format(DateTimeFormats::SQL_DATE));
        self::assertEquals('2023-06-15 12:30:30', $testObject->format(DateTimeFormats::SQL_DATETIME));

        self::assertEquals($testObject->format(DateTimeFormats::FUNCTIONS), $testObject->__toString());
    }
}
