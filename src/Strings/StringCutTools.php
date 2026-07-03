<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\Strings;

use DraculAid\PhpTools\tests\Strings\StringCutToolsTest;

/**
 * Статический класс для осуществления обрезания строк
 *
 * Оглавление:
 * <br>{@see StringCutTools::firstSubstrBefore()} - Обрежет строку до указанной подстроки (или подстрок)
 * <br>{@see StringCutTools::firstSubstrAfter()} - Вырежет строку с указанной подстроки (или подстрок) до конца, если такая подстрока есть
 * <br>{@see StringCutTools::trimInString()} - Удалит все повторяющиеся пробелы, в том числе и внутри строки
 * <br>{@see StringCutTools::quoteTrim()} - Удалит из начала и конца строки все кавычки
 * <br>{@see StringCutTools::clearMultiSpaces()} - Удалит повторяющиеся пробельные символы
 * <br>{@see StringCutTools::mask()} - Маскирует строку
 * <br>{@see StringCutTools::resize()} - Обрежет или дополнит строку до указанной длины
 *
 * @todo Реализовать StringCut::afterLastSubstr() - Обрежет строку после указанной подстроки (или подстрок)
 * @todo Реализовать StringCut::fromBetweenSubstr() - Обрежет строку между указанными подстроками
 *
 * Test cases for class {@see StringCutToolsTest}
 *
 * @since 0.3.0
 */
final class StringCutTools
{
    /**
     * Вырежет строку с начала до указанной подстроки, если такая подстрока есть (или списка подстрок)
     *
     * (!) Поиск ведется до первого нахождения подстроки
     *
     * @param   string                                $string       Строка для обрезания
     * @param   string|iterable<string|\Stringable>   $substr       Строка до которой ведется поиск или массив с подстроками (поиск ведется до нахождения первой из них)
     * @param   bool                                  $withSubstr   Нужно ли подстроку оставить в ответе
     * @param   int                                   $start        Позиция начала поиска в символах (замедляет поиск)
     *
     * @return  string   Вернет изначальную строку или обрезанную строку
     *
     * @psalm-suppress PossiblyInvalidArrayAccess Мы точно знаем, что запрошенный элемент массива будет
     */
    public static function firstSubstrBefore(string $string, string|iterable $substr, bool $withSubstr = false, int $start = 0): string
    {
        if (is_string($substr)) $substr = [$substr];

        $utf8mode = $start !== 0;

        // * * *

        $positionResult = StringSearchTools::position($string, $substr, $start, $utf8mode, true);

        if ($positionResult === null) return $string;

        if ($utf8mode)
        {
            if ($withSubstr) return (mb_substr($string, 0, $positionResult[0]) . $positionResult[1]);
            else return mb_substr($string, 0, $positionResult[0]);
        }
        else
        {
            if ($withSubstr) return (substr($string, 0, $positionResult[0]) . $positionResult[1]);
            else return substr($string, 0, $positionResult[0]);
        }
    }

    /**
     * Вырежет строку после указанной подстроки, если такая подстрока есть (или списка подстрок)
     *
     * (!) Поиск ведется до первого нахождения подстроки
     *
     * @param   string                                $string       Строка для обрезания
     * @param   string|iterable<string|\Stringable>   $substr       Строка до которой ведется поиск или массив с подстроками (поиск ведется до нахождения первой из них)
     * @param   bool                                  $withSubstr   Нужно ли найденную подстроку оставить в ответе
     * @param   int                                   $start        Позиция начала поиска в символах (замедляет поиск)
     *
     * @return  string   Вернет изначальную строку или обрезанную строку
     *
     * @psalm-suppress PossiblyInvalidArrayAccess Мы точно знаем, что запрошенный элемент массива будет
     * @psalm-suppress UnusedParam Псалм считает, что большая часть аргументов функции не используется внутри функции, но это не так
     */
    public static function firstSubstrAfter(string $string, string|iterable $substr, bool $withSubstr = false, int $start = 0): string
    {
        if (is_string($substr)) $substr = [$substr];

        $utf8mode = $start !== 0;

        // * * *

        $positionResult = StringSearchTools::position($string, $substr, $start, $utf8mode, true);

        if ($positionResult === null) return $string;

        if ($utf8mode)
        {
            if ($withSubstr) return $positionResult[1] . mb_substr($string, $positionResult[0] + mb_strlen($positionResult[1]));
            else return mb_substr($string, $positionResult[0] + mb_strlen($positionResult[1]));
        }
        else
        {
            if ($withSubstr) return $positionResult[1] . substr($string, $positionResult[0] + strlen($positionResult[1]));
            else return substr($string, $positionResult[0] + strlen($positionResult[1]));
        }
    }

