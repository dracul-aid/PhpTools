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

use DraculAid\PhpTools\Strings\StringSearchTools;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see StringSearchTools}
 *
 * @run php tests/run.php tests/Strings/StringSearchToolsTest.php
 */
class StringSearchToolsTest extends TestCase
{
    public function testRun(): void
    {
        $this->runTestPosition();
        $this->runTestInCenter();
        $this->runTestInString();
    }

    /**
     * Test for {@see StringSearchTools::position()}
     */
    private function runTestPosition(): void
    {
        self::assertNull(StringSearchTools::position('', ['str']));
        self::assertNull(StringSearchTools::position('str', []));
        self::assertNull(StringSearchTools::position('BBB', ['AAA']));

        self::assertEquals(0, StringSearchTools::position('AAA', ['AAA']));
        self::assertEquals(1, StringSearchTools::position('BAAA', ['AAA']));
        self::assertEquals(0, StringSearchTools::position('BAAA', ['AAA', 'B']));

        self::assertEquals(3, StringSearchTools::position('AAABBBCCC', ['BBB', 'CCC']));

        self::assertEquals(6, StringSearchTools::position('AAABBBCCC', ['BBB', 'CCC'], 5));

        self::assertEquals([0, 'AAA'], StringSearchTools::position('AAA', ['AAA'], 0, true, true));
        self::assertEquals([6, 'CCC'], StringSearchTools::position('AAABBBCCC', ['BBB', 'CCC'], 5, true, true));

        // * * * Проверка поддержки перебираемого

        $iterableFunction = function (): \Generator {
            yield 'str111';
            yield 'str222';
        };

        self::assertEquals(2, StringSearchTools::position('01str222', $iterableFunction()));
    }

    /**
     * Test for {@see StringSearchTools::inCenter()}
     */
    private function runTestInCenter(): void
    {
        self::assertFalse(StringSearchTools::inCenter('Я', 'Я'));
        self::assertFalse(StringSearchTools::inCenter('Я', 'ЯГ'));
        self::assertFalse(StringSearchTools::inCenter('ЯГ', 'Я'));
        self::assertFalse(StringSearchTools::inCenter('AГ', 'Г'));

        self::assertFalse(StringSearchTools::inCenter('ЯГШ', 'Я'));
        self::assertFalse(StringSearchTools::inCenter('ЯГШ', 'Ш'));
        self::assertFalse(StringSearchTools::inCenter('ЯГШ', 'Л'));
        self::assertFalse(StringSearchTools::inCenter('ЯГШ', 'Q'));

        self::assertTrue(StringSearchTools::inCenter('ЯГШ', 'Г'));
    }

    /**
     * Test for {@see StringSearchTools::inString()}
     */
    private function runTestInString(): void
    {
        self::assertTrue(StringSearchTools::inString('ЯБЪЧШ', ['start' => 'ЯБ']));
        self::assertFalse(StringSearchTools::inString('ЯБЪЧШ', ['start' => 'БЪ']));

        self::assertTrue(StringSearchTools::inString('ЯБЪЧШ', ['end' => 'ЧШ']));
        self::assertFalse(StringSearchTools::inString('ЯБЪЧШ', ['end' => 'ЪЧ']));

        self::assertTrue(StringSearchTools::inString('ЯБЪЧШ', ['center' => 'БЪЧ']));
        self::assertFalse(StringSearchTools::inString('ЯБЪЧШ', ['center' => 'ЯБЪЧШ']));
        self::assertFalse(StringSearchTools::inString('ЯБЪЧШ', ['center' => 'ЧШ']));
        self::assertFalse(StringSearchTools::inString('ЯБЪЧШ', ['center' => 'ЯБ']));

        self::assertTrue(StringSearchTools::inString('ЯБЪЧШ', ['content' => 'ЯБЪЧШ']));
        self::assertTrue(StringSearchTools::inString('ЯБЪЧШ', ['content' => 'ЯБЪ']));
        self::assertTrue(StringSearchTools::inString('ЯБЪЧШ', ['content' => 'ЪЧШ']));
        self::assertTrue(StringSearchTools::inString('ЯБЪЧШ', ['content' => 'БЪЧ']));
        self::assertFalse(StringSearchTools::inString('ЯБЪЧШ', ['content' => 'Q']));

        self::assertTrue(StringSearchTools::inString('ЯБЪЧШЯ', ['border' => 'Я']));
        self::assertFalse(StringSearchTools::inString('ЯБЪЧШ', ['border' => 'Я']));
        self::assertFalse(StringSearchTools::inString('ЯБЪЧШ', ['border' => 'Ш']));

        // * * *

        self::assertTrue(StringSearchTools::inString('ЯБЪЧШЯ', ['start' => 'ЯБ', 'end' => 'ШЯ', 'center' => 'БЪЧШ', 'content' => 'ЯБЪЧШЯ', 'border' => 'Я']));
        self::assertFalse(StringSearchTools::inString('ЯБЪЧШЯ', ['start' => 'ЯБ1', 'end' => 'ШЯ', 'center' => 'БЪЧШ', 'content' => 'ЯБЪЧШЯ', 'border' => 'Я']));
        self::assertFalse(StringSearchTools::inString('ЯБЪЧШЯ', ['start' => 'ЯБ', 'end' => 'ШЯ1', 'center' => 'БЪЧШ', 'content' => 'ЯБЪЧШЯ', 'border' => 'Я']));
        self::assertFalse(StringSearchTools::inString('ЯБЪЧШЯ', ['start' => 'ЯБ', 'end' => 'ШЯ', 'center' => 'БЪЧ1', 'content' => 'ЯБЪЧШЯ', 'border' => 'Я']));
        self::assertFalse(StringSearchTools::inString('ЯБЪЧШЯ', ['start' => 'ЯБ', 'end' => 'ШЯ', 'center' => 'БЪЧШ', 'content' => 'ЯБЪЧШ1', 'border' => 'Я']));
        self::assertFalse(StringSearchTools::inString('ЯБЪЧШЯ', ['start' => 'ЯБ', 'end' => 'ШЯ', 'center' => 'БЪЧШ', 'content' => 'ЯБЪЧШЯ', 'border' => 'Я1']));
    }
}
