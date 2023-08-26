<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests\DateTime\Dictionary;

use DraculAid\PhpTools\DateTime\Dictionary\TimestampConstants;
use PHPUnit\Framework\TestCase;


/**
 * Test for {@see TimestampConstants}
 *
 * @run php tests/run.php tests/DateTime/Dictionary/DateConstantsTest.php
 */
class TimestampConstantsTest extends TestCase
{
    /**
     * @return void
     */
    public function testRun(): void
    {
        self::assertEquals(60, TimestampConstants::MINUTE_SEC);
        self::assertEquals(60 * 60, TimestampConstants::HOUR_SEC);
        self::assertEquals(24 * 60 * 60, TimestampConstants::DAY_SEC);
        self::assertEquals(7 * 24 * 60 * 60, TimestampConstants::WEEK_SEC);
        self::assertEquals(28 * 24 * 60 * 60, TimestampConstants::MON_28_SEC);
        self::assertEquals(29 * 24 * 60 * 60, TimestampConstants::MON_29_SEC);
        self::assertEquals(30 * 24 * 60 * 60, TimestampConstants::MON_30_SEC);
        self::assertEquals(31 * 24 * 60 * 60, TimestampConstants::MON_31_SEC);
        self::assertEquals(365 * 24 * 60 * 60, TimestampConstants::YEAR_SEC);
        self::assertEquals(366 * 24 * 60 * 60, TimestampConstants::YEAR_LEAP_SEC);
    }
}
