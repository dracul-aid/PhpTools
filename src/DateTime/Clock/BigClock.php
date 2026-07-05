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
use DraculAid\PhpTools\tests\DateTime\Clock\BigClockTest;

/**
 * Часы, для получения времени, с возможностью указывать коррекцию выдываемого времени
 *
 * Оглавление:
 * <br>- {@see self::$correctionSeconds} - Поправка времени в секундах
 * <br>- {@see self::now()} - Вернет немутабельный объект даты времени ({@see \DateTimeImmutable})
 * <br>- {@see self::timestamp()} - Вернет таймштамп текущего времени
 * <br>- {@see self::timestampJs()} - Вернет таймштамп текущего времени в JS форматера (1 сек = 1000 мс)
 * <br>- {@see self::microtime()} - Вернет кол-во секунд и долей секунд
 * <br>- {@see self::format()} - Вернет текущую дату-время в указанном формате
 * <br>- {@see self::dateTime()} - Вернет мутабельный объект даты времени ({@see \DateTime})
 * <br>- {@see self::object()} - Вернет дату-время ввиде указанного объекта {@see \DateTimeInterface}
 *
 * Test cases for class {@see BigClockTest}
 *
 * @since 1.3.0
 */
class BigClock extends AbstractBigClock
{
    /** Коррекция времени в секундах */
    public float $correctionSeconds = 0;

    /**
     * Создаст часы с возможностью указывать коррекцию времени
     *
     * @param   float   $correctionSeconds    Коррекция времени в секундах
     */
    public function __construct(float $correctionSeconds = 0.0)
    {
        $this->correctionSeconds = $correctionSeconds;
    }

    /** @inheritdoc */
    public function microtime(): float
    {
        return microtime(true) + $this->correctionSeconds;
    }
}
