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
 * Test for {@see StringCutTools}
 *
 * @run php tests/run.php tests/Strings/StringSearchToolsTest.php
 */
class StringCutToolsTest extends TestCase
{
    public function testRun(): void
    {
        $this->testFirstSubstrAfter();
        $this->testFirstSubstrBefore();
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
}
