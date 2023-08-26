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

use DraculAid\PhpTools\DateTime\Types\GetTimestampInterface;
use DraculAid\PhpTools\DateTime\Dictionary\DateTimeFormats;
use DraculAid\PhpTools\DateTime\Dictionary\TimestampConstants;

/**
 * Набор функция для облегчения работы с PHP таймштампами (кол-во секунд с 1 января 1970-го года)
 *
 * <br>--- Преобразования в другие форматы
 * <br>+ {@see TimestampHelper::toJsTimestamp()} - Преобразование в JS таймштамп
 * <br>+ {@see TimestampHelper::toString()} - Преобразование в строку (устойчив к таймштампам с микросекундами)
 * <br>--- Создание таймштампов
 * <br>+ {@see TimestampHelper::getTimestamp()} - Вернет таймштамп, из разного представления даты-времени
 * <br>+ {@see TimestampHelper::getYearDay()} - Определенного дня года
 * <br>+ {@see TimestampHelper::getMonDay()} - Определенного дня месяца
 * <br>+ {@see TimestampHelper::getWeekDay()} - Определенного дня недели
 * <br>+ {@see TimestampHelper::getFirstWeek()} - 1-го дня 1 недели года (полночь)
 * <br>+ {@see TimestampHelper::getdateArrayToTimestamp()} - Преобразует массив {@see getdate()} в таймштамп
 *
 * @see DateTimeHelper Набор функий для работы с датой и временем
 * @see DateTimeObjectHelper Набор функий для работы с объектами даты и времени
 */
final class TimestampHelper
{
    /**
     * Преобразование в JS таймштам (INT64, обычный таймштамп с милисекундами)
     *
     * @param   int|float|\DateTimeInterface    $timestamp    Таймштамп для преобразования или объект даты-времени
     * @param   bool                            $asInt64      Нужно ли вернуть в виде числа (int64)
     *
     * @return  int|string
     *
     * @throws  \RuntimeException   Если нужно вернуть ввиде числа, а версия PHP не поддерживает int64 (например, запущена на 32 битной операционной системе)
     *
     * @todo PHP8 Типизация аргументов и ответа функции
     */
    public static function toJsTimestamp($timestamp, bool $asInt64 = false)
    {
        // Если версия PHP не поддерживает int64
        if ($asInt64 && PHP_INT_SIZE < 5){
            throw new \RuntimeException('PHP run on a 32 bit OS system, Int64 not supported, use method  toJsTimestamp() with $asString = true');
        }

        // * * *

        if ($timestamp instanceof \DateTimeInterface) $timestamp = (float)$timestamp->format(DateTimeFormats::TIMESTAMP_WITH_MILLISECONDS);

        if (!$asInt64) return (string)($timestamp * 1000);
        else return $timestamp * 1000;
    }

    /**
     * Преобразует таймштамп в строку, аналогично {@see date()}, но устойчив к тому, что таймштамп может содержать микросекунды
     *
     * @link https://www.php.net/manual/ru/datetime.format.php Описание форматов преобразования таймштампа в строку
     *
     * @param   int|float   $timestamp    Таймштам (`секунды` или `сек.микросек`)
     * @param   string      $format       Формат преобразования, см https://www.php.net/manual/ru/datetime.format.php
     *
     * @return  string
     *
     * @todo PHP8 Типизация аргументов функции
     */
    public static function toString($timestamp, string $format = DateTimeFormats::FUNCTIONS): string
    {
        if (is_int($timestamp)) return date($format, $timestamp);

        if (is_float($timestamp)) return DateTimeObjectHelper::getDateObject($timestamp)->format($format);

        throw new \TypeError('$timestamp can be int or float, bat is a ' .gettype($timestamp));
    }

