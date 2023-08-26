<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\DateTime;

use DraculAid\PhpTools\DateTime\Dictionary\DateTimeFormats;
use DraculAid\PhpTools\DateTime\Dictionary\TimestampConstants;

/**
 * Полезные функции для работы с датой-временем
 *
 * Оглавление:
 * <br> {@see DateTimeHelper::getDateArray()} -  Вернет массив с описанием даты-времени (аналогичный {@see getdate()} для даты представленной в любом формате
 * <br> {@see DateTimeHelper::getTimezoneOffsetSec()} - Вернет смещение часового пояса в секундах для текущего установленного часового пояса PHP
 * <br> {@see DateTimeHelper::getTimeString()} - Вернет время ввиде строки, вне зависимости от переданного типа (ЧЧ-ММ-СС)
 * <br> {@see DateTimeHelper::getTimeInt()} - Вернет время ввиде числа (кол-во секунд с начала суток)
 * <br> {@see DateTimeHelper::getDaySecFromDateTime()} - Вернет для переданной даты кол-во секунд с начала суток
 * <br> {@see DateTimeHelper::isValidDateArray()} - Проверяет массив, соответствует ли он массиву-ответу функции {@see getdate()}
 *
 * @see DateTimeObjectHelper Хэлпер, для работы с датой-времени, как с объектами
 * @see TimestampHelper Хэлпер, для работы с таймштампами
 */
final class DateTimeHelper
{
    /**
     * Вернет массив с описанием даты-времени (аналогичный {@see getdate()} для даты представленной в любом формате
     *
     * @link https://www.php.net/manual/ru/function.getdate.php Описание массива даты-времени и PHP функции getdate()
     * @see TimestampHelper::getTimestamp() Позволяяет из любого формата даты-времени получить таймштамп
     * @see DateTimeObjectHelper::getDateObject() Позволяяет из любого формата даты-времени получить объект даты-времени
     *
     * @param   mixed    $dateTime   Дата-время в одном из представлений:
     *                               <br>+ null: текущий момент, см {@see time()}
     *                               <br>+ number: Таймштам (в секундах), на основе таймштампа будет создан объект даты-времени
     *                               <br>+ string: Строковое представление даты, см {@see date_parse()}
     *                               <br>+ array: массив с описанием даты, см {@see getdate()}
     *                               <br>+ {@see \DateTimeInterface}: Объект даты-времени
     *                               <br>+ {@see GetTimestampInterface}: Объект, поддерживающий ответ ввиде таймштампа
     *
     * @return array{year: int, mon: int, yday: int, mday: int, wday: int, hours: int, minutes: int, seconds: int, month: string, weekday: string, 0: int}
     *
     * @todo PHP8 типизация аргументов (null|int|float|string|array|\DateTimeInterface)
     */
    public static function getDateArray($dateTime = null): array
    {
        if (is_array($dateTime) && self::isValidDateArray($dateTime)) return $dateTime;

        return getdate(TimestampHelper::getTimestamp($dateTime));
    }

    /**
     * Вернет смещение часового пояса в секундах
     *
     * Вернет отрицательное число для запада (Америка) и положительное для востока (Европа, Азия)
     *
     * @param   null|string   $timeZone   Имя часового пояса (например, MSK), если NULL - текущий часовой пояс PHP
     *
     * @return  int
     *
     * @todo PHP8 типизация аргументов функции
     */
    public static function getTimezoneOffsetSec(?string $timeZone = null): int
    {
        if ($timeZone === null) return (int)Date('Z');
        else return strtotime('NOW UTC') - strtotime("NOW {$timeZone}");
    }

