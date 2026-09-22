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

use DraculAid\PhpTools\DateTime\SecondsToHelper;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@coversDefaultClass SecondsToHelper}
 *
 * @run php tests/run.php tests/DateTime/SecondsToHelperTest.php
 */
class SecondsToHelperTest extends TestCase
{
    /**
     * Test for {@covers SecondsToHelper::getMinutes()}
     * Test for {@covers SecondsToHelper::getHours()}
     *
     * @return void
     */
    public function testRun(): void
    {
        $testFunctionGetMinutes = SecondsToHelper::getMinutes(...);

        self::assertEquals(0, $testFunctionGetMinutes(0));
        self::assertEquals(0, $testFunctionGetMinutes(59));
        self::assertEquals(1, $testFunctionGetMinutes(60));
        self::assertEquals(1, $testFunctionGetMinutes(119));
        self::assertEquals(2, $testFunctionGetMinutes(120));

        // * * *

        $testFunctionGetHours = SecondsToHelper::getHours(...);

        self::assertEquals(0, $testFunctionGetHours(0));
        self::assertEquals(0, $testFunctionGetHours(60));
        self::assertEquals(0, $testFunctionGetHours(1 * 60 * 60 - 1));
        self::assertEquals(1, $testFunctionGetHours(1 * 60 * 60));
    }

    /**
     * Test for {@covers SecondsToHelper::minutesAndSeconds()}
     * Test for {@covers SecondsToHelper::time()}
     * Test for {@covers SecondsToHelper::timeAndDays()}
     *
     * @return void
     *
     * @psalm-suppress InvalidOperand PSALM ругается на то, что к int прибавляется float
     */
    public function runGetInts(): void
    {
        $testFunctionMinutesAndSeconds = SecondsToHelper::minutesAndSeconds(...);

        self::assertEquals([0, 0, 0], $testFunctionMinutesAndSeconds(0));
        self::assertEquals([0, 59, 0], $testFunctionMinutesAndSeconds(59));
        self::assertEquals([1, 0, 0], $testFunctionMinutesAndSeconds(60));
        self::assertEquals([1, 59, 0], $testFunctionMinutesAndSeconds(119));
        self::assertEquals([2, 0, 0], $testFunctionMinutesAndSeconds(120));
        self::assertEquals([2, 0, 0], $testFunctionMinutesAndSeconds(120.0));
        self::assertEquals([2, 0, 123], $testFunctionMinutesAndSeconds(120.123));
        self::assertEquals([2, 0, 12235523], $testFunctionMinutesAndSeconds(120.12235523));
        self::assertEquals([2, 0, 000001], $testFunctionMinutesAndSeconds(120.000001));

        // * * *

        $testFunctionTime = SecondsToHelper::time(...);

        self::assertEquals([0, 0, 0, 0], $testFunctionTime(0));
        self::assertEquals([0, 0, 59, 0], $testFunctionTime(59));
        self::assertEquals([0, 1, 0, 0], $testFunctionTime(60));
        self::assertEquals([0, 1, 59, 0], $testFunctionTime(119));
        self::assertEquals([0, 2, 1, 0], $testFunctionTime(2 * 60 + 1));
        self::assertEquals([0, 59, 58, 0], $testFunctionTime(60 * 60 - 2));
        self::assertEquals([1, 1, 2, 0], $testFunctionTime(60 * 60 + 62));
        self::assertEquals([24, 2, 3, 0], $testFunctionTime(24 * 60 * 60 + 123));
        self::assertEquals([24, 2, 3, 12], $testFunctionTime(24 * 60 * 60 + 123.12));
        self::assertEquals([24, 2, 3, 12235523], $testFunctionTime(24 * 60 * 60 + 123.12235523));
        self::assertEquals([24, 2, 3, 000001], $testFunctionTime(24 * 60 * 60 + 123.000001));

        // * * *

        $testFunctionTimeAndDays = SecondsToHelper::timeAndDays(...);

        self::assertEquals([0, 0, 0, 0, 0], $testFunctionTimeAndDays(0));
        self::assertEquals([0, 0, 0, 59, 0], $testFunctionTimeAndDays(59));
        self::assertEquals([0, 0, 1, 0, 0], $testFunctionTimeAndDays(60));
        self::assertEquals([0, 0, 1, 59, 0], $testFunctionTimeAndDays(119));
        self::assertEquals([0, 0, 2, 1, 0], $testFunctionTimeAndDays(2 * 60 + 1));
        self::assertEquals([0, 0, 59, 58, 0], $testFunctionTimeAndDays(60 * 60 - 2));
        self::assertEquals([0, 1, 1, 2, 0], $testFunctionTimeAndDays(60 * 60 + 62));
        self::assertEquals([0, 23, 2, 3, 0], $testFunctionTimeAndDays(23 * 60 * 60 + 123));
        self::assertEquals([1, 0, 2, 3, 0], $testFunctionTimeAndDays(24 * 60 * 60 + 123));
        self::assertEquals([1, 23, 2, 3, 0], $testFunctionTimeAndDays(47 * 60 * 60 + 123));
        self::assertEquals([2, 0, 2, 3, 0], $testFunctionTimeAndDays(48 * 60 * 60 + 123));
        self::assertEquals([1, 0, 2, 3, 12], $testFunctionTimeAndDays(24 * 60 * 60 + 123.12));
        self::assertEquals([1, 0, 2, 3, 12235523], $testFunctionTimeAndDays(24 * 60 * 60 + 123.12235523));
        self::assertEquals([1, 0, 2, 3, 000001], $testFunctionTimeAndDays(24 * 60 * 60 + 123.000001));
    }

