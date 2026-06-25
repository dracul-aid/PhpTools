<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests\DateTime\Clock;

use DraculAid\PhpTools\DateTime\Clock\RealBigClock;

/**
 * @covers RealBigClock
 *
 * @run php tests/run.php tests/DateTime/Clock/RealBigClockTest.php
 */
class RealBigClockTest extends AbstractBigClockTestClass
{
    /** @inheritdoc */
    protected function getTestRealTimeObject(): RealBigClock
    {
        return new RealBigClock();
    }
}