    /**
     * Удалит все повторяющиеся пробелы, в том числе и внутри строки
     *
     * @param   string   $string    Строка для обработки
     * @param   string   $replace   На что будут заменены найденные пробелы (по умолчанию на ' ')
     *
     * @return string
     *
     * @psalm-suppress InvalidNullableReturnType Если preg_replace() вернет NULL (или иной другой тип кроме строки) мы и правда хотим упасть
     */
    public static function trimInString(string $string, string $replace = ' '): string
    {
        /** @psalm-suppress NullableReturnStatement Если preg_replace() вернет NULL (или иной другой тип кроме строки) мы и правда хотим упасть */
        return preg_replace("/\s+/", $replace, $string);
    }

    /**
     * Удалит из начала и конца строки все кавычки
     *
     * (!) Функция является "сахаром" для PHP функции {@see trim()}
     *
     * @param   string   $string   Строка для обработки
     *
     * @return string
     */
    public static function quoteTrim(string $string): string
    {
        return trim($string, '\'"«»‘`‚„‘’“”' );
    }

    /**
     * Удалит из строки последовательные пробельные символы, заменив их на пробел (или иной символ)
     *
     * @param   string   $string    Строка для обработки
     * @param   string   $replace   Строка для замены (по умолчанию пробел: `' '`)
     *
     * @return  string
     *
     * @psalm-suppress InvalidNullableReturnType Если preg_replace() вернет NULL (или иной другой тип кроме строки) мы и правда хотим упасть
     *
     * @since 0.7.0
     */
    public static function clearMultiSpaces(string $string, string $replace = ' '): string
    {
        if ($string === '' || $replace === '') return $string;

        /** @psalm-suppress NullableReturnStatement Если preg_replace() вернет NULL (или иной другой тип кроме строки) мы и правда хотим упасть */
        return preg_replace('/\s+/', $replace, $string);
    }

    /**
     * Маскирует строку (например, ключи или пароли), маска будет вставлена в центр строки
     *
     * @param   string        $string   Строка для обработки
     * @param   int<0, max>   $margin   Кол-во символов справа и слева от маски
     * @param   int<0, max>   $length   Разме строки после маскирования
     * @param   string        $mask     Символы маскирования
     *
     * @return  string
     *
     * @since 1.3.0
     */
    public static function mask(string $string, int $margin, int $length, string $mask = '*'): string
    {
        if ($length === 0 || $string === '') return '';

        if ($mask === '') return self::resize($string, $length, '');

        $stringLength = mb_strlen($string);
        $leftLength = min($margin, $stringLength, $length);
        $rightLength = min($margin, max(0, $stringLength - $leftLength), max(0, $length - $leftLength));
        $maskLength = $length - $leftLength - $rightLength;

        if ($maskLength <= 0) return self::resize($string, $length, '');

        if ($margin === 0) return StringTools::repeat($mask, $maskLength);

        return mb_substr($string, 0, $leftLength)
            . StringTools::repeat($mask, $maskLength)
            . mb_substr($string, -1 * $rightLength);
    }

    /**
     * Вернет строку, обрезав ее до нужного размера, если строка стала меньше нужного размера, дополнит ее указанными символами
     *
     * - Если $padding будет передан пустой строкой, то дополнение до указанной длины в $length не произойдет.
     * - Если $padding не кратен $length, одна из его вставок может быть обрезана
     *
     * @param   string   $string    Строка для обработки
     * @param   int      $length    Размер строки в символах (положительное число - обрезаем слева->направо, отрицательное число - обрезаем справа->налево [т.е. с конца])
     * @param   string   $padding   Строка для дозаполнения
     *
     * @return  string
     *
     * @since 1.3.0
     */
    public static function resize(string $string, int $length, string $padding): string
    {
        if ($length === 0) return '';

        $needLength = abs($length);
        $paddingLength = mb_strlen($padding);

        if ($string === '' && $padding !== '')
        {
            return StringTools::repeat($padding, $length);
        }

        $stringLength = mb_strlen($string);

        if ($stringLength === $needLength) return $string;

        // если длина строки больше, чем надо - обрежем
        if ($stringLength > $needLength)
        {
            if ($length > 0) return mb_substr($string, 0, $needLength);
            else return mb_substr($string, -1 * $needLength);
        }

        if ($padding === '') return $string;

        // Дополним строку справа или лева и вернем
        if ($length > 0) return $string . StringTools::repeat($padding, (int)($needLength - $stringLength / $paddingLength));
        else return StringTools::repeat($padding, (int)($needLength - $stringLength / $paddingLength)) . $string;
    }
}