    /**
     * Вернет время ввиде строки, вне зависимости от переданного типа (ЧЧ-ММ-СС)
     *
     * (!) В случае преобразования времени в строку, оно будет преобразовано в формат совместимый с SQL, см ({@see DateTimeFormats::SQL_TIME})
     *
     * @see DateTimeHelper::getTimeInt() Вернет время ввиде числа (кол-во секунд с начала суток)
     *
     * @param   mixed  $time   Время в одном из форматов:
     *                         <br>+ null: Текущее время
     *                         <br>+ false: Начало суток
     *                         <br>+ true: Конец суток
     *                         <br>+ int: Таймштамп для получения времени
     *                         <br>+ string: Строка с временем (вернется в неизменном виде)
     *                         <br>+ \DateTimeInterface: объект даты-времени
     *
     * @return  string
     *
     * @todo PHP8 типизация аргументов функции (null|bool|int|float|string|\DateTimeInterface)
     */
    public static function getTimeString($time = null): string
    {
        if (is_string($time)) return $time;

        // * * *

        if ($time === null) return date(DateTimeFormats::SQL_TIME);

        if ($time === false) return '00:00:00';

        if ($time === true) return '23:59:59';

        // * * *

        return date(DateTimeFormats::SQL_TIME, TimestampHelper::getTimestamp($time));
    }

    /**
     * Вернет время ввиде числа (кол-во секунд с начала суток)
     *
     * @see DateTimeHelper::getTimeString() Вернет время ввиде строки, вне зависимости от переданного типа (ЧЧ-ММ-СС)
     * @see DateTimeHelper::getDaySecFromDateTime() Вернет для переданной даты кол-во секунд с начала суток
     *
     * @param   mixed  $time   Время в одном из форматов:
     *                         <br>+ null: Текущее время
     *                         <br>+ false: Начало суток
     *                         <br>+ true: Конец суток
     *                         <br>+ int|float: Вернет как есть (float преобразует к целому числу)
     *                         <br>+ string: Строка с временем (вернется в неизменном виде)
     *                         <br>+ {@see \DateTimeInterface}: объект даты-времени
     *
     * @return  int
     *
     * @todo PHP8 типизация аргументов функции (null|bool|int|float|string|\DateTimeInterface)
     */
    public static function getTimeInt($time = null): int
    {
        if (is_int($time)) return $time;

        if (is_float($time)) return (int)$time;

        // * * *

        if ($time === false) return 0;

        if ($time === true) return TimestampConstants::DAY_SEC - 1;

        // * * *

        return self::getDaySecFromDateTime($time);
    }

    /**
     * Вернет для переданной даты кол-во секунд с начала суток
     *
     * @see DateTimeHelper::getTimeInt() Вернет время ввиде числа (кол-во секунд с начала суток)
     *
     * @param   mixed   $dateTime   Дата-время в любом представлении, см {@see DateTimeHelper::getDateArray()}
     *
     * @return  int
     *
     * @todo PHP8 типизация аргументов (null|int|float|string|array|\DateTimeInterface)
     */
    public static function getDaySecFromDateTime($dateTime = null): int
    {
        $dateArray = self::getDateArray($dateTime);

        return $dateArray['hours'] * TimestampConstants::HOUR_SEC
            + $dateArray['minutes'] * TimestampConstants::MINUTE_SEC
            + $dateArray['seconds'];
    }

    /**
     * Проверяет массив, соответствует ли он массиву-ответу функции {@see getdate()}
     *
     * (!) Функция не проверяет корректность значений
     *
     * @see DateTimeValidator Валидатор даты и времени
     * @link https://www.php.net/manual/ru/function.getdate.php Описание массива даты-времени и PHP функции getdate()
     *
     * @param   array  $testArray   Массив для анализа
     *
     * @return  bool
     *
     * @todo Добавить проверку корректности значений (добавить аргумент bool $validateValues = false)
     */
    public static function isValidDateArray(array $testArray): bool
    {
        if (count($testArray) !== 11) return false;

        return array_key_exists('year', $testArray)
            && array_key_exists('mon', $testArray)
            && array_key_exists('yday', $testArray)
            && array_key_exists('mday', $testArray)
            && array_key_exists('wday', $testArray)
            && array_key_exists('hours', $testArray)
            && array_key_exists('minutes', $testArray)
            && array_key_exists('seconds', $testArray)
            && array_key_exists('month', $testArray)
            && array_key_exists('weekday', $testArray)
            && array_key_exists(0, $testArray);
    }
}
