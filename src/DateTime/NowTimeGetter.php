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

use DraculAid\PhpTools\DateTime\Clock\Abstraction\BigClockInterface;
use DraculAid\PhpTools\DateTime\Clock\RealBigClock;
use DraculAid\PhpTools\DateTime\Dictionary\DateTimeFormats;
use DraculAid\PhpTools\tests\DateTime\NowTimeGetterTest;

/**
 * Статический класс, с набором функций, для получения частей текущей даты-времени
 *
 * Оглавление:
 * <br> {@see NowTimeGetter::getClock()} - Вернет текущие часы
 * <br> {@see NowTimeGetter::setClock()} - Заменит текущие часы
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
 *
 * Test cases for class {@see NowTimeGetterTest}
 */
final class NowTimeGetter
{
    /** Объект-часы для получения текущего времени */
    protected static BigClockInterface $clock;

    /**
     * Вернет часы, для получения текущего времени
     *
     * @return BigClockInterface
     *
     * @since 1.3.0
     */
    public static function getClock(): BigClockInterface
    {
        /** @psalm-suppress TypeDoesNotContainType PSALM не понимает, что переменные могут быть неинициализированными */
        if (empty(self::$clock)) {
            self::$clock = new RealBigClock();
        }

        return self::$clock;
    }

    /**
     * Заменит текущие часы
     *
     * @param   BigClockInterface   $clock
     *
     * @return  class-string<self>
     *
     * @since 1.3.0
     */
    public static function setClock(BigClockInterface $clock): string
    {
        self::$clock = $clock;

        return self::class;
    }

    /**
     * Вернет текущую дату в SQL формате для типа DATE (ДДДД-ММ-ДД)
     *
     * @return string
     */
    public static function sqlDate(): string
    {
        return self::getClock()->format(DateTimeFormats::SQL_DATE);
    }

    /**
     * Вернет текущую дату в SQL формате для типа TIME (ЧЧ:ММ:СС)
     *
     * @return string
     */
    public static function sqlTime(): string
    {
        return self::getClock()->format(DateTimeFormats::SQL_TIME);
    }

    /**
     * Вернет текущую дату в SQL формате для типа DATETIME (ДДДД-ММ-ДД ЧЧ:ММ:СС)
     *
     * @return string
     */
    public static function sqlDateTime(): string
    {
        return self::getClock()->format(DateTimeFormats::SQL_DATETIME);
    }

    /**
     * Вернет текущий год (например, 2018)
     *
     * @return int
     */
    public static function getYear(): int
    {
        return (int)self::getClock()->format('Y');
    }

    /**
     * Вернет текущий номер месяца (1 - 12)
     *
     * @return int<1, 12>
     *
     * @psalm-suppress LessSpecificReturnStatement Тут точно вернется нужный диапазон чисел
     * @psalm-suppress MoreSpecificReturnType Тут точно вернется нужный диапазон чисел
     */
    public static function getMon(): int
    {
        return (int)self::getClock()->format('n');
    }

    /**
     * Вернет текущий номер месяца с ведущим нулем (01 - 12)
     *
     * @return string
     */
    public static function getMon2(): string
    {
        return self::getClock()->format('m');
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
     * @return int<1, 52>
     *
     * @psalm-suppress LessSpecificReturnStatement Тут точно вернется нужный диапазон чисел
     * @psalm-suppress MoreSpecificReturnType Тут точно вернется нужный диапазон чисел
     */
    public static function getWeek(): int
    {
        return (int)self::getClock()->format('W');
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
        $week = self::getClock()->format('W');

        if (strlen($week) === 1) return "0{$week}";
        else return $week;
    }

    /**
     * Вернет текущий день месяца (1 - 31)
     *
     * @return int<1, 31>
     *
     * @psalm-suppress LessSpecificReturnStatement Тут точно вернется нужный диапазон чисел
     * @psalm-suppress MoreSpecificReturnType Тут точно вернется нужный диапазон чисел
     */
    public static function getMonDay(): int
    {
        return (int)self::getClock()->format('j');
    }

    /**
     * Вернет текущий день месяца с ведущим нулем (01 - 31)
     *
     * @return string
     */
    public static function getMonDay2(): string
    {
        return self::getClock()->format('d');
    }

    /**
     * Вернет текущий день недели (1 - понедельник ... 7 - воскресенье)
     *
     * @return int<1, 7>
     *
     * @psalm-suppress LessSpecificReturnStatement Тут точно вернется нужный диапазон чисел
     * @psalm-suppress MoreSpecificReturnType Тут точно вернется нужный диапазон чисел
     */
    public static function getWeekDay(): int
    {
        return (int)self::getClock()->format('N');
    }

    /**
     * Вернет текущий день недели для США (0 - воскресенье, 1 - понедельник ... 6 - суббота)
     *
     * @return int<0, 6>
     *
     * @psalm-suppress LessSpecificReturnStatement Тут точно вернется нужный диапазон чисел
     * @psalm-suppress MoreSpecificReturnType Тут точно вернется нужный диапазон чисел
     */
    public static function getWeekDayUSA(): int
    {
        return getdate(self::getClock()->timestamp())['wday'];
    }

    /**
     * Вернет текущий день года (1 - 366)
     *
     * @return int<0, 366>
     *
     * @psalm-suppress LessSpecificReturnStatement Тут точно вернется нужный диапазон чисел
     * @psalm-suppress MoreSpecificReturnType Тут точно вернется нужный диапазон чисел
     */
    public static function getYearDay(): int
    {
        return getdate(self::getClock()->timestamp())['yday'] + 1;
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
     * @return int<0, 23>
     *
     * @psalm-suppress LessSpecificReturnStatement Тут точно вернется нужный диапазон чисел
     * @psalm-suppress MoreSpecificReturnType Тут точно вернется нужный диапазон чисел
     */
    public static function getHour(): int
    {
        return (int)self::getClock()->format('G');
    }

    /**
     * Вернет текущий час с ведущим 0-ом (00 - 23)
     *
     * @return string
     */
    public static function getHour2(): string
    {
        return self::getClock()->format('H');
    }

    /**
     * Вернет текущую минуту (0 - 59)
     *
     * @return int<0, 59>
     *
     * @todo TEST тебует покрытия теста
     *
     * @psalm-suppress LessSpecificReturnStatement Тут точно вернется нужный диапазон чисел
     * @psalm-suppress MoreSpecificReturnType Тут точно вернется нужный диапазон чисел
     */
    public static function getMinute(): int
    {
        $minutes = self::getClock()->format('i');
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
        return self::getClock()->format('i');
    }

    /**
     * Вернет текущую секунду (0 - 59)
     *
     * @return int<0, 59>
     *
     * @todo TEST тебует покрытия теста
     *
     * @psalm-suppress LessSpecificReturnStatement Тут точно вернется нужный диапазон чисел
     * @psalm-suppress MoreSpecificReturnType Тут точно вернется нужный диапазон чисел
     */
    public static function getSecond(): int
    {
        $second = self::getClock()->format('s');
        if ($second[0] === '0') $second = (int)$second[1];

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
        return self::getClock()->format('s');
    }
}
