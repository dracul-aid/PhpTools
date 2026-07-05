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

use DraculAid\PhpTools\Strings\StringCutTools;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@coversDefaultClass StringCutTools}
 *
 * @run php tests/run.php tests/Strings/StringCutToolsTest.php
 */
class StringCutToolsTest extends TestCase
{
    public function testRun(): void
    {
        $this->testFirstSubstrAfter();
        $this->testFirstSubstrBefore();
        $this->testTrimInString();
        $this->testQuoteTrim();
        $this->testClearMultiSpaces();
        $this->testMask();
        $this->testResize();
    }

    /**
     * Test for {@see StringCutTools::firstSubstrBefore()}
     *
     * @return void
     */
    private function testFirstSubstrBefore(): void
    {
        self::assertEquals(
            'ZZZ Мама мыла раму WWW',
            StringCutTools::firstSubstrBefore('ZZZ Мама мыла раму WWW', '123')
        );
        self::assertEquals(
            'ZZZ Мама мыла раму WWW',
            StringCutTools::firstSubstrBefore('ZZZ Мама мыла раму WWW', ['123', 'жмых'])
        );
        self::assertEquals(
            'ZZZ Мама мыла раму WWW',
            StringCutTools::firstSubstrBefore('ZZZ Мама мыла раму WWW', 'Мама', false, 10)
        );

        // * * *

        self::assertEquals(
            'ZZZ Мама ',
            StringCutTools::firstSubstrBefore('ZZZ Мама мыла раму WWW', ['123', 'мыла'])
        );
        self::assertEquals(
            'ZZZ Мама ',
            StringCutTools::firstSubstrBefore('ZZZ Мама мыла раму WWW', ['123', 'мыла'], false)
        );
        self::assertEquals(
            'ZZZ Мама мыла',
            StringCutTools::firstSubstrBefore('ZZZ Мама мыла раму WWW', ['123', 'мыла'], true)
        );

        self::assertEquals(
            'ZZZ Мама ',
            StringCutTools::firstSubstrBefore('ZZZ Мама мыла мыла раму WWW', 'мыла')
        );
        self::assertEquals(
            'ZZZ Мама мыла и снова ',
            StringCutTools::firstSubstrBefore('ZZZ Мама мыла и снова мыла раму WWW', 'мыла', false, 12)
        );
    }

    /**
     * Test for {@see StringCutTools::firstSubstrAfter()}
     *
     * @return void
     */
    private function testFirstSubstrAfter(): void
    {
        self::assertEquals(
            'ZZZ Мама мыла раму WWW',
            StringCutTools::firstSubstrAfter('ZZZ Мама мыла раму WWW', '123')
        );
        self::assertEquals(
            'ZZZ Мама мыла раму WWW',
            StringCutTools::firstSubstrAfter('ZZZ Мама мыла раму WWW', ['123', 'жмых'])
        );
        self::assertEquals(
            'ZZZ Мама мыла раму WWW',
            StringCutTools::firstSubstrAfter('ZZZ Мама мыла раму WWW', 'Мама', false, 10)
        );

        // * * *

        self::assertEquals(
            ' раму WWW',
            StringCutTools::firstSubstrAfter('ZZZ Мама мыла раму WWW', ['123', 'мыла'])
        );
        self::assertEquals(
            ' раму WWW',
            StringCutTools::firstSubstrAfter('ZZZ Мама мыла раму WWW', ['123', 'мыла'], false)
        );
        self::assertEquals(
            'мыла раму WWW',
            StringCutTools::firstSubstrAfter('ZZZ Мама мыла раму WWW', ['123', 'мыла'], true)
        );

        self::assertEquals(
            ' мыла раму WWW',
            StringCutTools::firstSubstrAfter('ZZZ Мама мыла мыла раму WWW', 'мыла')
        );
        self::assertEquals(
            ' раму WWW',
            StringCutTools::firstSubstrAfter('ZZZ Мама мыла и снова мыла раму WWW', 'мыла', false, 12)
        );
    }

    /**
     * Test for {@see StringCutTools::trimInString()}
     *
     * @return void
     */
    private function testTrimInString(): void
    {
        self::assertEquals(' домик на дереве ', StringCutTools::trimInString('   домик на дереве   '));
        self::assertEquals('домик на дереве', StringCutTools::trimInString('домик   на      дереве'));
        self::assertEquals('домик на дереве', StringCutTools::trimInString('домик  на       дереве'));
    }

