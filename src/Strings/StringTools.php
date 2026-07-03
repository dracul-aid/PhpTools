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

use DraculAid\PhpTools\tests\Strings\StringToolsTest;

/**
 * Различные функции для облегчения работы со строками
 *
 * Оглавление
 * <br>{@see StringTools::lengthTrim()} Вернет длину строки без учета пробельных символов в начале и конце строки
 * <br>{@see StringTools::ipFilenameDecode()} Преобразует IP адрес в строку, пригодную для использования в качестве имен файлов
 * <br>{@see StringTools::ipFilenameEncode()} Преобразует IP адрес из формата имени файла, в IP адрес
 *
 * Test cases for class {@see StringToolsTest}
 *
 * @since 0.3.0
 */
final class StringTools
{
    /**
     * Вернет длину строки без учета пробельных символов в начале и конце строки
     *
     * (!) Очистка от пробелов проводится с помощью PHP функции {@see trim()}
     *
     * @param   string   $string           Строка для анализа
     * @param   string   $trimCharacters   Список "удаляемых символов" для {@see trim()}. Если пропущены, берется стандартный набор для {@see trim()}
     *
     * @return  int    Верент кол-во символов, без учета всех пробельных символов
     *
     */
    public static function lengthTrim(string $string, string $trimCharacters = ''): int
    {
        if ($trimCharacters !== '') return mb_strlen(trim($string, $trimCharacters));

        return mb_strlen(trim($string));
    }

    /**
     * Преобразует IP адрес в строку, пригодную для использования в качестве имен файлов
     *
     * Заменяет IP адрес, где разделителем частей IP v4 адреса служат символ 'p', для ip v6 'x'
     * Подобный споод кодирования ip адресов используется для создания имен файлов.
     *
     * (!) Обратное декодирование производит {@see StringTools::ipFilenameDecode()}
     *
     * @param   string   $string    Строка источник данных
     *
     * @return  string    Вернет строку с ip адресом
     *
     */
    public static function ipFilenameEncode(string $string) : string
    {
        // заменяем в строке разделители
        return str_replace(array( '.', ':' ), array( 'p', 'x' ), $string);
    }

    /**
     * Преобразует IP адрес из формата имени файла, в IP адрес
     *
     * Восстанавливает IP адрес, где разделителем частей IP v4 адреса служат символ 'p', для ip v6 'x'
     * Подобный споод кодирования IP адресов используется для создания имен файлов.
     *
     * (!) Кодирование в этот формат производит {@see StringTools::ipFilenameEncode()}
     *
     * @param   string   $string   Cтрока источник данных
     *
     * @return  string   Вернет строку ip адресом без разделительных символов
     *
     */
    public static function ipFilenameDecode(string $string) : string
    {
        return str_replace(array( 'p', 'x' ), array( '.', ':' ), $string);
    }

    /**
     * Продублирует строку столько раз, что бы она уместилась в указанную длину
     *
     * (!) Если $length не кратна длине $string, последняя часть вставки будет включать только часть $string
     *
     * @param   string        $string   Строка для повторения
     * @param   int<0, max>   $length   Длина результирующий строки в символах
     *
     * @return string
     *
     * @since 1.3.0
     */
    public static function repeat(string $string, int $length): string
    {
        if ($length < 1 || $string === '') return '';

        /** @var int<1, max> $stringChars Кол-во символов в строке для повторов */
        $stringChars = mb_strlen($string);

        /** @var int<1, max> $stringChars Кол-во вставок строки, что бы с запасом покрыть нужную длину */
        $countRepeat = (int)ceil($length / $stringChars);

        if ($countRepeat * $stringChars === $length) return str_repeat($string, $countRepeat);

        return mb_substr(
            str_repeat($string, $countRepeat),
            0,
            $length
        );
    }
}
