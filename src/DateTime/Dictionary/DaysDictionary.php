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

use DraculAid\PhpTools\tests\DateTime\Dictionary\DaysDictionaryTest;

/**
 * Различные константы связанные с днями (сутками)
 *
 * Оглавление
 * <br>--- Содержимое дня
 * <br>{@see DaysDictionary::HOURS_IN_DAY} Кол-во часов в сутках
 * <br>{@see DaysDictionary::MINUTES_IN_DAY} Кол-во минут в сутках
 * <br>{@see DaysDictionary::SECONDS_IN_DAY} Кол-во секунд в сутках
 * <br>--- Номера дней недели
 * <br>{@see DaysDictionary::PHP_FIRST_DAY_NUMBER_IN_USA} Номер первого дня в форматах даны США (Воскресенье)
 * <br>{@see DaysDictionary::PHP_FIRST_DAY_NUMBER} Номер первого дня в международном формате (Понедельник)
 * <br>{@see DaysDictionary::PHP_LAST_DAY_NUMBER_IN_USA} Номер последнего дня в неделе в форматах даны США (Суббота)
 * <br>{@see DaysDictionary::GETDATE_DAY_TO_NUMBER_DAY} Преобразование номеров дней недели из формата функции {@see getdate()}
 * <br>Номера дней недели в международном формате {@see DaysDictionary::DAY_1} - {@see DaysDictionary::DAY_7}
 * <br>Номера дней недели в формате PHP функции {@see getdate()} {@see DaysDictionary::GETDATE_DAY_1} - {@see DaysDictionary::GETDATE_DAY_7}
 * <br>--- Буквенные коды дней недели
 * <br>{@see DaysDictionary::getDayLabel()} Вернет буквенный код дня или FALSE если номер дня неверный
 * <br>{@see DaysDictionary::getDayLabelOrException()} Вернет буквенный код дня или выбросит исключение}
 * <br>{@see DaysDictionary::CHAR2_LIST} Список 2 буквенных кодов дней недели
 * <br>{@see DaysDictionary::CHAR3_LIST} Список 3 буквенных кодов дней недели
 *
 * Test cases for class {@see DaysDictionaryTest}
 */
final class DaysDictionary
{
    /** Кол-во часов в сутках */
    public const HOURS_IN_DAY = 24;

    /** Кол-во минут в сутках */
    public const MINUTES_IN_DAY = 1440;

    /**
     * Кол-во секунд в сутках
     *
     * @see TimestampConstants::DAY_SEC Кол-во секунд в сутках (в пакете констант таймштампов)
     */
    public const SECONDS_IN_DAY = 86400;

    /** Секунда начала суток */
    public const START_DAY_SECOND = 0;

    /** Минута начала суток */
    public const START_DAY_MINUTE = 0;

    /** Последняя секунда суток */
    public const END_DAY_SECOND = 86400;

    /** Последняя минута суток */
    public const END_DAY_MINUTE = 1440;

    /** Час начала суток */
    public const START_DAY_HOUR = 0;

    /** Час конца суток (в 12-ти часовом формате) */
    public const END_DAY_HOUR_12 = 12;

    /** Час конца суток (в 24-ти часовом формате) */
    public const END_DAY_HOUR_24 = 24;

    /** Понедельник, в формате {@see getdate()} (Номер дня, с 0 до 6) */
    public const GETDATE_DAY_1 = 1;
    /** Вторник, в формате {@see getdate()} (Номер дня, с 0 до 6) */
    public const GETDATE_DAY_2 = 2;
    /** Среда, в формате {@see getdate()} (Номер дня, с 0 до 6) */
    public const GETDATE_DAY_3 = 3;
    /** Четверг, в формате {@see getdate()} (Номер дня, с 0 до 6) */
    public const GETDATE_DAY_4 = 4;
    /** Пятница, в формате {@see getdate()} (Номер дня, с 0 до 6) */
    public const GETDATE_DAY_5 = 5;
    /** Суббота, в формате {@see getdate()} (Номер дня, с 0 до 6) */
    public const GETDATE_DAY_6 = 6;
    /** Воскресенье, в формате {@see getdate()} (Номер дня, с 0 до 6) */
    public const GETDATE_DAY_7 = 0;

