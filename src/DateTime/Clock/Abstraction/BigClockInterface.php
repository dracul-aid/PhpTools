<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\DateTime\Clock\Abstraction;

use DraculAid\PhpTools\DateTime\Dictionary\DateTimeFormats;
use Psr\Clock\ClockInterface;

/**
 * Интерфейс, расширяющий часы PSR-20 ({@see ClockInterface})
 *
 * Используется для создания компонентов возвращающих текущее время (возможно, с заменой "текущего времени" на что-то)
 *
 * Основные реализации
 * <br>+ {@see BigClock} - Часы с возможностью установить поправку в секундах
 * <br>+ {@see FrozenBigClock} - Часы с возможностью "заморозить" время (идеально подходят для тестов)
 * <br>+ {@see RealBigClock} - Часы, без возможности повлиять на возвращаемое время
 * <br>+ {@see BigClockProxy} - Прокси-класс для использования {@see \Psr\Clock\ClockInterface} которые уже есть на проекте
 *
 * Оглавление:
 * <br>- {@see self::now()} - Вернет немутабельный объект даты времени ({@see \DateTimeImmutable})
 * <br>- {@see self::timestamp()} - Вернет таймштамп текущего времени
 * <br>- {@see self::timestampJs()} - Вернет таймштамп текущего времени в JS форматера (1 сек = 1000 мс)
 * <br>- {@see self::microtime()} - Вернет кол-во секунд и долей секунд
 * <br>- {@see self::format()} - Вернет текущую дату-время в указанном формате
 * <br>- {@see self::dateTime()} - Вернет мутабельный объект даты времени ({@see \DateTime})
 * <br>- {@see self::object()} - Вернет дату-время ввиде указанного объекта {@see \DateTimeInterface}
 *
 * @since 1.3.0
 */
interface BigClockInterface extends ClockInterface
{
    /**
     * Вернет время в Timestamp формате
     *
     * @return int<0, max>
     */
    public function timestamp(): int;

    /**
     * Вернет таймштам в формате пригодном для JS (1 сек = 1000 мс)
     *
     * @return int<0, max>
     */
    public function timestampJs(): int;

    /**
     * Вернет кол-во секунд и долей секунд
     *
     * @return float
     */
    public function microtime(): float;

    /**
     * Вернет время в виде отформатированной строки
     *
     * @see DateTimeFormats Примеры форматов даты-времени
     *
     * @param   string   $format   Параметры форматирования, см {@link https://www.php.net/manual/ru/datetime.format.php#refsect1-datetime.format-parameters}
     *
     * @return string
     */
    public function format(string $format): string;

    /**
     * Вернет дату-время в {@see \DateTime} объекта
     *
     * @return \DateTime
     */
    public function dateTime(): \DateTime;

    /**
     * Вернет дату-время в объекте указанного класса
     *
     * @param   class-string<\DateTimeInterface>   $className
     *
     * @return \DateTimeInterface
     *
     * @psalm-template T of \DateTimeInterface
     * @psalm-param class-string<T> $className
     * @psalm-return T
     */
    public function object(string $className): \DateTimeInterface;
}
