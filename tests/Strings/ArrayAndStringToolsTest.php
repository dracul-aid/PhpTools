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

use DraculAid\PhpTools\Strings\ArrayAndStringTools;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see ArrayAndStringTools}
 *
 * @run php tests/run.php tests/Strings/ArrayAndStringToolsTest.php
 */
class ArrayAndStringToolsTest extends TestCase
{
    /**
     * Test for {@see ArrayAndStringTools::arrayToStringWithoutEmpty()}
     *
     * @return void
     */
    public function testArrayToStringWithoutEmpty(): void
    {
        self::assertEquals('', ArrayAndStringTools::arrayToStringWithoutEmpty('-', []));
        self::assertEquals('', ArrayAndStringTools::arrayToStringWithoutEmpty('-', ['', 0, false, null]));
        self::assertEquals('', ArrayAndStringTools::arrayToStringWithoutEmpty('-', ['', 0, false, null], false));
        self::assertEquals('0', ArrayAndStringTools::arrayToStringWithoutEmpty('-', ['', 0, false, null], true));

        self::assertEquals('1', ArrayAndStringTools::arrayToStringWithoutEmpty('-', ['', 0, 1, false, null]));
        self::assertEquals('1', ArrayAndStringTools::arrayToStringWithoutEmpty('-', ['', 0, 1, false, null], false));
        self::assertEquals('0-1', ArrayAndStringTools::arrayToStringWithoutEmpty('-', ['', 0, 1, false, null], true));

        // проверка языковых конструкций
        self::assertEquals('-0--', ArrayAndStringTools::arrayToStringWithoutEmpty('-', ['', 0, 1, false, null], 'empty'));
        self::assertEquals('-0-1-', ArrayAndStringTools::arrayToStringWithoutEmpty('-', ['', 0, 1, false, null], 'isset'));

        // проверка функций (с 1 аргументом)
        self::assertEquals('0-1-2', ArrayAndStringTools::arrayToStringWithoutEmpty('-', ['', 0, 1, 2, false, null], 'is_int'));

        // проверка функций (с 2 аргументами)
        self::assertEquals(
            '0-2',
            ArrayAndStringTools::arrayToStringWithoutEmpty(
                '-',
                [0, 1, 2],
                function ($value, $index) {
                    return $index === 0 || $value === 2;
                }
            )
        );
    }

    /**
     * Test for {@see ArrayAndStringTools::subStringToArray()}
     *
     * @return void
     */
    public function testSubStringToArray(): void
    {
        self::assertEquals(['123', '456', '7'], ArrayAndStringTools::subStringToArray('1234567', 3));
        self::assertEquals(['123', '456', '7'], ArrayAndStringTools::subStringToArray('1234567', 3, false));
        self::assertEquals(['567', '234', '1'], ArrayAndStringTools::subStringToArray('1234567', 3, true));

        self::assertEquals(['ЯZZ', 'яzz'], ArrayAndStringTools::subStringToArray('ЯZZяzz', 3, false));
        self::assertEquals(['ЯZZ', 'яzz'], ArrayAndStringTools::subStringToArray('ЯZZяzz', 3, false, true));
        self::assertEquals(['ЯZ', 'Zя', 'zz'], ArrayAndStringTools::subStringToArray('ЯZZяzz', 3, false, false));
    }
}