    /** Понедельник (Номер дня, с 1 до 7) */
    public const DAY_1 = 1;
    /** Вторник (Номер дня, с 1 до 7) */
    public const DAY_2 = 2;
    /** Среда (Номер дня, с 1 до 7) */
    public const DAY_3 = 3;
    /** Четверг (Номер дня, с 1 до 7) */
    public const DAY_4 = 4;
    /** Пятница (Номер дня, с 1 до 7) */
    public const DAY_5 = 5;
    /** Суббота (Номер дня, с 1 до 7) */
    public const DAY_6 = 6;
    /** Воскресенье (Номер дня, с 1 до 7) */
    public const DAY_7 = 7;

    /** Номер первого дня в форматах даны США (Воскресенье), используется для работы с {@see getdate()} */
    public const PHP_FIRST_DAY_NUMBER_IN_USA = 0;

    /** Номер первого дня в международном формате (Понедельник), используется для работы с {@see getdate()} */
    public const PHP_FIRST_DAY_NUMBER = 1;

    /** Номер последнего дня в неделе в форматах даны США (Суббота), используется для работы с {@see getdate()} */
    public const PHP_LAST_DAY_NUMBER_IN_USA = 6;

    /** Преобразование номера дня недели из формата США (например, для функции {@see getdate()}) в международный формат */
    public const GETDATE_DAY_TO_NUMBER_DAY = [
        self::GETDATE_DAY_7 => self::DAY_7,
        self::GETDATE_DAY_1 => self::DAY_1,
        self::GETDATE_DAY_2 => self::DAY_2,
        self::GETDATE_DAY_3 => self::DAY_3,
        self::GETDATE_DAY_4 => self::DAY_4,
        self::GETDATE_DAY_5 => self::DAY_5,
        self::GETDATE_DAY_6 => self::DAY_6,
    ];

    /** Понедельник (2 буквенное международное сокращение) */
    public const CHAR2_1 = 'Mo';
    /** Понедельник (2 буквенное международное сокращение) */
    public const CHAR2_2 = 'Tu';
    /** Понедельник (2 буквенное международное сокращение) */
    public const CHAR2_3 = 'We';
    /** Понедельник (2 буквенное международное сокращение) */
    public const CHAR2_4 = 'Th';
    /** Понедельник (2 буквенное международное сокращение) */
    public const CHAR2_5 = 'Fr';
    /** Понедельник (2 буквенное международное сокращение) */
    public const CHAR2_6 = 'Sa';
    /** Понедельник (2 буквенное международное сокращение) */
    public const CHAR2_7 = 'Su';

    /**
     * Список 2-буквенных международных кодов дней недели
     *
     * Это именно список всех двухбуквенных кодов, если необходимо получить буквенный код для дня недели из {@see getdate()}
     * то стоит использовать {@see DaysDictionary::getDayLabel()} или {@see DaysDictionary::getDayLabelOrException()}
     *
     * @see DaysDictionary::CHAR3_LIST Список 3 буквенных кодов дней недели
     */
    public const CHAR2_LIST = [
        self::DAY_1 => self::CHAR2_1,
        self::DAY_2 => self::CHAR2_2,
        self::DAY_3 => self::CHAR2_3,
        self::DAY_4 => self::CHAR2_4,
        self::DAY_5 => self::CHAR2_5,
        self::DAY_6 => self::CHAR2_6,
        self::DAY_7 => self::CHAR2_7,
    ];

    /** Понедельник (3 буквенное международное сокращение) */
    public const CHAR3_1 = 'Mon';
    /** Понедельник (3 буквенное международное сокращение) */
    public const CHAR3_2 = 'Tue';
    /** Понедельник (3 буквенное международное сокращение) */
    public const CHAR3_3 = 'Wed';
    /** Понедельник (3 буквенное международное сокращение) */
    public const CHAR3_4 = 'Thu';
    /** Понедельник (3 буквенное международное сокращение) */
    public const CHAR3_5 = 'Fri';
    /** Понедельник (3 буквенное международное сокращение) */
    public const CHAR3_6 = 'Sat';
    /** Понедельник (3 буквенное международное сокращение) */
    public const CHAR3_7 = 'Sun';

