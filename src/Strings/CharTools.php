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

use DraculAid\PhpTools\Strings\Components\CharTypes;
use DraculAid\PhpTools\tests\Strings\CharToolsTest;

/**
 * Статический класс для работы с символами (строкой в 1-цу размера). Корректно работает с символами до 127 позиции
 * (т.е. цифрами, латинкими буквами)
 *
 * Оглавление:
 * @see CharTools::CODE_ABC_UPPER - Позиции с которых начинаются и заканчиваются заглавные буквы
 * @see CharTools::CODE_ABC_UPPER - Позиции с которых начинаются и заканчиваются строчные буквы
 * @see CharTools::CODE_NUMBER - Позиции с которых начинаются и заканчиваются символы цифр (10-тичных цифр)
 * --- Проверка типов символов
 * @see CharTools::getType() - Вернет тип символа
 * @see CharTools::isAbc() - Является ли указанный символ буквой латинского алфавита
 * @see CharTools::isAbcLower() - Является ли указанный символ строчной буквой латинского алфавита
 * @see CharTools::isAbcUpper() - Является ли указанный символ заглавной буквой латинского алфавита
 * @see CharTools::isNumber() - Является ли указанный символ цифрой (отрицательное число - провалит проверку)
 * @see CharTools::isHex() - Является ли указанный символ 16-тиричной цифрой
 * --- Функции проверки символов
 * @see CharTools::isStartNameOfVar() - Проверяет, удовлетворяет ли переданный символ правилу начала имен переменных
 * @see CharTools::isInsideNameOfVar() - Проверяет, символ является символом, допустимым внутри имени переменной (т.е. кроме первого символа)
 *
 * Test cases for class {@see CharToolsTest}
 */
final class CharTools
{
    /**
     * Позиции с которых начинаются и заканчиваются заглавные буквы
     * Массив: [начало, конец]
     */
    public const CODE_ABC_UPPER = [65, 90];

    /**
     * Позиции с которых начинаются и заканчиваются строчные буквы
     * Массив: [начало, конец]
     */
    public const CODE_ABC_LOWER = [97, 122];

    /**
     * Позиции с которых начинаются и заканчиваются символы цифр (10-тичных цифр)
     * Массив: [начало, конец]
     */
    public const CODE_NUMBER = [48, 57];

    /**
     * Вернет тип символа
     *
     * @see \DraculAid\PhpTools\Strings\Components\CharTypes Хранит типы символов
     *
     * @param    string      $char       Анализируемый символ
     * @param    null|bool   $onlyAbc    TRUE - если нужно вернуть только тип буквы, или FALSE - если любая не буква
     *
     * @return   false|int см константы {@see \DraculAid\PhpTools\Strings\Components\CharTypes}
     *
     * @todo PHP8 типизация аргументов и результатов работы функции
     */
    public static function getType(string $char, ?bool $onlyAbc = null)
    {
        if ($char === '' || !empty($char[1])) return CharTypes::IS_NOT_CHAR;

        // * * *

        $char_int = ord($char);

        if ($onlyAbc !== false)
        {
            if ($char_int >= self::CODE_ABC_LOWER[0] && $char_int <= self::CODE_ABC_LOWER[1]) return CharTypes::IS_ABC_LOWER;
            elseif ($char_int >= self::CODE_ABC_UPPER[0] && $char_int <= self::CODE_ABC_UPPER[1]) return CharTypes::IS_ABC_UPPER;
        }

        if ($onlyAbc !== true)
        {
            if ($char_int >= self::CODE_NUMBER[0] && $char_int <= self::CODE_NUMBER[1]) return CharTypes::IS_NUMBER;
        }

        return CharTypes::IS_OTHER;
    }

    /**
     * Проверит, является ли указанный символ буквой латинского алфавита
     *
     * @param   string   $char   Проверяемый символ
     *
     * @return   bool   Вернет TRUE если символ является буквой
     *
     * (!) Если передан не символ, а строка (т.е. длина строки более 1-го символа), вернет FALSE
     *
     * @link https://en.wikipedia.org/wiki/ASCII
     */
    public static function isAbc(string $char): bool
    {
        if ($char === '' || !empty($char[1])) return false;

        // * * *

        $char_int = ord($char);

        // Заглавные буквы занимают 65-90 позиции, строчные 97-122
        return ($char_int >= self::CODE_ABC_UPPER[0] && $char_int <= self::CODE_ABC_UPPER[1]) || ($char_int >= self::CODE_ABC_LOWER[0] && $char_int <= self::CODE_ABC_LOWER[1]);
    }

