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

/**
 * Константы форматов переводы даты-времени в строку
 *
 * @link https://www.php.net/manual/ru/datetime.format.php Форматы преобразования даты-времени в строку в PHP
 *
 * Оглавление:
 * <br>+ {@see DateTimeFormats::FUNCTIONS} - Системный вариант, подходит для работы с функциями даты-времени в PHP (YYYY-MM-DD HH:MI:SS.ms timezone)
 * <br>+ {@see DateTimeFormats::TIMESTAMP_SEC_TO_STRING} - Преобразование таймштампа (сек) в строку (YYYY-MM-DD HH:MI:SS)
 * <br>+ {@see DateTimeFormats::TIMESTAMP_WITH_MILLISECONDS} - Таймштамп с милисекундами (123456789.123)
 * <br>+ {@see DateTimeFormats::TIMESTAMP_WITH_MICROSECONDS} - Таймштамп с милисекундами (123456789.123456)
 * <br>--- Отображения для людей
 * <br>+ {@see DateTimeFormats::VIEW_FOR_PEOPLE} - Вариант для отображения людям без часового пояса (YYYY-MM-DD HH:MI:SS)
 * <br>+ {@see DateTimeFormats::VIEW_FOR_PEOPLE_DATE} - Отображение даты людям (YYYY-MM-DD)
 * <br>+ {@see DateTimeFormats::VIEW_FOR_PEOPLE_TIME} - Отображение времени людям (HH:MI:SS)
 * <br>+ {@see DateTimeFormats::VIEW_FOR_PEOPLE_WITH_TIMEZONE} - Вариант для отображения людям С часовым пояса (YYYY-MM-DD HH:MI:SS timezone)
 * <br>--- Типы для SQL (MySQL)
 * <br>+ {@see DateTimeFormats::SQL_YEAR} - Хранение года, тип YEAR (YYYY)
 * <br>+ {@see DateTimeFormats::SQL_DATE} - Хранение даты (без времени), тип DATE (YYYY-MM-DD)
 * <br>+ {@see DateTimeFormats::SQL_TIME} - Хранение времени (без даты), тип DATE, без микросекунд (HH:MI:SS)
 * <br>+ {@see DateTimeFormats::SQL_TIME_MS} - Хранение времени (без даты), тип TIME, с микросекундами (HH:MI:SS.ms)
 * <br>+ {@see DateTimeFormats::SQL_DATETIME} - Хранение даты и времени, тип DATETIME, SMALLDATETIME, без указания микросекунд (YYYY-MM-DD HH:MI:SS.ms)
 * <br>+ {@see DateTimeFormats::SQL_DATETIME_MS} - Хранение даты и времени, тип DATETIME, SMALLDATETIME, с микросекундами (YYYY-MM-DD HH:MI:SS.ms)
 */
final class DateTimeFormats
{
    /**
     * Системный вариант, подходит для работы с функциями даты-времени в PHP (YYYY-MM-DD HH:MI:SS.ms timezone)
     */
    public const FUNCTIONS = 'Y-m-d H:i:s.u T';

    /**
     * Преобразование таймштампа (сек) в строку (YYYY-MM-DD HH:MI:SS)
     *
     * Этот формат подходит для передачи строкового представления времени в функции PHP, например {@see strtotime()}
     */
    public const TIMESTAMP_SEC_TO_STRING = 'Y-m-d H:i:s';

    /**
     * Вариант для отображения людям без часового пояса (YYYY-MM-DD HH:MI:SS)
     */
    public const VIEW_FOR_PEOPLE = 'Y-m-d H:i:s';

    /**
     * Отображение даты людям (YYYY-MM-DD)
     *
     * (!) Этот формат может быть изменен в любой следующей версии
     */
    public const VIEW_FOR_PEOPLE_DATE = 'Y-m-d';

    /**
     * Отображение времени людям (HH:MI:SS)
     *
     * (!) Этот формат может быть изменен в любой следующей версии
     */
    public const VIEW_FOR_PEOPLE_TIME = 'H:i:s';

    /**
     * Вариант для отображения людям с часовым поясом (YYYY-MM-DD HH:MI:SS timezone)
     *
     * (!) Этот формат может быть изменен в любой следующей версии
     */
    public const VIEW_FOR_PEOPLE_WITH_TIMEZONE = 'Y-m-d H:i:s T';

    /**
     * Дата для SQL типа YEAR `YYYY`
     * (Хранение года)
     */
    public const SQL_YEAR = 'Y';

    /**
     * Дата для SQL типа DATE `YYYY-MM-DD`
     * (Хранение даты, без времени)
     */
    public const SQL_DATE = 'Y-m-d';

    /**
     * Дата для SQL типа DATE, без микросекунд `HH:MI:SS`
     * (Хранение времени, без даты)
     */
    public const SQL_TIME = 'H:i:s';

    /**
     * Дата для SQL типа TIME, с микросекундами `HH:MI:SS.ms`
     * (Хранение времени, без даты)
     */
    public const SQL_TIME_MS = 'H:i:s.u';

    /**
     * Дата для SQL типа DATETIME, SMALLDATETIME, без указания микросекунд `YYYY-MM-DD HH:MI:SS`
     * (Хранение даты и времени)
     */
    public const SQL_DATETIME = 'Y-m-d H:i:s';

    /**
     * Дата для SQL типа DATETIME, SMALLDATETIME, с микросекундами `YYYY-MM-DD HH:MI:SS.ms`
     * (Хранение даты и времени)
     */
    public const SQL_DATETIME_MS = 'Y-m-d H:i:s.u';

    /** Таймштамп с милисекундами (123456789.123) */
    public const TIMESTAMP_WITH_MILLISECONDS = 'U.v';

    /** Таймштамп с микросекундами (123456789.123456) */
    public const TIMESTAMP_WITH_MICROSECONDS = 'U.u';
}
