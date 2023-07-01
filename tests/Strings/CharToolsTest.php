<?php

namespace DraculAid\PhpTools\tests\Strings;

use DraculAid\PhpTools\Strings\CharTools;
use DraculAid\PhpTools\Strings\Components\CharTypes;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see CharTools}
 *
 * @run php tests/run.php tests/Strings/CharToolsTest.php
 */
class CharToolsTest extends TestCase
{
    public function testRun(): void
    {
        $this->runTestIsAbc();
        $this->runTestIsAbcLow();
        $this->runTestIsAbcUpper();
        $this->runTestIsNumber();
        $this->runTestIsHex();
        $this->runTestGetType();

        $this->runTestIsStartNameOfVar();
        $this->runTestIsInsideNameOfVar();
    }

    /**
     * Test for {@see CharTools::getType()}
     */
    private function runTestGetType(): void
    {
        self::assertFalse(CharTools::getType('123'));

        self::assertEquals(0, CharTools::getType(''));
        self::assertEquals(0, CharTools::getType('+'));
        self::assertEquals(0, CharTools::getType('-'));
        self::assertEquals(0, CharTools::getType('!'));
        self::assertEquals(0, CharTools::getType('.'));

        self::assertEquals(CharTypes::IS_ABC_LOWER, CharTools::getType('a'));
        self::assertEquals(CharTypes::IS_ABC_LOWER, CharTools::getType('z'));

        self::assertEquals(CharTypes::IS_ABC_UPPER, CharTools::getType('A'));
        self::assertEquals(CharTypes::IS_ABC_UPPER, CharTools::getType('Z'));

        self::assertEquals(CharTypes::IS_NUMBER, CharTools::getType('1'));
        self::assertEquals(CharTypes::IS_NUMBER, CharTools::getType('0'));
        self::assertEquals(CharTypes::IS_NUMBER, CharTools::getType('9'));

        // * * *

        self::assertEquals(CharTypes::IS_ABC_LOWER, CharTools::getType('a', true));
        self::assertEquals(CharTypes::IS_ABC_UPPER, CharTools::getType('A', true));
        self::assertEquals(0, CharTools::getType('1', true));

        // * * *

        self::assertEquals(0, CharTools::getType('a', false));
        self::assertEquals(0, CharTools::getType('A', false));
        self::assertEquals(CharTypes::IS_NUMBER, CharTools::getType('1', false));
    }

    /**
     * Test for {@see CharTools::isStartNameOfVar()}
     */
    private function runTestIsStartNameOfVar(): void
    {
        $this->runTestForNameOfVar('isStartNameOfVar');

        self::assertFalse(CharTools::isStartNameOfVar('0'));
        self::assertFalse(CharTools::isStartNameOfVar('9'));
    }

    /**
     * Test for {@see CharTools::isInsideNameOfVar()}
     */
    private function runTestIsInsideNameOfVar(): void
    {
        $this->runTestForNameOfVar('isInsideNameOfVar');

        self::assertTrue(CharTools::isInsideNameOfVar('0'));
        self::assertTrue(CharTools::isInsideNameOfVar('9'));
    }

    private function runTestForNameOfVar(string $functionName): void
    {
        self::assertFalse([CharTools::class, $functionName](''));
        self::assertFalse([CharTools::class, $functionName]('123'));

        self::assertFalse([CharTools::class, $functionName]('-'));

        self::assertTrue([CharTools::class, $functionName]('_'));
        self::assertTrue([CharTools::class, $functionName]('A'));
        self::assertTrue([CharTools::class, $functionName]('Z'));
        self::assertTrue([CharTools::class, $functionName]('a'));
        self::assertTrue([CharTools::class, $functionName]('z'));
    }

    /**
     * Test for {@see CharTools::isNumber()}
     */
    private function runTestIsNumber(): void
    {
        $this->runTestNumbersDefaultCases('isNumber');

        self::assertFalse(CharTools::isNumber('A'));
        self::assertFalse(CharTools::isNumber('a'));
    }

    /**
     * Test for {@see CharTools::isHex()}
     */
    private function runTestIsHex(): void
    {
        $this->runTestNumbersDefaultCases('isHex');

        self::assertTrue(CharTools::isHex('A'));
        self::assertTrue(CharTools::isHex('a'));
        self::assertTrue(CharTools::isHex('F'));
        self::assertTrue(CharTools::isHex('f'));

        self::assertFalse(CharTools::isHex('G'));
        self::assertFalse(CharTools::isHex('g'));
    }

    private function runTestNumbersDefaultCases(string $functionName): void
    {
        self::assertFalse([CharTools::class, $functionName](''), "Error {$functionName}()");
        self::assertFalse([CharTools::class, $functionName]('123'), "Error {$functionName}()");

        self::assertFalse([CharTools::class, $functionName]('-'), "Error {$functionName}()");
        self::assertFalse([CharTools::class, $functionName]('_'), "Error {$functionName}()");
        self::assertFalse([CharTools::class, $functionName](' '), "Error {$functionName}()");
        self::assertFalse([CharTools::class, $functionName]('!'), "Error {$functionName}()");

        self::assertFalse([CharTools::class, $functionName]('Z'), "Error {$functionName}()");
        self::assertFalse([CharTools::class, $functionName]('z'), "Error {$functionName}()");

        self::assertTrue([CharTools::class, $functionName]('0'), "Error {$functionName}()");
        self::assertTrue([CharTools::class, $functionName]('9'), "Error {$functionName}()");
    }

    /**
     * Test for {@see CharTools::isAbc()}
     */
    private function runTestIsAbc(): void
    {
        $this->runTestAbsFalseCases('isAbc');

        self::assertTrue(CharTools::isAbc('A'));
        self::assertTrue(CharTools::isAbc('Z'));
        self::assertTrue(CharTools::isAbc('a'));
        self::assertTrue(CharTools::isAbc('z'));
    }

    /**
     * Test for {@see CharTools::isAbcLower()}
     */
    private function runTestIsAbcLow(): void
    {
        $this->runTestAbsFalseCases('isAbcLower');

        self::assertFalse(CharTools::isAbcLower('A'));
        self::assertFalse(CharTools::isAbcLower('Z'));

        self::assertTrue(CharTools::isAbcLower('a'));
        self::assertTrue(CharTools::isAbcLower('z'));
    }

    /**
     * Test for {@see CharTools::isAbcUpper()}
     */
    private function runTestIsAbcUpper(): void
    {
        $this->runTestAbsFalseCases('isAbcUpper');

        self::assertFalse(CharTools::isAbcUpper('a'));
        self::assertFalse(CharTools::isAbcUpper('z'));

        self::assertTrue(CharTools::isAbcUpper('A'));
        self::assertTrue(CharTools::isAbcUpper('Z'));
    }

    private function runTestAbsFalseCases(string $functionName): void
    {
        self::assertFalse([CharTools::class, $functionName](''), "Error {$functionName}()");
        self::assertFalse([CharTools::class, $functionName]('ABC'), "Error {$functionName}()");

        self::assertFalse([CharTools::class, $functionName]('0'), "Error {$functionName}()");
        self::assertFalse([CharTools::class, $functionName]('9'), "Error {$functionName}()");

        self::assertFalse([CharTools::class, $functionName]('-'), "Error {$functionName}()");
        self::assertFalse([CharTools::class, $functionName]('_'), "Error {$functionName}()");
        self::assertFalse([CharTools::class, $functionName](' '), "Error {$functionName}()");
        self::assertFalse([CharTools::class, $functionName]('!'), "Error {$functionName}()");
    }
}
