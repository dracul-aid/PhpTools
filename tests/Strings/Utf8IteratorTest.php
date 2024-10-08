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

use DraculAid\PhpTools\Classes\ClassNotPublicManager;
use DraculAid\PhpTools\Strings\Utf8Iterator;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see Utf8Iterator}
 *
 * @run php tests/run.php tests/Strings/Utf8IteratorTest.php
 *
 * @deprecated Будет удален, начиная с версии 0.5, так как {@see Utf8Iterator} будет замещен более новой реализацией
 */
class Utf8IteratorTest extends TestCase
{
    /**
     * @return void
     *
     * @psalm-suppress DeprecatedClass пока не удалили устаревший класс, нужна и проверка
     */
    public function testRun(): void
    {
        $this->runTestCalculationCharLen();

        $this->runTestGet();
        $this->runTestCounter();
        $this->runTestCursorSetAndGetAndReadChar();
        $this->runTestReadString();
        $this->runTestGetIterator();
    }

    /**
     * Test for {@see Utf8Iterator::calculationCharLen()}
     *
     * @psalm-suppress DeprecatedClass пока не удалили устаревший класс, нужна и проверка
     */
    private function runTestCalculationCharLen(): void
    {
        self::assertEquals(1, Utf8Iterator::calculationCharLen(mb_chr(127)));

        self::assertEquals(2, Utf8Iterator::calculationCharLen(mb_chr(128)));
        self::assertEquals(2, Utf8Iterator::calculationCharLen(mb_chr(2047)));

        self::assertEquals(3, Utf8Iterator::calculationCharLen(mb_chr(2048)));
    }

    /**
     * Test for {@see Utf8Iterator::get()}
     *
     * @psalm-suppress DeprecatedClass пока не удалили устаревший класс, нужна и проверка
     */
    private function runTestGet(): void
    {
        $testString = new Utf8Iterator('Яблоки in snow');

        self::assertEquals('Яблоки in snow', $testString->get());
    }

    /**
     * Test for {@see Utf8Iterator::length()}
     * Test for {@see Utf8Iterator::count()}
     *
     * @psalm-suppress DeprecatedClass пока не удалили устаревший класс, нужна и проверка
     */
    private function runTestCounter(): void
    {
        $testString = new Utf8Iterator('Яблоки in snow');

        self::assertEquals(14, $testString->length());
        self::assertEquals(14, $testString->length(false));
        self::assertEquals(14, $testString->count());

        self::assertEquals(20, $testString->length(true));
    }

    /**
     * Test for {@see Utf8Iterator::cursorSet()}
     * Test for {@see Utf8Iterator::cursorGet()}
     * Test for {@see Utf8Iterator::readChar()}
     *
     * @psalm-suppress DeprecatedClass пока не удалили устаревший класс, нужна и проверка
     */
    private function runTestCursorSetAndGetAndReadChar(): void
    {
        $testString = new Utf8Iterator('Яблоки in snow');
        $this->cursorEquals($testString, 0, 0, 2, 'Я');

        $testString->cursorSet(1);
        $this->cursorEquals($testString, 1, 2, 2, 'б');
        $testString->cursorSet(2);
        $this->cursorEquals($testString, 2, 4, 2, 'л');

        $testString->cursorSet(6);
        $this->cursorEquals($testString, 6, 12, 1, ' ');
        $testString->cursorSet(7);
        $this->cursorEquals($testString, 7, 13, 1, 'i');

        $testString->cursorTo(2);
        $this->cursorEquals($testString, 9, 15, 1, ' ');
        $testString->cursorTo(-1);
        $this->cursorEquals($testString, 8, 14, 1, 'n');

        $testString->cursorSet(3);
        $this->cursorEquals($testString, 3, 6, 2, 'о');

        $testString->cursorSet(0);
        $this->cursorEquals($testString, 0, 0, 2, 'Я');

        $testString->cursorSet(1000);
        $this->cursorEquals($testString, 14, 20, 0, '');

        // * * *

        $testString->cursorSet(0);
        self::assertEquals('Я', $testString->readChar());
        self::assertEquals('Я', $testString->readChar());

        self::assertEquals('Я', $testString->readChar(1));
        self::assertEquals('б', $testString->readChar());

        self::assertEquals('б', $testString->readChar(5));
        self::assertEquals(' ', $testString->readChar(1));
        self::assertEquals('i', $testString->readChar());
    }

    /**
     * Test for {@see Utf8Iterator::readString()}
     *
     * @psalm-suppress DeprecatedClass пока не удалили устаревший класс, нужна и проверка
     */
    private function runTestReadString(): void
    {
        $testString = new Utf8Iterator('Яблоки in snow');

        self::assertEquals('Яб', $testString->readString(2));
        self::assertEquals('Яб', $testString->readString(2, false));
        self::assertEquals('Яб', $testString->readString(2, true));
        self::assertEquals('л', $testString->readChar());
        self::assertEquals('лок', $testString->readString(3));

        $testString->cursorSet(7);
        self::assertEquals('in', $testString->readString(2));
        self::assertEquals('in', $testString->readString(2, true));
        self::assertEquals(' ', $testString->readChar());
    }

    /**
     * Test for {@see Utf8Iterator::getIterator()}
     *
     * @psalm-suppress DeprecatedClass пока не удалили устаревший класс, нужна и проверка
     */
    private function runTestGetIterator(): void
    {
        $testString = new Utf8Iterator('ABCЯВГ123ϽȾ');
        self::assertEquals(['A', 'B', 'C', 'Я', 'В', 'Г', '1', '2', '3', 'Ͻ', 'Ⱦ'], iterator_to_array($testString));
    }

    /**
     * Test for {@see Utf8Iterator::utf8Generator()}
     *
     * @psalm-suppress DeprecatedClass пока не удалили устаревший класс, нужна и проверка
     */
    private function runTestGenerator(): void
    {
        $testString = Utf8Iterator::utf8Generator('ABCЯВГ123ϽȾ');
        self::assertEquals(['A', 'B', 'C', 'Я', 'В', 'Г', '1', '2', '3', 'Ͻ', 'Ⱦ'], iterator_to_array($testString));
    }

    /**
     * @param   Utf8Iterator   $testString
     * @param   int            $char
     * @param   int            $byte
     * @param   int            $len
     * @param   string         $readChar
     *
     * @return void
     *
     * @psalm-suppress DeprecatedClass пока не удалили устаревший класс, нужна и проверка
     */
    private function cursorEquals(Utf8Iterator $testString, int $char, int $byte, int $len, string $readChar): void
    {
        self::assertEquals($char, $testString->cursorGet());

        self::assertEquals($char, ClassNotPublicManager::readProperty($testString, 'cursorChar'));
        self::assertEquals($byte, ClassNotPublicManager::readProperty($testString, 'cursorByte'));
        self::assertEquals($len, ClassNotPublicManager::readProperty($testString, 'cursorLen'));

        self::assertEquals($readChar, $testString->readChar());
    }
}
