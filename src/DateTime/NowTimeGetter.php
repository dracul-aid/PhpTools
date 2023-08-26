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

/**
 * Статический класс, с набором функций, для получения частей текущей даты-времени
 *
 * Оглавление:
 * <br>--- SQL форматы
 * <br> {@see NowTimeGetter::sqlDate()} - SQL дата (ДДДД-ММ-ДД)
 * <br> {@see NowTimeGetter::sqlTime()} - SQL время (ЧЧ:ММ:СС)
 * <br> {@see NowTimeGetter::sqlDateTime()} - SQL дата-время DATETIME (ДДДД-ММ-ДД ЧЧ:ММ:СС)
 * <br>--- Год
 * <br> {@see NowTimeGetter::getYear()} - Год (например, 2018)
 * <br> {@see NowTimeGetter::getYearDay()} - День года (1 - 366)
 * <br> {@see NowTimeGetter::getYearDay2()} - День года с ведущим нулем (001 - 366)
 * <br>--- Месяц
 * <br> {@see NowTimeGetter::getMon()} - Месяца (1 - 12)
 * <br> {@see NowTimeGetter::getMon2()} - Месяца с ведущим нулем (01 - 12)
 * <br> {@see NowTimeGetter::getMonDay()} - Текущий день месяца (1 - 31)
 * <br> {@see NowTimeGetter::getMonDay2()} - Текущий день месяца с ведущим нулем (01 - 31)
 * <br>--- Неделя
 * <br> {@see NowTimeGetter::getWeek()} - Неделя (01 - 52)
 * <br> {@see NowTimeGetter::getWeek2()} - Неделя с ведущим нулем (01 - 52)
 * <br> {@see NowTimeGetter::getWeekDay()} - День недели (1 - понедельник ... 7 - воскресенье)
 * <br> {@see NowTimeGetter::getWeekDayUSA()} - День недели для США (0 - воскресенье, 1 - понедельник ... 6 - суббота)
 * <br>--- Время
 * <br> {@see NowTimeGetter::getHour()} - Час (0 - 23)
 * <br> {@see NowTimeGetter::getHour2()} - Час с ведущим 0-ом (00 - 23)
 * <br> {@see NowTimeGetter::getMinute()} - Минута (0 - 59)
 * <br> {@see NowTimeGetter::getMinute2()} - Минута с ведущим 0-ом (00 - 59)
 * <br> {@see NowTimeGetter::getSecond()} - Секунда (0 - 59)
 * <br> {@see NowTimeGetter::getSecond2()} - Секунда с ведущим 0-ом (00 - 59)
 */
final class NowTimeGetter
{
    /**
     * Вернет текущую дату в SQL формате для типа DATE (ДДДД-ММ-ДД)
     *
     * @return string
     */
    public static function sqlDate(): string
    {
        return date(DateTimeFormats::SQL_DATE);
    }

    /**
     * Вернет текущую дату в SQL формате для типа TIME (ЧЧ:ММ:СС)
     *
     * @return string
     */
    public static function sqlTime(): string
    {
        return date(DateTimeFormats::SQL_TIME);
    }

    /**
     * Вернет текущую дату в SQL формате для типа DATETIME (ДДДД-ММ-ДД ЧЧ:ММ:СС)
     *
     * @return string
     */
    public static function sqlDateTime(): string
    {
        return date(DateTimeFormats::SQL_DATETIME);
    }

    /**
     * Вернет текущий год (например, 2018)
     *
     * @return int
     */
    public static function getYear(): int
    {
        return (int)date('Y');
    }

    /**
     * Вернет текущий номер месяца (1 - 12)
     *
     * @return int
     */
    public static function getMon(): int
    {
        return (int)date('n');
    }

    /**
     * Вернет текущий номер месяца с ведущим нулем (01 - 12)
     *
     * @return string
     */
    public static function getMon2(): string
    {
        return date('m');
    }

