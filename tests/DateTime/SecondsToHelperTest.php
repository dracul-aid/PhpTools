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
 * Test for {@see SecondsToHelper}
 *
 * @run php tests/run.php tests/DateTime/SecondsToHelperTest.php
 */
class SecondsToHelperTest extends TestCase
{
    /**
     * Test for {@see SecondsToHelper::getMinutes()}
     * Test for {@see SecondsToHelper::getHours()}
     *
     * @return void
     */
    public function testRun(): void
    {
        self::assertEquals(0, SecondsToHelper::getMinutes(0));
        self::assertEquals(0, SecondsToHelper::getMinutes(59));
        self::assertEquals(1, SecondsToHelper::getMinutes(60));
        self::assertEquals(1, SecondsToHelper::getMinutes(119));
        self::assertEquals(2, SecondsToHelper::getMinutes(120));

        self::assertEquals(0, SecondsToHelper::getHours(0));
        self::assertEquals(0, SecondsToHelper::getHours(60));
        self::assertEquals(0, SecondsToHelper::getHours(1 * 60 * 60 - 1));
        self::assertEquals(1, SecondsToHelper::getHours(1 * 60 * 60));
    }

    /**
     * Test for {@see SecondsToHelper::minutesAndSeconds()}
     * Test for {@see SecondsToHelper::time()}
     * Test for {@see SecondsToHelper::timeAndDays()}
     *
     * @return void
     *
     * @psalm-suppress InvalidOperand PSALM ругается на то, что к int прибавляется float
     */
    public function runGetInts(): void
    {
        self::assertEquals([0, 0, 0], SecondsToHelper::minutesAndSeconds(0));
        self::assertEquals([0, 59, 0], SecondsToHelper::minutesAndSeconds(59));
        self::assertEquals([1, 0, 0], SecondsToHelper::minutesAndSeconds(60));
        self::assertEquals([1, 59, 0], SecondsToHelper::minutesAndSeconds(119));
        self::assertEquals([2, 0, 0], SecondsToHelper::minutesAndSeconds(120));
        self::assertEquals([2, 0, 0], SecondsToHelper::minutesAndSeconds(120.0));
        self::assertEquals([2, 0, 123], SecondsToHelper::minutesAndSeconds(120.123));
        self::assertEquals([2, 0, 12235523], SecondsToHelper::minutesAndSeconds(120.12235523));
        self::assertEquals([2, 0, 000001], SecondsToHelper::minutesAndSeconds(120.000001));

        self::assertEquals([0, 0, 0, 0], SecondsToHelper::time(0));
        self::assertEquals([0, 0, 59, 0], SecondsToHelper::time(59));
        self::assertEquals([0, 1, 0, 0], SecondsToHelper::time(60));
        self::assertEquals([0, 1, 59, 0], SecondsToHelper::time(119));
        self::assertEquals([0, 2, 1, 0], SecondsToHelper::time(2 * 60 + 1));
        self::assertEquals([0, 59, 58, 0], SecondsToHelper::time(60 * 60 - 2));
        self::assertEquals([1, 1, 2, 0], SecondsToHelper::time(60 * 60 + 62));
        self::assertEquals([24, 2, 3, 0], SecondsToHelper::time(24 * 60 * 60 + 123));
        self::assertEquals([24, 2, 3, 12], SecondsToHelper::time(24 * 60 * 60 + 123.12));
        self::assertEquals([24, 2, 3, 12235523], SecondsToHelper::time(24 * 60 * 60 + 123.12235523));
        self::assertEquals([24, 2, 3, 000001], SecondsToHelper::time(24 * 60 * 60 + 123.000001));

        self::assertEquals([0, 0, 0, 0, 0], SecondsToHelper::timeAndDays(0));
        self::assertEquals([0, 0, 0, 59, 0], SecondsToHelper::timeAndDays(59));
        self::assertEquals([0, 0, 1, 0, 0], SecondsToHelper::timeAndDays(60));
        self::assertEquals([0, 0, 1, 59, 0], SecondsToHelper::timeAndDays(119));
        self::assertEquals([0, 0, 2, 1, 0], SecondsToHelper::timeAndDays(2 * 60 + 1));
        self::assertEquals([0, 0, 59, 58, 0], SecondsToHelper::timeAndDays(60 * 60 - 2));
        self::assertEquals([0, 1, 1, 2, 0], SecondsToHelper::timeAndDays(60 * 60 + 62));
        self::assertEquals([0, 23, 2, 3, 0], SecondsToHelper::timeAndDays(23 * 60 * 60 + 123));
        self::assertEquals([1, 0, 2, 3, 0], SecondsToHelper::timeAndDays(24 * 60 * 60 + 123));
        self::assertEquals([1, 23, 2, 3, 0], SecondsToHelper::timeAndDays(47 * 60 * 60 + 123));
        self::assertEquals([2, 0, 2, 3, 0], SecondsToHelper::timeAndDays(48 * 60 * 60 + 123));
        self::assertEquals([1, 0, 2, 3, 12], SecondsToHelper::timeAndDays(24 * 60 * 60 + 123.12));
        self::assertEquals([1, 0, 2, 3, 12235523], SecondsToHelper::timeAndDays(24 * 60 * 60 + 123.12235523));
        self::assertEquals([1, 0, 2, 3, 000001], SecondsToHelper::timeAndDays(24 * 60 * 60 + 123.000001));
    }

