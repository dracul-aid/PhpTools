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

use DraculAid\PhpTools\tests\DateTime\Dictionary\TimestampConstantsTest;

/**
 * Константы связанные с таймштампами
 *
 * Оглавление:
 * <br>{@see TimestampConstants::YEAR_SEC} - Кол-во секунд в году (365 дней)
 * <br>{@see TimestampConstants::YEAR_LEAP_SEC} - Кол-во секунд в високосном году (366 дней)
 * <br>{@see TimestampConstants::MON_28_SEC} - Кол-во секунд в феврале (28 дней)
 * <br>{@see TimestampConstants::MON_29_SEC} - Кол-во секунд в феврале високосного года (29 дней)
 * <br>{@see TimestampConstants::MON_30_SEC} - Кол-во секунд в месяце (30 дней)
 * <br>{@see TimestampConstants::MON_31_SEC} - Кол-во секунд в месяце (31 дней)
 * <br>{@see TimestampConstants::WEEK_SEC} - Кол-во секунд в неделе
 * <br>{@see TimestampConstants::DAY_SEC} - Кол-во секунд в сутках
 * <br>{@see TimestampConstants::HOUR_SEC} - Кол-во секунд в часе
 * <br>{@see TimestampConstants::MINUTE_SEC} - Кол-во секунд в минуте
 * <br>* * *
 * <br>{@see TimestampConstants::MILLISECOND_MODIFICATION} - Поправка для приведения таймштампа в JS формат (1 сек = 1000 миллисекунд)
 *
 * Test cases for class {@see TimestampConstantsTest}
 */
final class TimestampConstants
{
    /** Кол-во секунд в году (365 дней) */
    public const YEAR_SEC = 31536000;

    /**  Кол-во секунд в високосном году (366 дней) */
    public const YEAR_LEAP_SEC = 31622400;

    /** Кол-во секунд в месяце (30 дней) */
    public const MON_28_SEC = 2419200;

    /** Кол-во секунд в месяце (30 дней) */
    public const MON_29_SEC = 2505600;

    /** Кол-во секунд в месяце (30 дней) */
    public const MON_30_SEC = 2592000;

    /** Кол-во секунд в месяце (31 дней) */
    public const MON_31_SEC = 2678400;

    /** Кол-во секунд в неделе */
    public const WEEK_SEC = 604800;

    /**
     * Кол-во секунд в сутках
     *
     * @see DaysDictionary::SECONDS_IN_DAY Кол-во секунд в сутках (в пакете констант "дней")
     */
    public const DAY_SEC = 86400;

    /** Кол-во секунд в часе */
    public const HOUR_SEC = 3600;

    /** Кол-во секунд в минуте */
    public const MINUTE_SEC = 60;

    /** Поправка для приведения таймштампа в JS формат (1 сек = 1000 миллисекунд) */
    public const MILLISECOND_MODIFICATION = 1000;
}
