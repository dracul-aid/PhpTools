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

use DraculAid\PhpTools\Functions\CallFunctionHelper;

/**
 * Преобразование массивов в строку и обратно
 *
 * Оглавление
 * <br>{@see ArrayAndStringTools::arrayToStringWithoutEmpty()} Преобразует массив в строку, аналогично PHP функции {@see implode()} но с игнорированием пустых значений
 * <br>{@see ArrayAndStringTools::subStringToArray()} Разбивает строку на подстроки указанной длины и помещает все подстроки в массив
 */
final class ArrayAndStringTools
{
    /**
     * Преобразует массив в строку, аналогично PHP функции {@see implode()} но с игнорированием пустых значений
     *
     * @param   string          $separator          Строка разделитель
     * @param   iterable        $array              Массив или перечисляемое для преобразования
     * @param   bool|callable   $ignoreFunction     Функция, проверяющая на "пустоту"
     *                                              <br>FALSE: empty()
     *                                              <br>TRUE: empty(), но число 0 не считается пустотой
     *                                              <br>callable: любая функция: $ignoreFunction($value, $index)
     *
     * @return string
     *
     * @todo PHP8 аргументы функции
     */
    public static function arrayToStringWithoutEmpty(string $separator, iterable $array, $ignoreFunction = false): string
    {
        $_return = '';

        $afterFirstElement = false;
        foreach ($array as $index => $value)
        {
            // вычисление, пустое значение или нет
            if ($ignoreFunction === false) $notEmpty = !empty($value);
            elseif ($ignoreFunction === true) $notEmpty = (is_numeric($value) || !empty($value));
            else $notEmpty = CallFunctionHelper::exe($ignoreFunction, $value, $index);

            // не пустое значение поместим в создаваемую строку
            if ($notEmpty) {
                if ($afterFirstElement)
                {
                    $_return .= "{$separator}{$value}";
                }
                else
                {
                    $_return = (string) $value;
                    $afterFirstElement = true;
                }
            }
        }

        return $_return;
    }

    /**
     * Разбивает строку на подстроки указанной длины и помещает все подстроки в массив
     *
     * @param   string   $string   Строка для обработки
     * @param   int      $len      Длина каждого блока (в символах)
     * @param   bool     $right    Направление обрезания. Если ЛОЖ (по умолчанию), то слева на право, если ИСТИНА, то с права на лево (т.е. в обратном порядке)
     * @param   bool     $utf8     TRUE если нужно применять `mb_` функции или FALSE если нет (если длина указана в байтах)
     *
     * @return string[]
     */
    public static function subStringToArray(string $string, int $len, bool $right = false, bool $utf8 = true): array
    {
        // временный массив для троек цифр
        $_return = array();

        // если с права на лево - делаем длину отрицательной
        if ($right) $len = -1 * $len;

        /** @var callable $strlen Функция, с помощью которой проводится обрезание строки ({@see mb_substr()} или {@see substr()}) */
        $substrFunction = $utf8 ? 'mb_substr' : 'substr';

        // * * *

        // пока длина строки >0
        while (!empty($string))
        {
            // если с лево направо
            if ($right)
            {
                // берем тройку символов с конца строки
                $_return[] = $substrFunction($string, $len);
                // обрезаем исходную строку
                $string = $substrFunction($string, 0, $len);
            }
            // если с права на лево
            else
            {
                // берем тройку символов с начала строки
                $_return[] = $substrFunction($string, 0, $len);
                // обрезаем исходную строку
                $string = $substrFunction($string, $len);
            }
        }

        return $_return;
    }
}
