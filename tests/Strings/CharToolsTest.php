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

use DraculAid\PhpTools\Strings\CharTools;
use DraculAid\PhpTools\Strings\Components\CharTypes;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@coversDefaultClass CharTools}
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
     * Test for {@covers CharTools::getType()}
     */
    private function runTestGetType(): void
    {
        $testFunctionGetType = CharTools::getType(...);

        self::assertFalse($testFunctionGetType('123'));

        self::assertEquals(0, $testFunctionGetType(''));
        self::assertEquals(0, $testFunctionGetType('+'));
        self::assertEquals(0, $testFunctionGetType('-'));
        self::assertEquals(0, $testFunctionGetType('!'));
        self::assertEquals(0, $testFunctionGetType('.'));

        self::assertEquals(CharTypes::IS_ABC_LOWER, $testFunctionGetType('a'));
        self::assertEquals(CharTypes::IS_ABC_LOWER, $testFunctionGetType('z'));

        self::assertEquals(CharTypes::IS_ABC_UPPER, $testFunctionGetType('A'));
        self::assertEquals(CharTypes::IS_ABC_UPPER, $testFunctionGetType('Z'));

        self::assertEquals(CharTypes::IS_NUMBER, $testFunctionGetType('1'));
        self::assertEquals(CharTypes::IS_NUMBER, $testFunctionGetType('0'));
        self::assertEquals(CharTypes::IS_NUMBER, $testFunctionGetType('9'));

        // * * *

        self::assertEquals(CharTypes::IS_ABC_LOWER, $testFunctionGetType('a', true));
        self::assertEquals(CharTypes::IS_ABC_UPPER, $testFunctionGetType('A', true));
        self::assertEquals(0, $testFunctionGetType('1', true));

        // * * *

        self::assertEquals(0, $testFunctionGetType('a', false));
        self::assertEquals(0, $testFunctionGetType('A', false));
        self::assertEquals(CharTypes::IS_NUMBER, $testFunctionGetType('1', false));
    }

    /**
     * Test for {@covers CharTools::isStartNameOfVar()}
     */
    private function runTestIsStartNameOfVar(): void
    {
        $testFunctionIsStartNameOfVar = CharTools::isStartNameOfVar(...);

        $this->runTestForNameOfVar('isStartNameOfVar');

        self::assertFalse($testFunctionIsStartNameOfVar('0'));
        self::assertFalse($testFunctionIsStartNameOfVar('9'));
    }

    /**
     * Test for {@covers CharTools::isInsideNameOfVar()}
     */
    private function runTestIsInsideNameOfVar(): void
    {
        $testFunctionIsInsideNameOfVar = CharTools::isInsideNameOfVar(...);

        $this->runTestForNameOfVar('isInsideNameOfVar');

        self::assertTrue($testFunctionIsInsideNameOfVar('0'));
        self::assertTrue($testFunctionIsInsideNameOfVar('9'));
    }

    private function runTestForNameOfVar(string $functionName): void
    {
        $testFunction = CharTools::$functionName(...);

        self::assertFalse($testFunction(''));
        self::assertFalse($testFunction('123'));

        self::assertFalse($testFunction('-'));

        self::assertTrue($testFunction('_'));
        self::assertTrue($testFunction('A'));
        self::assertTrue($testFunction('Z'));
        self::assertTrue($testFunction('a'));
        self::assertTrue($testFunction('z'));
    }

    /**
     * Test for {@covers CharTools::isNumber()}
     */
    private function runTestIsNumber(): void
    {
        $testFunctionIsNumber = CharTools::isNumber(...);

        $this->runTestNumbersDefaultCases('isNumber');

        self::assertFalse($testFunctionIsNumber('A'));
        self::assertFalse($testFunctionIsNumber('a'));
    }

    /**
     * Test for {@covers CharTools::isHex()}
     */
    private function runTestIsHex(): void
    {
        $testFunctionIsHex = CharTools::isHex(...);

        $this->runTestNumbersDefaultCases('isHex');

        self::assertTrue($testFunctionIsHex('A'));
        self::assertTrue($testFunctionIsHex('a'));
        self::assertTrue($testFunctionIsHex('F'));
        self::assertTrue($testFunctionIsHex('f'));

        self::assertFalse($testFunctionIsHex('G'));
        self::assertFalse($testFunctionIsHex('g'));
    }

    private function runTestNumbersDefaultCases(string $functionName): void
    {
        $testFunction = CharTools::$functionName(...);

        self::assertFalse($testFunction(''), "Error {$functionName}()");
        self::assertFalse($testFunction('123'), "Error {$functionName}()");

        self::assertFalse($testFunction('-'), "Error {$functionName}()");
        self::assertFalse($testFunction('_'), "Error {$functionName}()");
        self::assertFalse($testFunction(' '), "Error {$functionName}()");
        self::assertFalse($testFunction('!'), "Error {$functionName}()");

        self::assertFalse($testFunction('Z'), "Error {$functionName}()");
        self::assertFalse($testFunction('z'), "Error {$functionName}()");

        self::assertTrue($testFunction('0'), "Error {$functionName}()");
        self::assertTrue($testFunction('9'), "Error {$functionName}()");
    }

    /**
     * Test for {@covers CharTools::isAbc()}
     */
    private function runTestIsAbc(): void
    {
        $testFunctionIsAbc = CharTools::isAbc(...);

        $this->runTestAbsFalseCases('isAbc');

        self::assertTrue($testFunctionIsAbc('A'));
        self::assertTrue($testFunctionIsAbc('Z'));
        self::assertTrue($testFunctionIsAbc('a'));
        self::assertTrue($testFunctionIsAbc('z'));
    }

    /**
     * Test for {@covers CharTools::isAbcLower()}
     */
    private function runTestIsAbcLow(): void
    {
        $testFunctionIsAbcLower = CharTools::isAbcLower(...);

        $this->runTestAbsFalseCases('isAbcLower');

        self::assertFalse($testFunctionIsAbcLower('A'));
        self::assertFalse($testFunctionIsAbcLower('Z'));

        self::assertTrue($testFunctionIsAbcLower('a'));
        self::assertTrue($testFunctionIsAbcLower('z'));
    }

    /**
     * Test for {@covers CharTools::isAbcUpper()}
     */
    private function runTestIsAbcUpper(): void
    {
        $testFunctionIsAbcUpper = CharTools::isAbcUpper(...);

        $this->runTestAbsFalseCases('isAbcUpper');

        self::assertFalse($testFunctionIsAbcUpper('a'));
        self::assertFalse($testFunctionIsAbcUpper('z'));

        self::assertTrue($testFunctionIsAbcUpper('A'));
        self::assertTrue($testFunctionIsAbcUpper('Z'));
    }

    private function runTestAbsFalseCases(string $functionName): void
    {
        $testFunction = CharTools::$functionName(...);

        self::assertFalse($testFunction(''), "Error {$functionName}()");
        self::assertFalse($testFunction('ABC'), "Error {$functionName}()");

        self::assertFalse($testFunction('0'), "Error {$functionName}()");
        self::assertFalse($testFunction('9'), "Error {$functionName}()");

        self::assertFalse($testFunction('-'), "Error {$functionName}()");
        self::assertFalse($testFunction('_'), "Error {$functionName}()");
        self::assertFalse($testFunction(' '), "Error {$functionName}()");
        self::assertFalse($testFunction('!'), "Error {$functionName}()");
    }
}
