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

use DraculAid\PhpTools\DateTime\Dictionary\DateConstants;
use PHPUnit\Framework\TestCase;


/**
 * Test for {@see DateConstants}
 *
 * @run php tests/run.php tests/DateTime/Dictionary/DateConstantsTest.php
 */
class DateConstantsTest extends TestCase
{
    /**
     * @return void
     */
    public function testRun(): void
    {
        self::assertEquals(28, DateConstants::MON_28);
        self::assertEquals(29, DateConstants::MON_29);
        self::assertEquals(30, DateConstants::MON_30);
        self::assertEquals(31, DateConstants::MON_31);

        self::assertEquals(365, DateConstants::YEAR_DAYS);
        self::assertEquals(366, DateConstants::YEAR_LEAP_DAYS);

        self::assertEquals(7, DateConstants::WEEK_DAYS);

        // * * *

        self::assertCount(12, DateConstants::MON_DAY_COUNT_LIST);

        self::assertCount(7, DateConstants::MON_31_DAY_LIST);
        foreach (DateConstants::MON_31_DAY_LIST as $numberMon)
        {
            self::assertTrue(
                isset(DateConstants::MON_DAY_COUNT_LIST[$numberMon])
                && DateConstants::MON_DAY_COUNT_LIST[$numberMon] === DateConstants::MON_31,
                "Fail for {$numberMon} mon"
            );
        }

        self::assertCount(4, DateConstants::MON_30_DAY_LIST);
        foreach (DateConstants::MON_30_DAY_LIST as $numberMon)
        {
            self::assertTrue(
                isset(DateConstants::MON_DAY_COUNT_LIST[$numberMon])
                && DateConstants::MON_DAY_COUNT_LIST[$numberMon] === DateConstants::MON_30,
                "Fail for {$numberMon} mon"
            );
        }

        self::assertCount(5, DateConstants::MON_SHORT_LIST);
        foreach (DateConstants::MON_SHORT_LIST as $numberMon)
        {
            self::assertTrue(
                isset(DateConstants::MON_DAY_COUNT_LIST[$numberMon])
                && DateConstants::MON_DAY_COUNT_LIST[$numberMon] === DateConstants::MON_30 || DateConstants::MON_DAY_COUNT_LIST[$numberMon] === DateConstants::MON_28,
                "Fail for {$numberMon} mon"
            );
        }
    }
}
