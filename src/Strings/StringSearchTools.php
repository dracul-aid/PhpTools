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

use DraculAid\PhpTools\tests\Strings\StringSearchToolsTest;

/**
 * Статический класс для осуществления различных действий связанных с поиском внутри строк, если в описании функции не
 * указано иначе то - работает с UTF8
 *
 * Оглавление:
 * <br>{@see StringSearchTools::position()} - Вернет первое вхождение любой подстроки в строке
 * <br>{@see StringSearchTools::inCenter()} - Проверяет, нет ли в центре строки, указанной подстроки
 * <br>{@see StringSearchTools::inString()} - Проверит, строка начинается, кончается или включает в себя указанную подстроку
 *
 * Test cases for class {@see StringSearchToolsTest}
 */
final class StringSearchTools
{
    /**
     * Вернет первое вхождение любой подстроки в строке
     *
     * @param   string                         $string         Строка в которой будет вестись поиск
     * @param   iterable<string|\Stringable>   $searchList     Любое перебираемое подстрок для поиска
     * @param   int                            $start          Позиция начала поиска
     * @param   bool                           $utf8           Поиск позиции, считая строку UTF8 строкой
     * @param   bool                           $returnArray    TRUE если нужно вернуть массив:
     *                                                         <br>* 0: найденная позиция
     *                                                         <br>* 1: найденная подстрока
     *
     * @return  null|int|array   Вернет позицию первой найденной подстроки или NULL, если ни одна подстрока не найдена
     *                           Также может вернуть массив, с номером позиции и найденной подстрокой, см параметр $return_array
     *
     * @psalm-suppress UnusedParam Псалм считает, что большая часть аргументов функции не используется внутри функции, но это не так
     * @psalm-suppress UndefinedDocblockClass В PHP 7.4 Псалм ругается на \Stringable, так как не может его найти @todo PHP8 удалить
     */
    public static function position(string $string, iterable $searchList, int $start = 0, bool $utf8 = true, bool $returnArray = false):  null|int|array
    {
        if ($string === '' || (is_countable($searchList) && count($searchList) === 0)) return null;

        $_returnPosition = null;
        $_returnString = null;

        // * * *

        foreach ($searchList as $searchString)
        {
            /** @psalm-suppress InvalidCast Преобразование в строку возможно, PSALM просто не знает про \Stringable в PHP 7.4 @todo PHP8 удалить */
            if (!is_string($searchString)) $searchString = (string)$searchString;

            if ($searchString === '')
            {
                continue;
            }

            if ($utf8) $searchStringStartPosition = mb_strpos($string, $searchString, $start);
            else $searchStringStartPosition = strpos($string, $searchString, $start);

            if ($searchStringStartPosition === 0)
            {
                $_returnPosition = $searchStringStartPosition;
                $_returnString = $searchString;
                break;
            }
            elseif ($searchStringStartPosition !== false && ($_returnPosition === null || $_returnPosition > $searchStringStartPosition))
            {
                $_returnPosition = $searchStringStartPosition;
                $_returnString = $searchString;
            }
        }

        // * * *

        return match (true) {
            (bool) $_returnString === false => null,
            $returnArray === true => [$_returnPosition, $_returnString],
            default => $_returnPosition,
        };
    }

    /**
     * Проверяет, нет ли в центре строки `$haystack`, подстроки `$needle`
     * (т.е. подстрока есть в строке, но при этом строка не начинается и не кончается подстрокой)
     *
     * @param   string   $haystack   Строка в которой идет поиск
     * @param   string   $needle     Строка для поиска
     *
     * @return  bool   Вернет FALSE (если подстроки нет в строке, она начинается или заканчивается на подстроку)
     */
    public static function inCenter(string $haystack, string $needle): bool
    {
        if (strlen($needle) + 2 > strlen($haystack)) return false;

        $position = strpos($haystack, $needle);

        return !(
            ! (bool) $position
            || ($position + strlen($needle) + 1 > strlen($haystack))
        );
    }

    /**
     * Проверит, строка начинается, кончается или включает в себя указанную подстроку
     * (может проверить несколько условий разом)
     *
     * @param    string                 $string   Строка для проверки
     * @param    array<string, string>  $filter   Фильтр поиска:
     *                                            <br>* 'start': начинается с указанной строки
     *                                            <br>* 'end': заканчивается на указанную строку
     *                                            <br>* 'center': включает указанную строку (но не начинается и не заканчивается на нее)
     *                                            <br>* 'content': в любом месте строки
     *                                            <br>* 'border': в начале И конце строки
     *
     * @return   bool   Вернет TRUE если строка удовлетворяет условию или FALSE в противном случае
     */
    public static function inString(string $string, array $filter): bool
    {
        // Если строка в любом месте
        if (!empty($filter['content']) && !str_contains($string, $filter['content'])) return false;

        // если строка должна начинаться с подстроки
        if (!empty($filter['start']) && !str_starts_with($string, $filter['start'])) return false;

        // если строка должна заканчиваться подстрокой
        if (!empty($filter['end']) && !str_ends_with($string, $filter['end'])) return false;

        // если строка в центре
        if (!empty($filter['center']) && !StringSearchTools::inCenter($string, $filter['center'])) return false;

        // если в начале и конце
        if (!empty($filter['border']) && (!str_starts_with($string, $filter['border']) || !str_ends_with($string, $filter['border']))) return false;

        // * * *

        return true;
    }
}
