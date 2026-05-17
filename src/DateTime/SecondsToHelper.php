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

use DraculAid\PhpTools\DateTime\Dictionary\DaysDictionary;
use DraculAid\PhpTools\DateTime\Dictionary\TimestampConstants;
use DraculAid\PhpTools\tests\DateTime\SecondsToHelperTest;

/**
 * Класс, для преобразования секунд в удобно читаемое время
 *
 * Оглавление:
 * <br>{@see SecondsToHelper::getMinutes()} Вернет целое кол-во минут в переданных секундах (0 и более минут)
 * <br>{@see SecondsToHelper::getHours()} Вернет целое кол-во часов в переданных секундах (0 и более часов)
 * <br>{@see SecondsToHelper::minutesAndSeconds()} Получит секунды и вернет кол-во целых минут и секунд
 * <br>{@see SecondsToHelper::time()} Получит секунды и вернет кол-во целых часов, минут и секунд
 * <br>{@see SecondsToHelper::timeAndDays()} Получит секунды и вернет кол-во целых дней, часов, минут и секунд
 *
 * Test cases for class {@see SecondsToHelperTest}
 */
final class SecondsToHelper
{
    /**
     * Вернет целое кол-во минут в переданных секундах (0 и более минут), с округлением минут вниз
     *
     * (!) Также может использоваться для получения часов из минут
     *
     * @param   int<0, max>   $seconds   Кол-во секунд
     *
     * @return  int<0, max>
     */
    public static function getMinutes(int $seconds): int
    {
        return abs((int)floor($seconds / TimestampConstants::MINUTE_SEC));
    }

    /**
     * Вернет целое кол-во часов в переданных секундах (0 и более часов), с округлением часов вниз
     *
     * @param   int<0, max>   $seconds   Кол-во секунд
     *
     * @return  int<0, max>
     */
    public static function getHours(int $seconds): int
    {
        return abs((int)floor($seconds / TimestampConstants::HOUR_SEC));
    }

    /**
     * Получит секунды и вернет кол-во целых минут и секунд
     *
     * @param   int<0, max>|float   $seconds
     *
     * @return  list{0: int<0, max>, 1: int<0, 60>, 2: int<0, max>}   Вернет массив [минуты, секунды, дробная часть секунды]
     *
     * @psalm-suppress MoreSpecificReturnType Функция всегда вернет именно указанный в return диапазон
     * @psalm-suppress LessSpecificReturnStatement -//-
     */
    public static function minutesAndSeconds(int|float $seconds): array
    {
        if (is_float($seconds)) {
            $microseconds = (int)explode('.', (string)$seconds)[1];
            $seconds = (int)$seconds;
        } else $microseconds = 0;

        $minutes = self::getMinutes($seconds);

        return [
            $minutes,
            $seconds - $minutes * TimestampConstants::MINUTE_SEC,
            $microseconds,
        ];
    }

    /**
     * Получит секунды и вернет кол-во целых часов, минут и секунд
     *
     * @param   int<0, max>|float   $seconds
     *
     * @return  list{0: int<0, max>, 1: int<0, 60>, 2: int<0, 60>, 3: int<0, max>}   Вернет массив [часы, минуты, секунды, дробная часть секунды]
     *
     * @psalm-suppress MoreSpecificReturnType Функция всегда вернет именно указанный в return диапазон
     * @psalm-suppress LessSpecificReturnStatement -//-
     */
    public static function time(int|float $seconds): array
    {
        if (is_float($seconds)) {
            $microseconds = (int)explode('.', (string)$seconds)[1];
            $seconds = (int)$seconds;
        } else $microseconds = 0;

        // (!) Помните, что минут в часах, столько же, сколько и секунд в минуте...
        $minutes = self::getMinutes($seconds);
        $hours = self::getMinutes($minutes);

        return [
            $hours,
            $minutes - $hours * TimestampConstants::MINUTE_SEC,
            $seconds - $minutes * TimestampConstants::MINUTE_SEC,
            $microseconds,
        ];
    }

    /**
     * Получит секунды и вернет кол-во целых дней, часов, минут и секунд
     *
     * @param   int<0, max>|float    $seconds
     *
     * @return  list{0: int<0, max>, 1: int<0, 23>, 2: int<0, 60>, 3: int<0, 60>, 4: int<0, max>}   Вернет массив [дни, часы, минуты, секунды, дробная часть секунды]
     *
     * @psalm-suppress MoreSpecificReturnType Функция всегда вернет именно указанный в return диапазон
     * @psalm-suppress LessSpecificReturnStatement -//-
     */
    public static function timeAndDays(int|float $seconds): array
    {
        [$hours, $minutes, $seconds, $microseconds] = self::time($seconds);

        $days = (int)floor($hours / DaysDictionary::HOURS_IN_DAY);
        $hours = $hours - $days * DaysDictionary::HOURS_IN_DAY;

        return [
            $days,
            $hours,
            $minutes,
            $seconds,
            $microseconds,
        ];
    }
}
