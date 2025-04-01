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

use DraculAid\Php8forPhp7\LoaderPhp8Lib;
use DraculAid\Php8forPhp7\TypeValidator;
use DraculAid\PhpTools\tests\Strings\StringCutToolsTest;

// @todo PHP8 удалить
LoaderPhp8Lib::loadInterfaces();

/**
 * Статический класс для осуществления обрезания строк
 *
 * Оглавление:
 * <br>{@see StringCutTools::firstSubstrBefore()} - Обрежет строку до указанной подстроки (или подстрок)
 * <br>{@see StringCutTools::firstSubstrAfter()} - Вырежет строку с указанной подстроки (или подстрок) до конца, если такая подстрока есть
 * <br>{@see StringCutTools::trimInString()} - Удалит все повторяющиеся пробелы, в том числе и внутри строки
 * <br>{@see StringCutTools::quoteTrim()} - Удалит из начала и конца строки все кавычки
 * <br>{@see StringCutTools::clearMultiSpaces()} - Удалит повторяющиеся пробельные символы
 *
 * @todo Реализовать StringCut::afterLastSubstr() - Обрежет строку после указанной подстроки (или подстрок)
 * @todo Реализовать StringCut::fromBetweenSubstr() - Обрежет строку между указанными подстроками
 *
 * Test cases for class {@see StringCutToolsTest}
 */
final class StringCutTools
{
    /**
     * Вырежет строку с начала до указанной подстроки, если такая подстрока есть (или списка подстрок)
     *
     * (!) Поиск ведется до первого нахождения подстроки
     *
     * @param   string         $string       Строка для обрезания
     * @param   string|array   $substr       Строка до которой ведется поиск или массив с подстроками (поиск ведется до нахождения первой из них)
     * @param   bool           $withSubstr   Нужно ли подстроку оставить в ответе
     * @param   int            $start        Позиция начала поиска в символах (замедляет поиск)
     *
     * @return  string   Вернет изначальную строку или обрезанную строку
     *
     *
     * @psalm-param string|iterable<string|\Stringable> $substr
     *
     * @todo PHP8 Типизация аргументов функции
     *
     * @psalm-suppress PossiblyInvalidArrayAccess Мы точно знаем, что запрошенный элемент массива будет
     * @psalm-suppress UnusedParam Псалм считает, что большая часть аргументов функции не используется внутри функции, но это не так
     * @psalm-suppress UndefinedDocblockClass В PHP 7.4 Псалм ругается на \Stringable, так как не может его найти @todo PHP8 удалить
     */
    public static function firstSubstrBefore(string $string, $substr, bool $withSubstr = false, int $start = 0): string
    {
        TypeValidator::validateOr($substr, ['string', 'array']);

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
     * @param   string            $string       Строка для обрезания
     * @param   string|iterable   $substr       Строка до которой ведется поиск или массив с подстроками (поиск ведется до нахождения первой из них)
     * @param   bool              $withSubstr   Нужно ли найденную подстроку оставить в ответе
     * @param   int               $start        Позиция начала поиска в символах (замедляет поиск)
     *
     * @return  string   Вернет изначальную строку или обрезанную строку
     *
     * @todo PHP8 Типизация аргументов функции
     *
     * @psalm-param string|iterable<string|\Stringable> $substr
     *
     * @psalm-suppress PossiblyInvalidArrayAccess Мы точно знаем, что запрошенный элемент массива будет
     * @psalm-suppress UnusedParam Псалм считает, что большая часть аргументов функции не используется внутри функции, но это не так
     * @psalm-suppress UndefinedDocblockClass В PHP 7.4 Псалм ругается на \Stringable, так как не может его найти @todo PHP8 удалить
     */
    public static function firstSubstrAfter(string $string, $substr, bool $withSubstr = false, int $start = 0): string
    {
        TypeValidator::validateOr($substr, ['string', 'array']);

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
     */
    public static function trimInString(string $string, string $replace = ' '): string
    {
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
     */
    public static function clearMultiSpaces(string $string, string $replace = ' '): string
    {
        if ($string === '' || $replace === '') return $string;

        return preg_replace('/\s+/', $replace, $string);
    }
}
