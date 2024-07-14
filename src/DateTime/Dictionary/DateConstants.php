<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\DateTime\Dictionary;

use DraculAid\PhpTools\tests\DateTime\Dictionary\DateConstantsTest;

/**
 * Различные константы связанные с датой и временем
 *
 * Оглавление:
 * <br>--- Размеры элементов даты (в днях)
 * <br>{@see DateConstants::YEAR_DAYS} Кол-во дней в году (365, не високосный год)
 * <br>{@see DateConstants::YEAR_LEAP_DAYS} Кол-во дней в високосном году (366)
 * <br>{@see DateConstants::MON_31} 31 день в месяце (Январь, Март, Июнь, Август, Октябрь, Декабрь)
 * <br>{@see DateConstants::MON_30} 30 дней в месяце (Апрель, Июль, Сентябрь, Ноябрь)
 * <br>{@see DateConstants::MON_29} Кол-во дней в Феврале високосного года (29 дней)
 * <br>{@see DateConstants::MON_28} Кол-во дней в Феврале (28 дней)
 * <br>{@see DateConstants::WEEK_DAYS} Кол-во дней в неделе
 * <br>--- Списки месяцев
 * <br>{@see DateConstants::MON_DAY_COUNT_LIST} Массив с кол-вом дней в каждом месяце
 * <br>{@see DateConstants::MON_31_DAY_LIST} Список месяцев в которых 31 День (Январь, Март, Май, Июль, Август, Октябрь, Декабрь)
 * <br>{@see DateConstants::MON_30_DAY_LIST} Список месяцев в которых 30 Дней (Апрель, Июнь, Сентябрь, Ноябрь)
 * <br>{@see DateConstants::MON_SHORT_LIST} Список коротких месяцев, т.е. 28 - 30 дней (Февраль, Апрель, Июнь, Сентябрь, Ноябрь)
 *
 * Test cases for class {@see DateConstantsTest}
 */
final class DateConstants
{
    /** Кол-во дней в году (365, не високосный год) */
    public const YEAR_DAYS = 365;

    /** Кол-во дней в високосном году (366) */
    public const YEAR_LEAP_DAYS = 366;

    /** 31 день в месяце (Январь, Март, Июнь, Август, Октябрь, Декабрь) */
    public const MON_31 = 31;

    /** 30 дней в месяце (Апрель, Июль, Сентябрь, Ноябрь) */
    public const MON_30 = 30;

    /** Кол-во дней в Феврале високосного года (29 дней) */
    public const MON_29 = 29;

    /** Кол-во дней в Феврале (28 дней) */
    public const MON_28 = 28;

    /** Кол-во дней в неделе */
    public const WEEK_DAYS = 7;

    /**
     * Массив с кол-вом дней в каждом месяце (индекс - номер месяца, значение - кол-во дней)
     *
     * (!) Для февраля содержит 28 дней, не учитывая високосный он или нет
     */
    public const MON_DAY_COUNT_LIST = [
        1 => 31, // Январь
        2 => 28, // Февраль (обычный, т.е. не високосный)
        3 => 31, // Март
        4 => 30, // Апрель
        5 => 31, // Май
        6 => 30, // Июнь
        7 => 31, // Июль
        8 => 31, // Август
        9 => 30, // Сентябрь
        10 => 31, // Октябрь
        11 => 30, // Ноябрь
        12 => 31, // Декабрь
    ];

    /** Список месяцев в которых 31 День (Январь, Март, Май, Июль, Август, Октябрь, Декабрь) */
    public const MON_31_DAY_LIST = [1, 3, 5, 7, 8, 10, 12];

    /** Список месяцев в которых 30 Дней (Апрель, Июнь, Сентябрь, Ноябрь) */
    public const MON_30_DAY_LIST = [4, 6, 9, 11];

    /** Список коротких месяцев, т.е. 28 - 30 дней (Февраль, Апрель, Июнь, Сентябрь, Ноябрь) */
    public const MON_SHORT_LIST = [2, 4, 6, 9, 11];
}
