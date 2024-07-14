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

use DraculAid\PhpTools\DateTime\Dictionary\DateConstants;
use DraculAid\PhpTools\tests\DateTime\DateTimeValidatorTest;

/**
 * Набор функций для валидации частей даты-времени
 *
 * Оглавление:
 * <br> {@see DateTimeValidator::isValidDateAndTime()} - Проверяет валидность даты и времени (по григорианскому календарю)
 * --- Вернет ближайшие валидные значения
 * <br> {@see DateTimeValidator::validMonAndDay()} Проверит, валиден ли год, месяц и число. Если нет, обновит переданные данные до валидных значений
 * <br> {@see DateTimeValidator::validTime()} - Проверит, валиден ли час, минута и секунда. Если нет - вернет ближайшие валидные значения
 * <br> {@see DateTimeValidator::getValidDayOfMon()} Проверит, валиден ли число (день месяца), если нет - вернет ближайшей валидный номер дня
 * <br>--- Проверка частей даты
 * <br> {@see DateTimeValidator::year()} - Год
 * <br> {@see DateTimeValidator::mon()} - Номер месяца
 * <br> {@see DateTimeValidator::week()} - Неделя
 * <br> {@see DateTimeValidator::yearDay()} - День года
 * <br> {@see DateTimeValidator::weekDay()} - День недели
 * <br> {@see DateTimeValidator::day()} - Число (день месяца)
 * <br>--- Проверка частей времени
 * <br> {@see DateTimeValidator::hour()} - Часы
 * <br> {@see DateTimeValidator::minute()} - Минуты
 * <br> {@see DateTimeValidator::second()} - Секунды
 *
 * @see DateTimeHelper::isValidDateArray() Проверяет валидность массива {@see getdate()}
 *
 * Test cases for class {@see DateTimeValidatorTest}
 */
final class DateTimeValidator
{
    /**
     * Проверяет валидность даты и времени (по григорианскому календарю)
     *
     * @see checkdate() Для проверки только даты
     * @see DateTimeHelper::isValidDateArray() Проверяет валидность массива {@see getdate()}
     *
     * @param   int   $year     Текущий год (например, 2018)
     * @param   int   $mon      Номер месяца (1 - 12)
     * @param   int   $day      Номер дня месяца (1 - 31)
     * @param   int   $hour     Час (0 - 23)
     * @param   int   $minute   Минута (0 - 60)
     * @param   int   $second   Секунда (0 - 60)
     *
     * @return  bool
     */
    public static function isValidDateAndTime(int $year, int $mon, int $day, int $hour, int $minute, int $second): bool
    {
        if (!checkdate($mon, $day, $year)) return false;

        return $hour >=0 && $hour < 24
            && $minute >= 0 && $minute < 60
            && $second >= 0 && $second < 60;
    }

    /**
     * Проверит, валиден ли год, месяц и число. Если нет, обновит переданные данные до валидных значений
     *
     * @param   int  &$_year   Год (4 цифры, максимум 9999, минимум -9999)
     * @param   int  &$_mon    Номер месяца (1-12)
     * @param   int  &$_day    Номер для в месяце (1-31)
     *
     * @return  bool   Вернет TRUE если все данные были валидны, или FALSE если были не валидны и была проведена коррекция
     */
    public static function validMonAndDay(int &$_year, int &$_mon, int &$_day): bool
    {
        $_return = true;

        if ($_year > 9999) $_year = 9999 and $_return = false;
        elseif ($_year < -9999) $_year = -9999 and $_return = false;

        if ($_mon < 1) $_mon = 1 and $_return = false;
        elseif ($_mon > 12) $_mon = 12 and $_return = false;

        $validDay = self::getValidDayOfMon($_year, $_mon, $_day);
        if ($validDay !== $_day)
        {
            $_day = $validDay;
            $_return = false;
        }

        // * * *

        return $_return;
    }

