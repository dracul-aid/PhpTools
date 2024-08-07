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

use DraculAid\PhpTools\Strings\Components\TranslitConverter\CharRuToEnIcao;
use DraculAid\PhpTools\Strings\TranslitConverter;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see TranslitConverter}
 *
 * @run php tests/run.php tests/Strings/TranslitConverterTest.php
 */
class TranslitConverterTest extends TestCase
{
    /**
     * Test for {@see TranslitConverter::toUrl()}
     *
     * @return void
     */
    public function testToUrl(): void
    {
        self::assertEquals('dom-i-more', TranslitConverter::toUrl('дом и море'));
        self::assertEquals('dom-home-dom', TranslitConverter::toUrl('дом, home, дом'));
        self::assertEquals('dom_dom', TranslitConverter::toUrl('дом_дом'));
        self::assertEquals('dom_rom-123', TranslitConverter::toUrl('дом___ром---123'));
    }

    /**
     * Test for {@see TranslitConverter::CyrillicToIcao()}
     *
     * @return void
     */
    public function testCyrillicToIcao(): void
    {
        foreach (CharRuToEnIcao::LIST as $ruChar => $enChar)
        {
            $testResult = TranslitConverter::CyrillicToIcao($ruChar);
            self::assertEquals($enChar, $testResult, "CyrillicToIcao('{$ruChar}') return '{$testResult}', but '{$ruChar}' => '{$enChar}'");
        }
    }
}
