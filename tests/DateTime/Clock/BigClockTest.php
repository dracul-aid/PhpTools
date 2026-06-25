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

use DraculAid\PhpTools\DateTime\Clock\BigClock;

/**
 * @covers BigClock
 *
 * @run php tests/run.php tests/DateTime/Clock/BigClockTest.php
 */
class BigClockTest extends AbstractBigClockTestClass
{
    /** @inheritdoc */
    protected function getTestRealTimeObject(): BigClock
    {
        return new BigClock();
    }

    /**
     * Проводим тест с поправкой времени
     *
     * @return void
     */
    public function testSetSeconds(): void
    {
        $testObject = new BigClock(0);
        $this->goTest($testObject, time(), 1);

        $testObject = new BigClock(1000);
        $this->goTest($testObject, time() + 1000, 1);

        $testObject = new BigClock(-1000);
        $this->goTest($testObject, time() - 1000, 1);

        $testObject = new BigClock(1000.9999);
        $this->goTest($testObject, time() + 1000, 1);
    }
}