    /**
     * Список 3-буквенных международных кодов дней недели
     *
     * Это именно список всех двухбуквенных кодов, если необходимо получить буквенный код для дня недели из {@see getdate()}
     * то стоит использовать {@see DaysDictionary::getDayLabel()} или {@see DaysDictionary::getDayLabelOrException()}
     *
     * @see DaysDictionary::CHAR2_LIST Список 2 буквенных кодов дней недели
     */
    public const CHAR3_LIST = [
        self::DAY_1 => self::CHAR3_1,
        self::DAY_2 => self::CHAR3_2,
        self::DAY_3 => self::CHAR3_3,
        self::DAY_4 => self::CHAR3_4,
        self::DAY_5 => self::CHAR3_5,
        self::DAY_6 => self::CHAR3_6,
        self::DAY_7 => self::CHAR3_7,
    ];

    /**
     * Вернет 2 или 3 буквенный код дня недели по его номеру, вернет FALSE если передан некорректный номер дня
     *
     * Совместим как с {@see getdate()} так и с номерами дней недели в международном формате
     *
     * @see DaysDictionary::getDayLabelOrException() Вернет буквенный код дня или выбросит исключение
     * @see DaysDictionary::CHAR2_LIST Список 2 буквенных кодов дней недели
     * @see DaysDictionary::CHAR3_LIST Список 3 буквенных кодов дней недели
     *
     * @param   int   $numberDay    Номер дня недели (от 0 до 7, включительно), если передан день вне диапазона - вернет FALSE
     * @param   int   $format       Символов в строковом представлении дня недели (2 или 3)
     *                              <br>Если 2, будет использованы {@see DaysDictionary::CHAR2_1} - {@see DaysDictionary::CHAR2_7}
     *                              <br>Если 3, будет использованы {@see DaysDictionary::CHAR3_1} - {@see DaysDictionary::CHAR3_7}
     *
     * @return  false|string
     *
     * @throws  \LogicException Формат должен быть равен 2 или 3
     *
     * @todo PHP8 типизация ответа функции
     */
    public static function getDayLabel(int $numberDay, int $format = 2)
    {
        if ($format != 2 && $format != 3) throw new \LogicException("\$format format can be 2 or 3, but function call with {$format}");

        // * * *

        if ($numberDay === 0) $numberDay = 7;

        $constName = self::class . "::CHAR{$format}_{$numberDay}";

        if (!defined($constName)) return false;
        else return constant($constName);
    }

    /**
     * Вернет 2 или 3 буквенный код дня недели по его номеру, выбросит исключение если передан некорректный номер дня
     *
     * Совместим как с {@see getdate()} так и с номерами дней недели в международном формате
     *
     * @see DaysDictionary::getDayLabel() Вернет буквенный код дня или FALSE если номер дня неверный
     * @see DaysDictionary::CHAR2_LIST Список 2 буквенных кодов дней недели
     * @see DaysDictionary::CHAR3_LIST Список 3 буквенных кодов дней недели
     *
     * @param   int<0, 7>      $numberDay        Номер дня недели (от 0 до 7, включительно), если передан день вне диапазона - будет выброшено исключение
     * @param   int<2, 3>      $format           Символов в строковом представлении дня недели (2 или 3)
     *                                     <br>Если 2, будет использованы {@see DaysDictionary::CHAR2_1} - {@see DaysDictionary::CHAR2_7}
     *                                     <br>Если 3, будет использованы {@see DaysDictionary::CHAR3_1} - {@see DaysDictionary::CHAR3_7}
     * @param   string   $classException   Класс для создания исключения (по умолчанию {@see \RuntimeException})
     *
     * @return  string
     *
     * @psalm-param class-string<\Throwable> $classException
     *
     * @throws  \RuntimeException Номер для недели должен быть от 0 до 7
     * @throws  \LogicException Формат должен быть равен 2 или 3
     *
     * @psalm-suppress InvalidFalsableReturnType Всегда вернет строку, варианты для возвращения FALSE отброшены выше
     */
    public static function getDayLabelOrException(int $numberDay, int $format = 2, string $classException = \RuntimeException::class): string
    {
        /** @psalm-suppress InvalidCast $numberDay еще как может быть преобразована в строку, так как в функцию пользователь может передать что хочет (псалм не боженька) */
        if ($numberDay < 0 || $numberDay > 7) throw new $classException("\$numberDay format can be 0 - 7, but function call with {$numberDay}");

        /** @psalm-suppress FalsableReturnStatement Всегда вернет строку, варианты для возвращения FALSE отброшены выше */
        return self::getDayLabel($numberDay, $format);
    }
}