    /**
     * Примет дату-время в различных форматах и всегда вернет таймштамп
     *
     * @see DateTimeObjectHelper::getDateObject() Позволяяет из любого формата даты-времени получить объект даты-времени
     * @see DateTimeHelper::getDateArray() Позволяяет из любого формата даты-времени получить массив с описанием даты ({@see getdate()})
     *
     * @param   mixed    $dateTime   Дата-время в одном из представлений:
     *                               <br>+ null: текущий момент, см {@see time()}
     *                               <br>+ number: Таймштам (в секундах), на основе таймштампа будет создан объект даты-времени
     *                               <br>+ string: Строковое представление даты, см {@see date_parse()}
     *                               <br>+ array: массив с описанием даты, см {@see getdate()}
     *                               <br>+ {@see \DateTimeInterface}: Объект даты-времени, из него будет получен таймштамп
     *                               <br>+ {@see GetTimestampInterface}: Объект, поддерживающий ответ ввиде таймштампа
     *
     * @return  int
     *
     * @throws  \TypeError          Если был передан неподходящий тип данных (в случае массива, массив не имел всех необходимых полей для построения даты)
     * @throws  \RuntimeException   Если версия PHP не поддерживает int64 (например, запущена на 32 битной операционной системе)
     *
     * @todo PHP8 типизация аргументов (null|int|float|string|array|\DateTimeInterface|GetTimestampInterface)
     */
    public static function getTimestamp($dateTime = null): int
    {
        if (is_null($dateTime)) return time();

        if (is_int($dateTime)) return $dateTime;

        if (is_float($dateTime)) return intval($dateTime);

        if (is_string($dateTime)) return intval(strtotime($dateTime));

        if (is_array($dateTime)) return self::getdateArrayToTimestamp($dateTime);

        if (DateTimeObjectHelper::isGetTimestamp($dateTime, true)) return $dateTime->getTimestamp();

        throw new \TypeError('$dateTime is not correct type (it can be a number, string, array or \DateTimeInterface object)');
    }

    /**
     * Преобразует массив с описанием даты (см {@see getdate()}) в таймштамп
     *
     * @link https://www.php.net/manual/ru/function.getdate.php Описание функции getdate() на русском языке
     *
     * Параметры, имеющие значение NULL - принимают текущее значение (например, year == NULL, примет текущий год)
     *
     * @param   array  $getdateArray   Массив с представлением даты-время, описание массива {@see getdate()}
     *
     * @throws  \TypeError   Если массив не имеет необходимых полей для построения даты
     *
     * @return  int
     */
    public static function getdateArrayToTimestamp(array $getdateArray): int
    {
        if (array_key_exists('year', $getdateArray))
        {
            if ($getdateArray['year'] === null) $getdateArray['year'] = NowTimeGetter::getYear();

            if (!isset($getdateArray['hours'])) $getdateArray['hours'] = NowTimeGetter::getHour();
            if (!isset($getdateArray['minutes'])) $getdateArray['minutes'] = NowTimeGetter::getMinute();
            if (!isset($getdateArray['seconds'])) $getdateArray['seconds'] = NowTimeGetter::getSecond();

            // если есть номер дня в году
            if (array_key_exists('yday', $getdateArray))
            {
                if ($getdateArray['yday'] === null) $getdateArray['yday'] = NowTimeGetter::getYearDay() - 1;

                return strtotime("{$getdateArray['year']}-1-1 {$getdateArray['hours']}:{$getdateArray['minutes']}:{$getdateArray['seconds']} +{$getdateArray['yday']} days");
            }

            // если есть месяц и число
            if (array_key_exists('mon', $getdateArray) && array_key_exists('mday', $getdateArray))
            {
                if ($getdateArray['mon'] === null) $getdateArray['mon'] = NowTimeGetter::getMon();
                if ($getdateArray['mday'] === null) $getdateArray['mday'] = NowTimeGetter::getMonDay();

                return mktime($getdateArray['hours'], $getdateArray['minutes'], $getdateArray['seconds'], $getdateArray['mon'], $getdateArray['mday'], $getdateArray['year']);
            }
        }

        // * * *

        throw new \TypeError('$getdateArray is not correct array(' . count($getdateArray) . ')');
    }

    /**
     * Создаст таймштамп определенного дня года
     *
     * (!) Не проверяет корректность дня, это значит, что указав, к примеру 400 дней, при установке даты, вы получите +1 год
     *
     * @param   null|int   $year           Номер года (NULL - текущий год)
     * @param   null|int   $day            Номер дня года, отсчет от 1-цы (NULL - текущий год)
     * @param   mixed      $endDayOrTime   Указание времени, см {@see DateTimeHelper::getTimeString}
     *
     * @return int
     *
     * @todo PHP8 типизация аргументов функции
     */
    public static function getYearDay(?int $year, ?int $day, $endDay = false): int
    {
        $endDay = DateTimeHelper::getTimeString($endDay);

        if ($year === null) $year = NowTimeGetter::getYear();
        if ($day === null) $day = NowTimeGetter::getYearDay();

        // * * *

        return strtotime("{$year}-01-00 {$endDay} + {$day} day");
    }

