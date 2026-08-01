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

use DraculAid\PhpTools\Code\DebugVarHtmlTools;

/**
 * Test for {@coversDefaultClass DebugVarHtmlTools}
 *
 * @run php tests/run.php tests/Code/DebugVarTools/DebugVarHtmlToolsTest.php
 */
class DebugVarHtmlToolsTest extends AbstractDebugVarToolsTestClass
{
    /**
     * @covers DebugVarHtmlTools::minDebugValueCases()
     *
     * @return void
     */
    public function testMinDebugValue(): void
    {
        foreach ($this->minDebugValueCases() as [$result, $argument]) {
            self::assertEquals($this->getPreCode($result), DebugVarHtmlTools::minDebugValue($argument));
        }
    }

    /**
     * @covers DebugVarTools::minDebugValueCases()
     *
     * @return void
     */
    public function testMinDebugValueCases(): void
    {
        $cases = [
            ['', []],
            [$this->getPreCode('NULL'), [null]],
            [$this->getPreCode('NULL') . $this->getPreCode('TRUE'), [null, true]],
        ];

        foreach ($cases as $number => [$result, $argument]) {
            self::assertEquals($result, DebugVarHtmlTools::minDebugValueCases($argument), "Error #{$number}");
        }
    }

    private function getPreCode(string $value): string
    {
        return "<pre><code>{$value}</code></pre>";
    }
}
