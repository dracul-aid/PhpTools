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

use DraculAid\PhpTools\DateTime\Clock\FrozenBigClock;

/**
 * @covers FrozenBigClock
 *
 * @run php tests/run.php tests/DateTime/Clock/FrozenBigClockTest.php
 */
class FrozenBigClockTest extends AbstractBigClockTestClass
{
    /** @inheritdoc */
    protected function getTestRealTimeObject(): FrozenBigClock
    {
        return new FrozenBigClock();
    }

    /**
     * Проводим тест с замоканным временем
     *
     * @return void
     */
    public function testSetDateTime(): void
    {
        $testObject = new FrozenBigClock(null);
        $this->goTest($testObject, time(), 1);

        $dateTime = new \DateTimeImmutable('2020-02-03 04:05:06');
        $testObject = new FrozenBigClock($dateTime);
        $this->goTest($testObject, $dateTime->getTimestamp(), 0);
    }
}