    /**
     * Test for {@see StringCutTools::quoteTrim()}
     *
     * @return void
     */
    private function testQuoteTrim(): void
    {
        self::assertEquals('домик на дереве', StringCutTools::quoteTrim('"домик на дереве"'));
        self::assertEquals('домик на дереве', StringCutTools::quoteTrim('\'домик на дереве\''));
        self::assertEquals('домик на дереве', StringCutTools::quoteTrim('`домик на дереве`'));
        self::assertEquals('домик на дереве', StringCutTools::quoteTrim('«домик на дереве»'));
        self::assertEquals('домик на дереве', StringCutTools::quoteTrim('”домик на дереве”'));
        self::assertEquals('домик на дереве', StringCutTools::quoteTrim('„домик на дереве„'));
        self::assertEquals('домик на дереве', StringCutTools::quoteTrim('‚домик на дереве‚'));
        self::assertEquals('домик на дереве', StringCutTools::quoteTrim('’домик на дереве’'));

        self::assertEquals('домик на дереве', StringCutTools::quoteTrim('"\'`домик на дереве"\'`'));
    }

    /**
     * Test for {@see StringCutTools::clearMultiSpaces()}
     *
     * @return void
     */
    private function testClearMultiSpaces(): void
    {
        self::assertEquals('', StringCutTools::clearMultiSpaces(''));
        self::assertEquals(' ', StringCutTools::clearMultiSpaces(' '));
        self::assertEquals(' ', StringCutTools::clearMultiSpaces('   '));
        self::assertEquals(' ', StringCutTools::clearMultiSpaces("\n\t      "));

        self::assertEquals('', StringCutTools::clearMultiSpaces('', '!'));
        self::assertEquals('!', StringCutTools::clearMultiSpaces(' ', '!'));
        self::assertEquals('!', StringCutTools::clearMultiSpaces("\n\t      ", '!'));
        self::assertEquals('123', StringCutTools::clearMultiSpaces("\n\t      ", '123'));

        self::assertEquals('abc', StringCutTools::clearMultiSpaces('abc'));
        self::assertEquals(' abc ', StringCutTools::clearMultiSpaces(' abc '));
        self::assertEquals(' abc ', StringCutTools::clearMultiSpaces("\n\tabc      "));
    }

    /**
     * Test for {@see StringCutTools::mask()}
     *
     * @return void
     */
    private function testMask(): void
    {
        $testFunction = StringCutTools::mask(...);

        self::assertEquals('', $testFunction('', 0, 0));
        self::assertEquals('', $testFunction('password', 0, 0));
        self::assertEquals('password', $testFunction('password', 2, 8, ''));
        self::assertEquals('pass', $testFunction('password', 2, 4, ''));

        self::assertEquals('********', $testFunction('password', 0, 8));
        self::assertEquals('я******ж', $testFunction('яж', 1, 8));
        self::assertEquals('яг****да', $testFunction('ягода', 2, 8));
        self::assertEquals('pa****rd', $testFunction('password', 2, 8));
        self::assertEquals('pa##rd', $testFunction('password-password', 2, 6, '#'));
        self::assertEquals('пар**оль', $testFunction('пароль', 3, 8));
        self::assertEquals('паабабль', $testFunction('пароль', 2, 8, 'аб'));

        self::assertEquals('pass', $testFunction('password', 2, 4));
        self::assertEquals('password', $testFunction('password', 4, 8));
    }

    /**
     * Test for {@see StringCutTools::resize()}
     *
     * @return void
     */
    private function testResize(): void
    {
        $testFunction = StringCutTools::resize(...);

        self::assertEquals('', $testFunction('', 0, ''));
        self::assertEquals('', $testFunction('', 0, '*'));
        self::assertEquals('', $testFunction('', 123, ''));

        // Когда функция работает, как аналог репита
        StringToolsTest::repeatTestRun(fn ($a, $b) => $testFunction('', $b, $a), 'StringCutTools::resize');

        // уменьшение строки
        self::assertEquals('123', $testFunction('123456', 3, ''));
        self::assertEquals('123', $testFunction('123456', 3, '**'));
        self::assertEquals('яяя', $testFunction('яяя|жжж', 3, '**'));
        self::assertEquals('яяя|', $testFunction('яяя|жжж', 4, '**'));
        self::assertEquals('456', $testFunction('123456', -3, ''));
        self::assertEquals('456', $testFunction('123456', -3, '**'));
        self::assertEquals('жжж', $testFunction('яяя|жжж', -3, '**'));
        self::assertEquals('|жжж', $testFunction('яяя|жжж', -4, '**'));

        // увеличение размера строки
        self::assertEquals('12*', $testFunction('12', 3, '*'));
        self::assertEquals('1**', $testFunction('1', 3, '*'));
        self::assertEquals('123', $testFunction('123', 3, '*'));
        self::assertEquals('12я', $testFunction('12', 3, 'я'));
        self::assertEquals('12яя', $testFunction('12', 4, 'я'));
        self::assertEquals('*12', $testFunction('12', -3, '*'));
        self::assertEquals('**1', $testFunction('1', -3, '*'));
        self::assertEquals('123', $testFunction('123', -3, '*'));
        self::assertEquals('я12', $testFunction('12', -3, 'я'));
        self::assertEquals('яя12', $testFunction('12', -4, 'я'));
    }
}
