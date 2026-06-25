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

use DraculAid\PhpTools\DateTime\Clock\Abstraction\AbstractBigClock;
use DraculAid\PhpTools\DateTime\Dictionary\DateTimeFormats;
use PHPUnit\Framework\TestCase;

/**
 * Абстрактный класс для тестов часов {@see BigClockInterface}
 */
abstract class AbstractBigClockTestClass extends TestCase
{
    /**
     * Проверяет, как часы работают со стандартным временем, т.е. Когда нет никаких поправок
     *
     * @return void
     */
    public function testRealTime(): void
    {
        $testObject = $this->getTestRealTimeObject();

        // (!) Отличие в 1 сек не считаем проблемой, так как при сравнении реального времени,
        // между запросами времени может реально произойти сдвиг на 1 секунду
        $this->goTest($testObject, time(), 1);
    }

    /**
     * Вернет тестовый объект для проверки "реального времени" (т.е. когда поправок к времени или мок значения не существует)
     *
     * @return AbstractBigClock
     */
    abstract protected function getTestRealTimeObject(): AbstractBigClock;

    /**
     * Проводит тестирование функций
     *
     * @param   AbstractBigClock   $testObject
     * @param   int                $now
     * @param   int                $testDeltaSeconds
     *
     * @return void
     */
    protected function goTest(AbstractBigClock $testObject, int $now, int $testDeltaSeconds): void
    {
        $this->goTestFunction(fn() => $testObject->now()->getTimestamp(), $now, $testDeltaSeconds);
        $this->goTestFunction(fn() => $testObject->dateTime()->getTimestamp(), $now, $testDeltaSeconds);
        self::assertTrue($testObject->object(\DateTime::class) instanceof \DateTime);
        $this->goTestFunction(fn() => $testObject->object(\DateTime::class)->getTimestamp(), $now, $testDeltaSeconds);
        self::assertTrue($testObject->object(\DateTimeImmutable::class) instanceof \DateTimeImmutable);
        $this->goTestFunction(fn() => $testObject->object(\DateTimeImmutable::class)->getTimestamp(), $now, $testDeltaSeconds);

        $this->goTestFunction($testObject->timestamp(...), $now, $testDeltaSeconds);
        $this->goTestFunction($testObject->microtime(...), $now, $testDeltaSeconds);
        $this->goTestFunction(fn() => $testObject->timestampJs() / 1_000, $now, $testDeltaSeconds);

        $this->goTestFunction(fn() => (int)$testObject->format(DateTimeFormats::TIMESTAMP_WITH_MICROSECONDS), $now, $testDeltaSeconds);
    }

    /**
     * Тестирует конкретную функцию
     *
     * @param   callable(): numeric   $testFunction
     * @param   int                   $now
     * @param   int                   $testDeltaSeconds
     *
     * @return void
     */
    protected function goTestFunction(callable $testFunction, int $now, int $testDeltaSeconds): void
    {
        $value = $testFunction();

        self::assertTrue(is_int($value) || is_float($value));
        self::assertTrue($value >= 0, "Value must be positive, but it is {$value}");
        self::assertTrue($now - $value <= $testDeltaSeconds, "Value must be {$now} - {$testDeltaSeconds} <= {$value}");
    }
}