    /**
     * Test for {@covers SecondsToHelper::minutesAndSecondsAsString()}
     * Test for {@covers SecondsToHelper::timeAsString()}
     *
     * @return void
     *
     * @psalm-suppress InvalidOperand PSALM ругается на то, что к int прибавляется float
     */
    public function runGetString(): void
    {
        $testFunctionMinutesAndSecondsAsString = SecondsToHelper::minutesAndSecondsAsString(...);

        self::assertEquals('00:00', $testFunctionMinutesAndSecondsAsString(0));
        self::assertEquals('00:59', $testFunctionMinutesAndSecondsAsString(59));
        self::assertEquals('01:00', $testFunctionMinutesAndSecondsAsString(60));
        self::assertEquals('01:59', $testFunctionMinutesAndSecondsAsString(119));
        self::assertEquals('02:00', $testFunctionMinutesAndSecondsAsString(120));
        self::assertEquals('02:00', $testFunctionMinutesAndSecondsAsString(120.0));
        self::assertEquals('02:00.123', $testFunctionMinutesAndSecondsAsString(120.123));
        self::assertEquals('02:00.12235523', $testFunctionMinutesAndSecondsAsString(120.12235523));
        self::assertEquals('02:00.000001', $testFunctionMinutesAndSecondsAsString(120.000001));

        // * * *

        $testFunctionTimeAsString = SecondsToHelper::timeAsString(...);

        self::assertEquals('00:00:00', $testFunctionTimeAsString(0));
        self::assertEquals('00:00:59', $testFunctionTimeAsString(59));
        self::assertEquals('00:01:00', $testFunctionTimeAsString(60));
        self::assertEquals('00:01:59', $testFunctionTimeAsString(119));
        self::assertEquals('00:02:01', $testFunctionTimeAsString(2 * 60 + 1));
        self::assertEquals('00:59:58', $testFunctionTimeAsString(60 * 60 - 2));
        self::assertEquals('01:01:02', $testFunctionTimeAsString(60 * 60 + 62));
        self::assertEquals('02:02:03', $testFunctionTimeAsString(24 * 60 * 60 + 123));
        self::assertEquals('02:02:03.12', $testFunctionTimeAsString(24 * 60 * 60 + 123.12));
        self::assertEquals('02:02:03.12235523', $testFunctionTimeAsString(24 * 60 * 60 + 123.12235523));
        self::assertEquals('02:02:03.000001', $testFunctionTimeAsString(24 * 60 * 60 + 123.000001));
    }
}