    /**
     * Вернет текущий недели (1 - 52)
     *
     * Вернет номер недели по стандарту ISO 8601, по которому первая неделя года:
     * <br>+ Неделя, содержащая 4 января
     * <br>+ Неделя, в которой 1 января это понедельник, вторник, среда или четверг
     * <br>+ Неделя, которая содержит как минимум четыре дня нового года
     * <br>Т.е. 52 неделя года может оказаться уже в "новом году" (например 1 января суббота, это будет 52 неделя и она будет относиться к предыдущему году)
     *
     * @return int
     */
    public static function getWeek(): int
    {
        return (int)date('W');
    }

    /**
     * Вернет текущий неделю с ведущим нулем (01 - 52)
     *
     * Вернет номер недели по стандарту ISO 8601, по которому первая неделя года:
     * <br>+ Неделя, содержащая 4 января
     * <br>+ Неделя, в которой 1 января это понедельник, вторник, среда или четверг
     * <br>+ Неделя, которая содержит как минимум четыре дня нового года
     * <br>Т.е. 52 неделя года может оказаться уже в "новом году" (например 1 января суббота, это будет 52 неделя и она будет относиться к предыдущему году)
     *
     * @return string
     */
    public static function getWeek2(): string
    {
        $week = date('W');

        if (strlen($week) === 1) return "0{$week}";
        else return $week;
    }

    /**
     * Вернет текущий день месяца (1 - 31)
     *
     * @return int
     */
    public static function getMonDay(): int
    {
        return (int)date('j');
    }

    /**
     * Вернет текущий день месяца с ведущим нулем (01 - 31)
     *
     * @return string
     */
    public static function getMonDay2(): string
    {
        return date('d');
    }

    /**
     * Вернет текущий день недели (1 - понедельник ... 7 - воскресенье)
     *
     * @return int
     */
    public static function getWeekDay(): int
    {
        return (int)date('N');
    }

    /**
     * Вернет текущий день недели для США (0 - воскресенье, 1 - понедельник ... 6 - суббота)
     *
     * @return int
     */
    public static function getWeekDayUSA(): int
    {
        return (int)getdate()['wday'];
    }

    /**
     * Вернет текущий день года (1 - 366)
     *
     * @return int
     */
    public static function getYearDay(): int
    {
        return (int)getdate()['yday'] + 1;
    }

    /**
     * Вернет текущий день года с ведущим нулем (001 - 366)
     *
     * @return string
     */
    public static function getYearDay2(): string
    {
        $yday = (string)self::getYearDay();

        if (strlen($yday) === 1) return "00{$yday}";
        elseif (strlen($yday) === 2) return "0{$yday}";
        else return $yday;
    }

    /**
     * Вернет текущий час (0 - 23)
     *
     * @return int
     */
    public static function getHour(): int
    {
        return (int)date('G');
    }

    /**
     * Вернет текущий час с ведущим 0-ом (00 - 23)
     *
     * @return string
     */
    public static function getHour2(): string
    {
        return date('H');
    }

    /**
     * Вернет текущую минуту (0 - 59)
     *
     * @return int
     *
     * @todo TEST тебует покрытия теста
     */
    public static function getMinute(): int
    {
        $minutes = date('i');
        if ($minutes[0] === '0') $minutes = $minutes[1];

        return (int)$minutes;
    }

    /**
     * Вернет текущую минуту с ведущим 0-ом (00 - 59)
     *
     * @return string
     *
     * @todo TEST тебует покрытия теста
     */
    public static function getMinute2(): string
    {
        return date('i');
    }

    /**
     * Вернет текущую секунду (0 - 59)
     *
     * @return int
     *
     * @todo TEST тебует покрытия теста
     */
    public static function getSecond(): int
    {
        $second = date('s');
        if ($second[0] === '0') $second = $second[1];

        // * * *

        return (int)$second;
    }

    /**
     * Вернет текущую секунду с ведущим 0-ом (00 - 59)
     *
     * @return string
     *
     * @todo TEST тебует покрытия теста
     */
    public static function getSecond2(): string
    {
        return date('s');
    }
}
