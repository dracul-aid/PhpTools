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
     * Вернет целое кол-во минут в переданных секундах (0 и более минут)
     *
     * (!) Также может использоваться для получения часов из минут
     *
     * @param   int   $seconds   Кол-во секунд
     *
     * @return  int
     */
    public static function getMinutes(int $seconds): int
    {
        return (int)floor($seconds / TimestampConstants::MINUTE_SEC);
    }

    /**
     * Вернет целое кол-во часов в переданных секундах (0 и более часов)
     *
     * @param   int   $seconds   Кол-во секунд
     *
     * @return  int
     */
    public static function getHours(int $seconds): int
    {
        return (int)floor($seconds / TimestampConstants::HOUR_SEC);
    }

    /**
     * Получит секунды и вернет кол-во целых минут и секунд
     *
     * @param   int   $seconds
     *
     * @return  int[]   Вернет массив [минуты, секунды]
     */
    public static function minutesAndSeconds(int $seconds): array
    {
        $minutes = self::getMinutes($seconds);

        return [
            $minutes,
            $seconds - $minutes * TimestampConstants::MINUTE_SEC,
        ];
    }

    /**
     * Получит секунды и вернет кол-во целых часов, минут и секунд
     *
     * @param   int   $seconds
     *
     * @return  int[]   Вернет массив [часы, минуты, секунды]
     */
    public static function time(int $seconds): array
    {
        // (!) Помните, что минут в часах, столько же, сколько и секунд в минуте...

        $minutes = self::getMinutes($seconds);
        $hours = self::getMinutes($minutes);

        return [
            $hours,
            $minutes - $hours * TimestampConstants::MINUTE_SEC,
            $seconds - $minutes * TimestampConstants::MINUTE_SEC,
        ];
    }

    /**
     * Получит секунды и вернет кол-во целых дней, часов, минут и секунд
     *
     * @param   int    $seconds
     *
     * @return  int[]   Вернет массив [дни, часы, минуты, секунды]
     */
    public static function timeAndDays(int $seconds): array
    {
        [$hours, $minutes, $seconds] = self::time($seconds);

        $days = (int)floor($hours / DaysDictionary::HOURS_IN_DAY);
        $hours = $hours - $days * DaysDictionary::HOURS_IN_DAY;

        return [
            $days,
            $hours,
            $minutes,
            $seconds,
        ];
    }
}
