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

use DraculAid\Php8forPhp7\TypeValidator;
use DraculAid\PhpTools\DateTime\Dictionary\DateTimeFormats;
use DraculAid\PhpTools\DateTime\Types\GetTimestampInterface;
use DraculAid\PhpTools\DateTime\Types\PhpExtended\DateTimeExtendedType;

/**
 * Набор функция для облегчения работы с объектами даты-времени (см {@see \DateTimeInterface})
 *
 * Оглавление:
 * <br> {@see DateTimeObjectHelper::getDateObject()} - Вернет дата-тайм объект, из разного представления даты-времени
 * <br> {@see DateTimeObjectHelper::copyDateTimeObject()} - Копирует объект даты-времени
 * <br> {@see DateTimeObjectHelper::isGetTimestamp()} - Проверяет, может ли объект вернуть тайштамп
 *
 * @see DateTimeHelper Набор функий для работы с датой и временем
 * @see TimestampHelper Хэлпер, для работы с таймштампами
 */
final class DateTimeObjectHelper
{
    /**
     * Примет дату-время в различных форматах и всегда вернет объект для работы с датой-временем
     *
     * @param mixed $dateTime        Дата-время в одном из представлений:
     *                                    <br>+ {@see \DateTimeInterface}: Объект даты-времени
     *                                    <br>+ {@see GetTimestampInterface}: Объект, поддерживающий ответ ввиде таймштампа
     *                                    <br>+ string: Строковое представление даты, см {@see date_parse()}
     *                                    <br>+ null: будет создан объект с текущей датой-временем
     *                                    <br>+ int: Таймштам (в секундах)
     *                                    <br>+ float: Таймштамп в `секундах.микросекундах`
     *                                    <br>+ array: массив с описанием даты, см {@see getdate()}
     * @param   string   $dateTimeClass   Класс, для создания объекта
     *
     * @return  \DateTimeInterface
     *
     * @throws  \TypeError Если был передан неподходящий тип данных (в случае массива, массив не имел всех необходимых полей для построения даты)
     *
     * @see TimestampHelper::getTimestamp() Позволяяет из любого формата даты-времени получить таймштамп
     * @see DateTimeHelper::getDateArray() Позволяяет из любого формата даты-времени получить массив с описанием даты ({@see getdate()})
     *
     * @todo PHP8 типизация аргументов (int|float|string|array|\DateTimeInterface)
     */
    public static function getDateObject($dateTime = null, string $dateTimeClass = DateTimeExtendedType::class): \DateTimeInterface
    {
        if (is_a($dateTime, $dateTimeClass)) return $dateTime;

        // * * *

        if ($dateTime === null) return new $dateTimeClass();

        if (is_string($dateTime)) return new $dateTimeClass($dateTime);

        if (is_int($dateTime)) return date_timestamp_set(new $dateTimeClass(), $dateTime);

        if (is_float($dateTime))
        {
            $second = intval($dateTime);
            // PHP7.4 ожидает, что дробная часть секунды, будет от 0 до 6 знаков после запятой,
            // прочие варианты (например, наносекунды) приведут к ошибке разбора строки-даты времени (критическая ошибка)
            $microsecond = substr((string)($dateTime - $second), 2, 6);

            return new $dateTimeClass(
                date(DateTimeFormats::TIMESTAMP_SEC_TO_STRING, $second) . ".{$microsecond}"
            );
        }

        if (is_array($dateTime))
        {
            return new $dateTimeClass(
                date(DateTimeFormats::FUNCTIONS, TimestampHelper::getdateArrayToTimestamp($dateTime))
            );
        }

        if (self::isGetTimestamp($dateTime))
        {
            return self::copyDateTimeObject($dateTime, $dateTimeClass);
        }

        throw new \TypeError('$dateTime is not correct type');
    }

    /**
     * Копирует объект даты-времени
     *
     * @param   \DateTimeInterface|GetTimestampInterface   $dateTime         Объект даты-времени, на основе которого будет создан новый объект
     * @param   null|string                                $dateTimeClass    Класс, для создания нового объекта, NULL - тот же класс, что и $dateTime
     *
     * @return  \DateTimeInterface|GetTimestampInterface
     *
     * @todo PHP8 типизация аргументов и ответа функции
     */
    public static function copyDateTimeObject($dateTime, ?string $dateTimeClass = null)
    {
        TypeValidator::validateOr($dateTime, [\DateTimeInterface::class, GetTimestampInterface::class]);

        if ($dateTimeClass === null)
        {
            $dateTimeClass = get_class($dateTime);
        }
        elseif (!is_subclass_of($dateTimeClass, \DateTimeInterface::class) && !is_subclass_of($dateTimeClass, GetTimestampInterface::class))
        {
            throw new \TypeError("Class {$dateTimeClass} can be a \DateTimeInterface or a GetTimestampInterface");
        }

        // * * *

        return new $dateTimeClass(
            $dateTime->format(DateTimeFormats::FUNCTIONS)
        );
    }

    /**
     * Проверяет, может ли объект вернуть тайштамп (см {@see \DateTimeInterface::getTimestamp()} или {@see GetTimestampInterface::getTimestamp()})
     *
     * @param   mixed   $testObject      Значение для проверки
     * @param   bool    $withCallable    Если объект не реализует  {@see \DateTimeInterface} или {@see GetTimestampInterface}
     *                                   также проверит, может быть в нем есть функция getTimestamp()
     *
     * @return  bool
     *
     * @todo PHP8 типизация аргументов
     */
    public static function isGetTimestamp($testObject, bool $withCallable = false): bool
    {
        if (!is_object($testObject)) return false;

        if (is_subclass_of($testObject, \DateTimeInterface::class) || is_subclass_of($testObject, GetTimestampInterface::class)) return true;

        if ($withCallable && is_callable([$testObject, 'getTimestamp'])) return true;

        return false;
    }
}
