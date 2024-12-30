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

use DraculAid\PhpTools\Strings\Objects\StringIterator\Utf8IteratorObject;
use DraculAid\PhpTools\tests\Strings\Utf8ToolsTest;

/**
 * Набор функций для облегчения работы с UTF-8 кодировкой
 *
 * Оглавление:
 * <br>- {@see Utf8Tools::calculationCharLen()} Проводит вычисление длины в байтах читаемого символа UTF-8 строки
 * <br>- {@see Utf8Tools::clearFatChars()} Очистит UTF-8 строку от символов, размер которых превышает указанное число байт
 * <br>- {@see Utf8Tools::convertToUtf8mb3()} Очистит UTF-8 строку от 4 байтовых символов
 *
 * @link https://unicode.org/charts/nameslist/ Список символов UTF-8 по группам
 *
 * Test cases for class {@see Utf8ToolsTest}
 */
final class Utf8Tools
{
    /**
     * Проводит вычисление длины в байтах читаемого символа UTF-8 строки
     *
     * (!) Если передана строка, с более чем 1-им символом, то все равно вернет ответ только для первого символа строки
     *
     * @param   string   $firstByte   Символ, для которого нужно вернуть размер в байтах
     *
     * @return  int  Для пустой строки длина всегда будет 0
     *
     * @see Utf8IteratorObject::calculationCharLen() Аналогичная функция (функция существует в 2 экзеплярах по "историческим причинам")
     */
    public static function calculationCharLen(string $firstByte): int
    {
        if (strlen($firstByte) === 0) return 0;

        $charCode = ord($firstByte[0]);

        if ($charCode < 128) return 1;
        elseif ($charCode < 224) return 2;
        elseif ($charCode < 240) return 3;
        else return 4;

        /* TODO PHP8
        return match (true) {
            $charCode < 128 => 1,
            $charCode < 224 => 2,
            $charCode < 240 => 3,
            default => 4,
        };
        */
    }

    /**
     * Очистит UTF-8 строку от символов, размер которых превышает $fatSize байт
     *
     * Также смотри {@see Utf8Tools::convertToUtf8mb3()} - представляет собою "сахар", для удаления 4 байтовых символов
     *
     * @param   string      $string    Строка для очистки
     * @param   int<1, 4>   $fatSize   Размер символов в байтах, начиная с этого размера (включительно) символы будут удалены
     *
     * @return  string
     */
    public static function clearFatChars(string $string, int $fatSize): string
    {
        // В UTF-8 максимальный размер строки = 4 байта
        if ($string === '' || $fatSize < 1) return '';
        elseif ($fatSize > 4) return $string;
 
        $resultChar = null;
        $utf8IteratorObject = new Utf8IteratorObject($string);

        do {
            if ($resultChar !== null)
            {
                if ($utf8IteratorObject->getCharLen() <= $fatSize) $resultChar .= $utf8IteratorObject->current();
            }
            // нет смысла начинать запоминать новую строку до тех пор, пока не встретим первый символ запрещенного размера
            elseif ($utf8IteratorObject->getCharLen() > $fatSize)
            {
                if ($resultChar === null)
                {
                    if ($utf8IteratorObject->key(true) > 0) $resultChar = substr($string, 0, $utf8IteratorObject->key(true));
                    else $resultChar = '';
                }
                elseif ($utf8IteratorObject->getCharLen() <= $fatSize) $resultChar .= $utf8IteratorObject->current();
            }

            $utf8IteratorObject->next();
        } while ($utf8IteratorObject->valid());

        if ($resultChar === null) return $string;

        return $resultChar;
    }

    /**
     * Очистит UTF-8 строку от 4 байтовых символов
     *
     * Эта функция является синтаксическим сахаром для {@see Utf8Tools::clearFatChars()}
     *
     * @param   string   $string
     *
     * @return  string
     */
    public static function convertToUtf8mb3(string $string): string
    {
        return self::clearFatChars($string, 4);
    }
}
