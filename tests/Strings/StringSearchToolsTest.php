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
 * Test for {@coversDefaultClass StringSearchTools}
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
     * Test for {@covers StringSearchTools::position()}
     */
    private function runTestPosition(): void
    {
        $testFunctionPosition = StringSearchTools::position(...);

        self::assertNull($testFunctionPosition('', ['str']));
        self::assertNull($testFunctionPosition('str', []));
        self::assertNull($testFunctionPosition('BBB', ['AAA']));

        self::assertEquals(0, $testFunctionPosition('AAA', ['AAA']));
        self::assertEquals(1, $testFunctionPosition('BAAA', ['AAA']));
        self::assertEquals(0, $testFunctionPosition('BAAA', ['AAA', 'B']));

        self::assertEquals(3, $testFunctionPosition('AAABBBCCC', ['BBB', 'CCC']));

        self::assertEquals(6, $testFunctionPosition('AAABBBCCC', ['BBB', 'CCC'], 5));

        self::assertEquals([0, 'AAA'], $testFunctionPosition('AAA', ['AAA'], 0, true, true));
        self::assertEquals([6, 'CCC'], $testFunctionPosition('AAABBBCCC', ['BBB', 'CCC'], 5, true, true));

        // * * * Проверка поддержки перебираемого

        $iterableFunction = function (): \Generator {
            yield 'str111';
            yield 'str222';
        };

        self::assertEquals(2, $testFunctionPosition('01str222', $iterableFunction()));
    }

    /**
     * Test for {@covers StringSearchTools::inCenter()}
     */
    private function runTestInCenter(): void
    {
        $testFunctionInCenter = StringSearchTools::inCenter(...);

        self::assertFalse($testFunctionInCenter('Я', 'Я'));
        self::assertFalse($testFunctionInCenter('Я', 'ЯГ'));
        self::assertFalse($testFunctionInCenter('ЯГ', 'Я'));
        self::assertFalse($testFunctionInCenter('AГ', 'Г'));

        self::assertFalse($testFunctionInCenter('ЯГШ', 'Я'));
        self::assertFalse($testFunctionInCenter('ЯГШ', 'Ш'));
        self::assertFalse($testFunctionInCenter('ЯГШ', 'Л'));
        self::assertFalse($testFunctionInCenter('ЯГШ', 'Q'));

        self::assertTrue($testFunctionInCenter('ЯГШ', 'Г'));
    }

    /**
     * Test for {@covers StringSearchTools::inString()}
     */
    private function runTestInString(): void
    {
        $testFunctionInString = StringSearchTools::inString(...);

        self::assertTrue($testFunctionInString('ЯБЪЧШ', ['start' => 'ЯБ']));
        self::assertFalse($testFunctionInString('ЯБЪЧШ', ['start' => 'БЪ']));

        self::assertTrue($testFunctionInString('ЯБЪЧШ', ['end' => 'ЧШ']));
        self::assertFalse($testFunctionInString('ЯБЪЧШ', ['end' => 'ЪЧ']));

        self::assertTrue($testFunctionInString('ЯБЪЧШ', ['center' => 'БЪЧ']));
        self::assertFalse($testFunctionInString('ЯБЪЧШ', ['center' => 'ЯБЪЧШ']));
        self::assertFalse($testFunctionInString('ЯБЪЧШ', ['center' => 'ЧШ']));
        self::assertFalse($testFunctionInString('ЯБЪЧШ', ['center' => 'ЯБ']));

        self::assertTrue($testFunctionInString('ЯБЪЧШ', ['content' => 'ЯБЪЧШ']));
        self::assertTrue($testFunctionInString('ЯБЪЧШ', ['content' => 'ЯБЪ']));
        self::assertTrue($testFunctionInString('ЯБЪЧШ', ['content' => 'ЪЧШ']));
        self::assertTrue($testFunctionInString('ЯБЪЧШ', ['content' => 'БЪЧ']));
        self::assertFalse($testFunctionInString('ЯБЪЧШ', ['content' => 'Q']));

        self::assertTrue($testFunctionInString('ЯБЪЧШЯ', ['border' => 'Я']));
        self::assertFalse($testFunctionInString('ЯБЪЧШ', ['border' => 'Я']));
        self::assertFalse($testFunctionInString('ЯБЪЧШ', ['border' => 'Ш']));

        // * * *

        self::assertTrue($testFunctionInString('ЯБЪЧШЯ', ['start' => 'ЯБ', 'end' => 'ШЯ', 'center' => 'БЪЧШ', 'content' => 'ЯБЪЧШЯ', 'border' => 'Я']));
        self::assertFalse($testFunctionInString('ЯБЪЧШЯ', ['start' => 'ЯБ1', 'end' => 'ШЯ', 'center' => 'БЪЧШ', 'content' => 'ЯБЪЧШЯ', 'border' => 'Я']));
        self::assertFalse($testFunctionInString('ЯБЪЧШЯ', ['start' => 'ЯБ', 'end' => 'ШЯ1', 'center' => 'БЪЧШ', 'content' => 'ЯБЪЧШЯ', 'border' => 'Я']));
        self::assertFalse($testFunctionInString('ЯБЪЧШЯ', ['start' => 'ЯБ', 'end' => 'ШЯ', 'center' => 'БЪЧ1', 'content' => 'ЯБЪЧШЯ', 'border' => 'Я']));
        self::assertFalse($testFunctionInString('ЯБЪЧШЯ', ['start' => 'ЯБ', 'end' => 'ШЯ', 'center' => 'БЪЧШ', 'content' => 'ЯБЪЧШ1', 'border' => 'Я']));
        self::assertFalse($testFunctionInString('ЯБЪЧШЯ', ['start' => 'ЯБ', 'end' => 'ШЯ', 'center' => 'БЪЧШ', 'content' => 'ЯБЪЧШЯ', 'border' => 'Я1']));
    }
}
