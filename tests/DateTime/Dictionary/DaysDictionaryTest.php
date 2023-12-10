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

use DraculAid\PhpTools\DateTime\Dictionary\DaysDictionary;
use DraculAid\PhpTools\ExceptionTools\ExceptionTools;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see DaysDictionary}
 *
 * @run php tests/run.php tests/DateTime/Dictionary/DaysDictionaryTest.php
 */
class DaysDictionaryTest extends TestCase
{
    public function testConstants(): void
    {
        self::assertEquals(DaysDictionary::HOURS_IN_DAY, 24);
        self::assertEquals(DaysDictionary::MINUTES_IN_DAY, 1440);
        self::assertEquals(DaysDictionary::SECONDS_IN_DAY, 86400);

        self::assertEquals(DaysDictionary::PHP_FIRST_DAY_NUMBER_IN_USA, 0);
        self::assertEquals(DaysDictionary::PHP_FIRST_DAY_NUMBER, 1);
        self::assertEquals(DaysDictionary::PHP_LAST_DAY_NUMBER_IN_USA, 6);

        for ($i = 1; $i < 7; $i++)
        {
            $constName = DaysDictionary::class . "::GETDATE_DAY_{$i}";
            self::assertEquals(constant($constName), $i, "Error value in {$constName}");
            self::assertEquals(DaysDictionary::GETDATE_DAY_TO_NUMBER_DAY[constant($constName)] ?? null, $i, "Error value in DaysDictionary::GETDATE_DAY_TO_NUMBER_DAY[{$constName}]");
        }
        self::assertEquals(DaysDictionary::GETDATE_DAY_7, 0);
        self::assertEquals(DaysDictionary::GETDATE_DAY_TO_NUMBER_DAY[DaysDictionary::GETDATE_DAY_7], 7);

        for ($i = 1; $i < 8; $i++)
        {
            $constName = DaysDictionary::class . "::DAY_{$i}";
            self::assertEquals(constant($constName), $i, "Error value in {$constName}");
        }

    }

    /**
     * Test for {@see DaysDictionary::getDayLabel()}
     * Test for {@see DaysDictionary::getDayLabelOrException()}
     *
     * @return void
     */
    public function testGetDayLabel(): void
    {
        self::assertTrue(ExceptionTools::wasCalledWithException([DaysDictionary::class, 'getDayLabel'], [1, 1], \LogicException::class));
        self::assertTrue(ExceptionTools::wasCalledWithException([DaysDictionary::class, 'getDayLabelOrException'], [1, 1], \LogicException::class));
        self::assertTrue(ExceptionTools::wasCalledWithException([DaysDictionary::class, 'getDayLabel'], [1, 4], \LogicException::class));
        self::assertTrue(ExceptionTools::wasCalledWithException([DaysDictionary::class, 'getDayLabelOrException'], [1, 4], \LogicException::class));

        self::assertFalse(DaysDictionary::getDayLabel(-1, 2));
        self::assertFalse(DaysDictionary::getDayLabel(8, 2));
        self::assertTrue(ExceptionTools::wasCalledWithException([DaysDictionary::class, 'getDayLabelOrException'], [-1, 2], \RuntimeException::class));
        self::assertTrue(ExceptionTools::wasCalledWithException([DaysDictionary::class, 'getDayLabelOrException'], [8, 2], \RuntimeException::class));
        self::assertTrue(ExceptionTools::wasCalledWithException([DaysDictionary::class, 'getDayLabelOrException'], [8, 2, \LogicException::class], \LogicException::class));

        for ($len = 2; $len < 4; $len++)
        {
            for ($day = 1; $day < 8; $day++)
            {
                $constName = DaysDictionary::class . "::CHAR{$len}_{$day}";
                self::assertEquals(constant($constName), DaysDictionary::getDayLabel($day, $len), "Error value in {$constName}");
            }
            $constName = DaysDictionary::class . "::CHAR{$len}_7";
            self::assertEquals(constant($constName), DaysDictionary::getDayLabel(0, $len), "Error value for day number 0 and size {$len}");
        }
    }
}
