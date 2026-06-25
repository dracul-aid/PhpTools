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
use DraculAid\PhpTools\DateTime\Dictionary\DateTimeFormats;
use DraculAid\PhpTools\tests\DateTime\Clock\FrozenBigClockTest;

/**
 * Часы, всегда возвращающие одно и то же время, идеально подходят для тестов
 *
 * Оглавление:
 * <br>+{@see self::$frozenDateTime} - Значение "Замороженного времени"
 * <br>+{@see self::now()} - Вернет немутабельный объект даты времени ({@see \DateTimeImmutable})
 * <br>+{@see self::timestamp()} - Вернет таймштамп текущего времени
 * <br>+{@see self::timestampJs()} - Вернет таймштамп текущего времени в JS форматера (1 сек = 1000 мс)
 * <br>+{@see self::microtime()} - Вернет кол-во секунд и долей секунд
 * <br>+{@see self::format()} - Вернет текущую дату-время в указанном формате
 * <br>+{@see self::dateTime()} - Вернет мутабельный объект даты времени ({@see \DateTime})
 * <br>+{@see self::object()} - Вернет дату-время ввиде указанного объекта {@see \DateTimeInterface}
 *
 * Test cases for class {@see FrozenBigClockTest}
 *
 * @since 1.3.0
 */
class FrozenBigClock extends AbstractBigClock
{
    /** Значение даты времени "Замороженное" для часов */
    public null|\DateTimeInterface $frozenDateTime;

    /**
     * Создаст часы, которые всегда возвращают одно и то же время
     *
     * @param   null|\DateTimeInterface   $frozenDateTime   Замороженное значение для часов, NULL - часы вернут текущее время
     */
    public function __construct(null|\DateTimeInterface $frozenDateTime = null)
    {
        $this->frozenDateTime = $frozenDateTime;
    }

    /** @inheritdoc */
    public function microtime(): float
    {
        if ($this->frozenDateTime === null) return microtime(true);

        // TODO 8.4 заменить на $this->frozenDateTime->getMicrosecond()
        return (float)$this->frozenDateTime->format(DateTimeFormats::TIMESTAMP_WITH_MICROSECONDS);
    }
}