    /**
     * Test for {@see SecondsToHelper::minutesAndSecondsAsString()}
     * Test for {@see SecondsToHelper::timeAsString()}
     *
     * @return void
     *
     * @psalm-suppress InvalidOperand PSALM ругается на то, что к int прибавляется float
     */
    public function runGetString(): void
    {
        self::assertEquals('00:00', SecondsToHelper::minutesAndSecondsAsString(0));
        self::assertEquals('00:59', SecondsToHelper::minutesAndSecondsAsString(59));
        self::assertEquals('01:00', SecondsToHelper::minutesAndSecondsAsString(60));
        self::assertEquals('01:59', SecondsToHelper::minutesAndSecondsAsString(119));
        self::assertEquals('02:00', SecondsToHelper::minutesAndSecondsAsString(120));
        self::assertEquals('02:00', SecondsToHelper::minutesAndSecondsAsString(120.0));
        self::assertEquals('02:00.123', SecondsToHelper::minutesAndSecondsAsString(120.123));
        self::assertEquals('02:00.12235523', SecondsToHelper::minutesAndSecondsAsString(120.12235523));
        self::assertEquals('02:00.000001', SecondsToHelper::minutesAndSecondsAsString(120.000001));

        self::assertEquals('00:00:00', SecondsToHelper::timeAsString(0));
        self::assertEquals('00:00:59', SecondsToHelper::timeAsString(59));
        self::assertEquals('00:01:00', SecondsToHelper::timeAsString(60));
        self::assertEquals('00:01:59', SecondsToHelper::timeAsString(119));
        self::assertEquals('00:02:01', SecondsToHelper::timeAsString(2 * 60 + 1));
        self::assertEquals('00:59:58', SecondsToHelper::timeAsString(60 * 60 - 2));
        self::assertEquals('01:01:02', SecondsToHelper::timeAsString(60 * 60 + 62));
        self::assertEquals('02:02:03', SecondsToHelper::timeAsString(24 * 60 * 60 + 123));
        self::assertEquals('02:02:03.12', SecondsToHelper::timeAsString(24 * 60 * 60 + 123.12));
        self::assertEquals('02:02:03.12235523', SecondsToHelper::timeAsString(24 * 60 * 60 + 123.12235523));
        self::assertEquals('02:02:03.000001', SecondsToHelper::timeAsString(24 * 60 * 60 + 123.000001));
    }
}
