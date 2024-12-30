<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests\Strings;

use DraculAid\PhpTools\Strings\Utf8Tools;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see Utf8Tools}
 *
 * @run php tests/run.php tests/Strings/Utf8ToolsTest.php
 */
class Utf8ToolsTest extends TestCase
{
    /**
     * Test for {@see Utf8Tools::calculationCharLen()}
     *
     * @return void
     */
    public function testCalculationCharLen(): void
    {
        self::assertEquals(0, Utf8Tools::calculationCharLen(''));

        self::assertEquals(1, Utf8Tools::calculationCharLen(mb_chr(127)));
        self::assertEquals(1, Utf8Tools::calculationCharLen('W'));

        self::assertEquals(2, Utf8Tools::calculationCharLen(mb_chr(128)));
        self::assertEquals(2, Utf8Tools::calculationCharLen(chr(128) . chr(1)));
        self::assertEquals(2, Utf8Tools::calculationCharLen(chr(223) . chr(1)));
        self::assertEquals(2, Utf8Tools::calculationCharLen(mb_chr(2047)));
        self::assertEquals(2, Utf8Tools::calculationCharLen('Я'));

        self::assertEquals(3, Utf8Tools::calculationCharLen(mb_chr(2048)));
        self::assertEquals(3, Utf8Tools::calculationCharLen(chr(224) . chr(1)));
        self::assertEquals(3, Utf8Tools::calculationCharLen(chr(239) . chr(1)));

        self::assertEquals(4, Utf8Tools::calculationCharLen(chr(240) . chr(1)));
    }

    /**
     * Test for {@see Utf8Tools::clearFatChars()}
     *
     * @return void
     */
    public function testFatChars(): void
    {
        /** @psalm-suppress InvalidArgument Нужно для проверки работы функции */
        self::assertEquals('', Utf8Tools::clearFatChars('', 0));
        /** @psalm-suppress InvalidArgument Нужно для проверки работы функции */
        self::assertEquals('', Utf8Tools::clearFatChars('abd', 0));

        // оставит только 1 байтовые символы
        self::assertEquals('', Utf8Tools::clearFatChars('', 1));
        self::assertEquals('', Utf8Tools::clearFatChars('ЯблокиНаСнегу', 1));
        self::assertEquals('', Utf8Tools::clearFatChars(mb_chr(2048) . mb_chr(2048), 1)); // это 3 байтовые символы
        self::assertEquals('abcwzf', Utf8Tools::clearFatChars('abcwzfЯ', 1));
        self::assertEquals('abcwzf', Utf8Tools::clearFatChars('ЯблокиabcнаwzfСнегу', 1));

        // оставит только 2 байтовые символы
        self::assertEquals('', Utf8Tools::clearFatChars('', 2));
        self::assertEquals('aswdf', Utf8Tools::clearFatChars('aswdf', 2));
        self::assertEquals('ЯблокиНаСнегу', Utf8Tools::clearFatChars('ЯблокиНаСнегу', 2));
        self::assertEquals('', Utf8Tools::clearFatChars(mb_chr(2048) . mb_chr(2048), 2)); // это 3 байтовые символы
        self::assertEquals('ЯблокиabcнаwzfСнегу', Utf8Tools::clearFatChars(mb_chr(2048) . 'ЯблокиabcнаwzfСнегу', 2));
        self::assertEquals('ЯблокиabcнаwzfСнегу', Utf8Tools::clearFatChars('ЯблокиabcнаwzfСнегу' . mb_chr(2048), 2));

        // оставит только 3 байтовые символы
        self::assertEquals('', Utf8Tools::clearFatChars('', 3));
        self::assertEquals('aswdf', Utf8Tools::clearFatChars('aswdf', 3));
        self::assertEquals('ЯблокиНаСнегу', Utf8Tools::clearFatChars('ЯблокиНаСнегу', 3));
        self::assertEquals(mb_chr(2048) . mb_chr(2048), Utf8Tools::clearFatChars(mb_chr(2048) . mb_chr(2048), 3)); // это 3 байтовые символы
        self::assertEquals('', Utf8Tools::clearFatChars(chr(240) . chr(1), 3)); // это 4 байтовые символ

        // оставит только 4 байтовые символы
        $this->testFatCharsFor4Bits([Utf8Tools::class, 'clearFatChars'], true);

        // больше 4 байт
        $this->testFatCharsForMore4Bits();
    }

    /**
     * Test for {@see Utf8Tools::convertToUtf8mb3()}
     *
     * @return void
     */
    public function testConvertToUtf8mb3(): void
    {
        $this->testFatCharsFor4Bits([Utf8Tools::class, 'convertToUtf8mb3'], false);
    }

    /**
     * Проведет тестирование функции обрезки 4 байтовых символов
     *
     * @param   callable   $function         Проверяемая функция
     * @param   bool       $secondArgument   Нужно ли передать 2 аргумент (указание, что "удаляем" 4 байтовые символы)
     *
     * @return void
     */
    private function testFatCharsFor4Bits(callable $function, bool $secondArgument): void
    {
        $variants = [
            ['', ''],
            ['aswdf', 'aswdf'],
            ['ЯблокиНаСнегу', 'ЯблокиНаСнегу'],
            [mb_chr(2048) . mb_chr(2048), mb_chr(2048) . mb_chr(2048)],
            [chr(240) . chr(1), chr(240) . chr(1)],
        ];

        foreach ($variants as $number => [$expected, $testString])
        {
            /** @psalm-suppress InvalidArrayAccess Мы уверенны, что всегда тут передаем массив, если начнем передавать что-то другое, хотим упасть */
            $errorDesc = "Function {$function[1]} variant #{$number}: {$testString}";

            self::assertEquals(
                $expected,
                $secondArgument ? $function($testString, 4) : $function($testString),
                $errorDesc
            );
        }
    }

    /**
     * Проверка "очистки" символов UTF-8 с длиною более 4 символов (вынесено в отдельную функцию, что бы экранировать ошибки PSALM)
     *
     * @return void
     *
     * @psalm-suppress InvalidArgument Нужно для проверки работы функции
     */
    private function testFatCharsForMore4Bits(): void
    {
        self::assertEquals('яблоки', Utf8Tools::clearFatChars('яблоки', 5));
        self::assertEquals('aswdf', Utf8Tools::clearFatChars('aswdf', 5));
        self::assertEquals('ЯблокиНаСнегу', Utf8Tools::clearFatChars('ЯблокиНаСнегу', 5));
        self::assertEquals(mb_chr(2048) . mb_chr(2048), Utf8Tools::clearFatChars(mb_chr(2048) . mb_chr(2048), 5)); // это 3 байтовые символы
        self::assertEquals(chr(240) . chr(1), Utf8Tools::clearFatChars(chr(240) . chr(1), 5)); // это 4 байтовые символ
    }
}