    /**
     * Создаст таймштамп определенного дня месяца
     *
     * @param   null|int   $year           Номер года (NULL - текущий год)
     * @param   null|int   $mon            Номер месяца (NULL - текущий месяц)
     * @param   null|int   $day            Номер для месяца (NULL - текущий день месяца)
     * @param   mixed      $endDayOrTime   Указание времени, см {@see DateTimeHelper::getTimeString}
     *
     * @return  int
     *
     * @throws  \LogicException   Если была передан невалидная дата
     *
     * @todo PHP8 типизация аргументов функции
     */
    public static function getMonDay(?int $year, ?int $mon, ?int $day, $endDay = false): int
    {
        $endDay = DateTimeHelper::getTimeString($endDay);

        if ($year === null) $year = NowTimeGetter::getYear();
        if ($mon === null) $mon = NowTimeGetter::getMon();
        if ($day === null) $day = NowTimeGetter::getMonDay();

        if (!checkdate($mon, $day, $year)) throw new \LogicException("{$year}-{$mon}-{$day} is not valid date");

        // * * *

        return strtotime("{$year}-{$mon}-{$day} {$endDay}");
    }

    /**
     * Создаст таймштамп определенного дня недели
     *
     * Номер недели по стандарту ISO 8601, по которому первая неделя года:
     * <br>+ Неделя, содержащая 4 января
     * <br>+ Неделя, в которой 1 января это понедельник, вторник, среда или четверг
     * <br>+ Неделя, которая содержит как минимум четыре дня нового года
     * <br>Т.е. 52 неделя года может оказаться уже в "новом году" (например 1 января суббота, это будет 52 неделя и она будет относиться к предыдущему году)
     *
     * @param   null|int   $year           Номер года (NULL - текущий год)
     * @param   null|int   $week           Номер недели (NULL - текущий номер недели), Отсчет от 1
     * @param   null|int   $day            Номер для дня недели (NULL - текущий день недели), 1 понедельник ... 7 воскресенье
     * @param   mixed      $endDayOrTime   Указание времени, см {@see DateTimeHelper::getTimeString}
     *
     * @return  int
     *
     * @todo PHP8 типизация аргументов функции
     */
    public static function getWeekDay(?int $year, ?int $week, ?int $day, $endDay = false): int
    {
        $endDay = DateTimeHelper::getTimeInt($endDay);

        if ($year === null) $year = NowTimeGetter::getYear();
        if ($week === null) $week = NowTimeGetter::getWeek();
        if ($day === null) $day = NowTimeGetter::getWeekDay();

        // * * *

        return self::getFirstWeek($year)
            + ($week - 1) *  TimestampConstants::WEEK_SEC
            + ($day - 1) * TimestampConstants::DAY_SEC
            + $endDay;
    }

    /**
     * Вернет таймштап 1-го дня 1 недели года (полночь)
     *
     * Номер недели по стандарту ISO 8601, по которому первая неделя года:
     * <br>+ Неделя, содержащая 4 января
     * <br>+ Неделя, в которой 1 января это понедельник, вторник, среда или четверг
     * <br>+ Неделя, которая содержит как минимум четыре дня нового года
     * <br>Т.е. 52 неделя года может оказаться уже в "новом году" (например 1 января суббота, это будет 52 неделя и она будет относиться к предыдущему году)
     *
     * @param   null|int   $year   Номер года (NULL - текущий год)
     *
     * @return  int
     *
     * @todo PHP8 типизация аргументов функции
     */
    public static function getFirstWeek(?int $year): int
    {
        if ($year === null) $year = NowTimeGetter::getYear();

        $timestampStartYear = new \DateTime("{$year}-01-01");
        $weekDay = $timestampStartYear->format('N');

        // * * *

        if ($weekDay < 5) return $timestampStartYear->getTimestamp();
        else return $timestampStartYear->getTimestamp() + (8 - $weekDay) * TimestampConstants::DAY_SEC;
    }
}
