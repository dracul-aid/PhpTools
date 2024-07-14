<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests\Strings\Components\TranslitConverter;

use DraculAid\PhpTools\Strings\Components\TranslitConverter\CharRuToEnIcao;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@coversDefaultClass CharRuToEnIcao}
 *
 * @run php tests/run.php tests/Strings/Components/TranslitConverter/CharRuToEnIcaoTest.php
 */
class CharRuToEnIcaoTest extends TestCase
{
    /**
     * Test for {@covers CharRuToEnIcao::LIST}
     *
     * @return void
     */
    public function testRun(): void
    {
        foreach (CharRuToEnIcao::LIST as $ruChar => $enChar)
        {
            // проверка ключей - кирилические символы
            self::assertEquals(1, mb_strlen($ruChar), "Char size '{$ruChar}': " . mb_strlen($ruChar));
            self::assertTrue(!preg_match('/[^а-яА-ЯёЁ]/u', $ruChar), "Char '{$ruChar}' is not Cyrillic string");

            // проверка значений - латинские символы
            self::assertTrue(!preg_match('/[^A-Za-z]/u', $enChar), "Char '{$enChar}' is not ABC char");
        }
    }
}