    /**
     * Проверяет, валиден ли день для указанного месяца, или нет. Если не валиден - вернет ближайший валидный номер дня
     *
     * (!) Для 0-ля и отрицательных значений вернет "1"
     * <br>(!) Для значений превышающих кол-во дней в месяце, вернет последний день в месяце
     *
     * @param   int   $year   Год (4 цифры)
     * @param   int   $mon    Номер месяца (1-12)
     * @param   int   $day    Номер для в месяце (1-31)
     *
     * @return  int
     */
    public static function getValidDayOfMon(int $year, int $mon, int $day): int
    {
        if ($day > 0 && $day < 29) return $day;

        if ($day < 1) return 1;

        // * * * Для месяцев, кроме февраля

        if ($mon !== 2)
        {
            return $day > DateConstants::MON_DAY_COUNT_LIST[$mon]
                ? DateConstants::MON_DAY_COUNT_LIST[$mon]
                : $day;
        }

        // * * * Для февраля

        if ($day > 28 && checkdate($mon, 29, $year)) return 29;
        else return 28;
    }

    /**
     * Проверит, валиден ли час, минута и секунда. Если нет - вернет ближайшие валидные значения
     *
     * @param   int   &$_hour      Час (0-23)
     * @param   int   &$_minute    Минута (0-59)
     * @param   int   &$_second    Секунда (0-59)
     *
     * @return  bool   Вернет TRUE если все данные были валидны, или FALSE если были не валидны и была проведена коррекция
     */
    public static function validTime(int &$_hour, int &$_minute, int &$_second): bool
    {
        $_return = true;

        if ($_hour < 0) $_hour = 0 or $_return = false;
        elseif ($_hour > 23) $_hour = 23 and $_return = false;

        if ($_minute < 0) $_minute = 0 or $_return = false;
        elseif ($_minute > 59) $_minute = 59 and $_return = false;

        if ($_second < 0) $_second = 0 or $_return = false;
        elseif ($_second > 59) $_second = 59 and $_return = false;

        // * * *

        return $_return;
    }

    /**
     * Проверяет на корректность номер года
     *
     * @param   int   $testValue   Значение для проверки
     *
     * @return  bool
     */
    public static function year(int $testValue): bool
    {
        return $testValue < 9999;
    }

    /**
     * Проверяет на корректность номер месяца
     *
     * @param   int   $testValue   Значение для проверки
     *
     * @return  bool
     */
    public static function mon(int $testValue): bool
    {
        return $testValue > 0 && $testValue < 13;
    }

    /**
     * Проверяет на корректность номера недели
     *
     * @param   int   $testValue   Значение для проверки
     *
     * @return  bool
     */
    public static function week(int $testValue): bool
    {
        return $testValue > 0 && $testValue < 53;
    }

    /**
     * Проверяет на корректность день месяца (без проверки, может ли такое число быть в конкретном месяце)
     *
     * @param   int   $testValue   Значение для проверки
     *
     * @return  bool
     */
    public static function day(int $testValue): bool
    {
        return $testValue > 0 && $testValue < 32;
    }

    /**
     * Проверяет на корректность часы
     *
     * @param   int   $testValue   Значение для проверки
     *
     * @return  bool
     */
    public static function hour(int $testValue): bool
    {
        return $testValue >= 0 && $testValue < 24;
    }

    /**
     * Проверяет на корректность минуты
     *
     * @param   int   $testValue   Значение для проверки
     *
     * @return  bool
     */
    public static function minute(int $testValue): bool
    {
        return $testValue >= 0 && $testValue < 60;
    }

    /**
     * Проверяет на корректность секунды
     *
     * @param   int   $testValue   Значение для проверки
     *
     * @return  bool
     */
    public static function second(int $testValue): bool
    {
        return $testValue >= 0 && $testValue < 60;
    }

    /**
     * Проверяет на корректность день года (без проверки, может ли указанный день быть в году)
     *
     * (!) Проверяет диапазон 1 - 366 дней (366 день бывает только в високосном году)
     *
     * @param   int   $testValue   Значение для проверки
     *
     * @return  bool
     */
    public static function yearDay(int $testValue): bool
    {
        return $testValue > 0 && $testValue < 367;
    }

    /**
     * Проверяет на корректность день недели (1 - понедельник, 7 - воскресенье)
     *
     * @param   int   $testValue   Значение для проверки
     *
     * @return  bool
     */
    public static function weekDay(int $testValue): bool
    {
        return $testValue > 0 && $testValue < 8;
    }
}
