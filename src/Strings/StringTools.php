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
    public static function ipFilenameEncode( string $string ) : string
    {
        // заменяем в строке разделители
        return str_replace( array( '.', ':' ), array( 'p', 'x' ), $string );
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
    public static function ipFilenameDecode( string $string ) : string
    {
        return str_replace( array( 'p', 'x' ), array( '.', ':' ), $string );
    }
}
