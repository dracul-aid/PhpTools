<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests;

use DraculAid\PhpTools\DateTime\Dictionary\DateTimeFormats;
use PHPUnit\Framework\TestCase;

/**
 * Расширение тест-класса под нужды библиотеки PhpTools
 */
class AbstractProjectTestCase extends TestCase
{
    /**
     * Сравнение таймштампов
     *
     * @param   mixed    $expected    Ожидаемое значение (верное значение)
     * @param   mixed    $actual      Проверяемое значение (что получилось в результате тестового вызова)
     * @param   string   $message     Сообщение, выводится при провале теста (Дополняет сообщение генерируемое функцией)
     * @return  void
     *
     * @todo PHP8 типизация аргументов функции
     */
    public static function assertTimestamp($expected, $actual, string $message = ''): void
    {
        if (!is_int($expected)) $message = ($message ? "{$message} " : '') . '$expected can be int, but it is a ' . gettype($expected);
        if (!is_int($expected)) $message = ($message ? "{$message} " : '') . '$actual can be int, but it is a ' . gettype($expected);

        if ($expected != $actual) $message = ($message ? "{$message} " : '')
            . '$expected = ' . date(DateTimeFormats::SQL_DATETIME, $expected)
            . ', but $actual = ' . date(DateTimeFormats::SQL_DATETIME, $actual);

        self::assertEquals($expected, $actual, $message);
    }
}
