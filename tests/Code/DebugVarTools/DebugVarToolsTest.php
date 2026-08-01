<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests\Code\DebugVarTools;

use DraculAid\PhpTools\Code\DebugVarTools;

/**
 * Test for {@coversDefaultClass DebugVarTools}
 *
 * @run php tests/run.php tests/Code/DebugVarTools/DebugVarToolsTest.php
 */
class DebugVarToolsTest extends AbstractDebugVarToolsTestClass
{
    /**
     * @covers DebugVarTools::minDebugValue()
     *
     * @return void
     */
    public function testMinDebugValue(): void
    {
        foreach ($this->minDebugValueCases() as [$result, $argument]) {
            self::assertEquals($result, DebugVarTools::minDebugValue($argument));
        }
    }

    /**
     * @covers DebugVarTools::minDebugValueCases()
     *
     * @return void
     */
    public function testMinDebugValueCases(): void
    {
        $testFunction = DebugVarTools::minDebugValueCases(...);

        self::assertEquals('', $testFunction([]));
        self::assertEquals('NULL', $testFunction([null]));
        self::assertEquals('NULL, FALSE, TRUE', $testFunction([null, false, true]));
        self::assertEquals('NULL-FALSE-TRUE', $testFunction([null, false, true], '-'));
        self::assertEquals('=NULL, =FALSE, =TRUE', $testFunction([null, false, true], ', ', '='));
    }
}