    /**
     * Проверит, является ли указанный символ прописной буквой латинского алфавита (т.е. маленькой буквой)
     *
     * @param   string   $char   Проверяемый символ
     *
     * @return   bool   Вернет TRUE если символ является буквой
     *
     * (!) Если передан не символ, а строка (т.е. длина строки более 1-го символа), вернет FALSE
     *
     * @link https://en.wikipedia.org/wiki/ASCII
     */
    public static function isAbcLower(string $char): bool
    {
        if ($char === '' || !empty($char[1])) return false;

        // * * *

        $char_int = ord($char);

        // Заглавные буквы занимают 65-90 позиции, строчные 97-122
        return $char_int >= self::CODE_ABC_LOWER[0] && $char_int <= self::CODE_ABC_LOWER[1];
    }

    /**
     * Проверит, является ли указанный символ заглавной буквой латинского алфавита (т.е. большой буквой)
     *
     * @param   string   $char   Проверяемый символ
     *
     * @return   bool   Вернет TRUE если символ является буквой
     *
     * (!) Если передан не символ, а строка (т.е. длина строки более 1-го символа), вернет FALSE
     *
     * @link https://en.wikipedia.org/wiki/ASCII
     */
    public static function isAbcUpper(string $char): bool
    {
        if ($char === '' || !empty($char[1])) return false;

        // * * *

        $char_int = ord($char);

        // Заглавные буквы занимают 65-90 позиции, строчные 97-122
        return $char_int >= self::CODE_ABC_UPPER[0] && $char_int <= self::CODE_ABC_UPPER[1];
    }

    /**
     * Проверит, является ли указанный символ цифрой (отрицательное число - провалит проверку)
     *
     * @param   string   $char   Проверяемый символ
     *
     * @return   bool   Вернет TRUE если символ является цифрой
     *
     * (!) Если передан не символ, а строка (т.е. длина строки более 1-го символа), вернет FALSE
     *
     * @link https://en.wikipedia.org/wiki/ASCII
     */
    public static function isNumber(string $char ) : bool
    {
        // если передан не символ
        if ($char === '' || !empty($char[1])) return false;

        // код символа
        $char_int = ord($char);

        // Цифры занимают 48-57 позиции
        return $char_int >= self::CODE_NUMBER[0] && $char_int <= self::CODE_NUMBER[1];
    }

    /**
     * Проверит, является ли указанный символ 16-тиричной цифрой
     *
     * @param   string   $char   Проверяемый символ
     *
     * @return   bool   Вернет TRUE если символ является 16-ой цифрой
     *
     * (!) Если передан не символ, а строка (т.е. длина строки более 1-го символа), вернет FALSE
     *
     * @link https://en.wikipedia.org/wiki/ASCII
     */
    public static function isHex(string $char ) : bool
    {
        // если передан не символ
        if ($char === '' || !empty($char[1])) return false;

        // код символа
        $char_int = ord($char);

        // Цифры занимают 48-57 позиции
        return ($char_int >= self::CODE_NUMBER[0] && $char_int <= self::CODE_NUMBER[1])
            // символы: abcdef
            || ($char_int > 96 && $char_int < 103)
            // символы: ABCDEF
            || ($char_int > 64 && $char_int < 71);
    }

    /**
     * Проверяет, удовлетворяет ли переданный символ правилу начала имен переменных
     * (т.е. это должна быть буква или символ подчеркивания)
     *
     * @see self::isInsideNameOfVar() - Проверит, подходит ли символ для использования в середине имени переменной
     *
     * @param   string   $char   Проверяемый символ
     *
     * @return  bool   Вернет TRUE если символ можно использовать в качестве начала имени переменной
     *
     * (!) Если передан не символ, а строка (т.е. длина строки более 1-го символа), вернет FALSE
     */
    public static function isStartNameOfVar(string $char ): bool
    {
        if ($char === '' || !empty($char[1])) return false;

        // переменная может начинаться или с подчеркивания, или с латинской буквы
        return $char === '_' || self::isAbc($char);
    }

    /**
     * Проверяет, символ является символом, допустимым внутри имени переменной (т.е. кроме первого символа)
     *
     * @see self::isStartNameOfVar() - Прверит, может ли символ использоваться в начале строк с именем переменной
     *
     * @param   string   $char   Проверяемый символ
     *
     * @return  bool   Вернет TRUE если символ можно использовать в качестве начала имени переменной
     *
     * (!) Если передан не символ, а строка (т.е. длина строки более 1-го символа), вернет FALSE
     * (!) Символ должен быть буквой, цифрой или символом подчеркивания
     */
    public static function isInsideNameOfVar(string $char ): bool
    {
        return self::isStartNameOfVar($char) || self::isNumber($char);
    }
}
