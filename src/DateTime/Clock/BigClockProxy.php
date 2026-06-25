<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\DateTime\Clock;

use DraculAid\PhpTools\DateTime\Clock\Abstraction\AbstractBigClock;
use DraculAid\PhpTools\DateTime\Clock\Abstraction\BigClockInterface;
use DraculAid\PhpTools\DateTime\Dictionary\DateTimeFormats;
use DraculAid\PhpTools\tests\DateTime\Clock\BigClockProxyTest;
use Psr\Clock\ClockInterface;

/**
 * Прокси класс для перехода от PSR-20 ({@see ClockInterface}) часов к {@see BigClockInterface}
 *
 * Оглавление:
 * <br>+{@see self::now()} - Вернет немутабельный объект даты времени ({@see \DateTimeImmutable})
 * <br>+{@see self::timestamp()} - Вернет таймштамп текущего времени
 * <br>+{@see self::timestampJs()} - Вернет таймштамп текущего времени в JS форматера (1 сек = 1000 мс)
 * <br>+{@see self::microtime()} - Вернет кол-во секунд и долей секунд
 * <br>+{@see self::format()} - Вернет текущую дату-время в указанном формате
 * <br>+{@see self::dateTime()} - Вернет мутабельный объект даты времени ({@see \DateTime})
 * <br>+{@see self::object()} - Вернет дату-время ввиде указанного объекта {@see \DateTimeInterface}
 *
 * Test cases for class {@see BigClockProxyTest}
 *
 * @since 1.3.0
 */
class BigClockProxy extends AbstractBigClock
{
    protected ClockInterface $psrClock;

    public function __construct(ClockInterface $psrClock)
    {
        $this->psrClock = $psrClock;
    }

    /** @inheritdoc */
    public function now(): \DateTimeImmutable
    {
        return clone $this->psrClock->now();
    }

    /** @inheritdoc */
    public function microtime(): float
    {
        // TODO 8.4 заменить на $this->psrClock->getMicrosecond()
        return (float)$this->psrClock->now()->format(DateTimeFormats::TIMESTAMP_WITH_MICROSECONDS);
    }
}
