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
 * Test for {@coversDefaultClass ArrayAndStringTools}
 *
 * @run php tests/run.php tests/Strings/ArrayAndStringToolsTest.php
 */
class ArrayAndStringToolsTest extends TestCase
{
    /**
     * Test for {@covers ArrayAndStringTools::arrayToStringWithoutEmpty()}
     *
     * @return void
     * @throws \ReflectionException
     */
    public function testArrayToStringWithoutEmpty(): void
    {
        $testFunctionArrayToStringWithoutEmpty = ArrayAndStringTools::arrayToStringWithoutEmpty(...);

        self::assertEquals('', $testFunctionArrayToStringWithoutEmpty('-', []));
        self::assertEquals('', $testFunctionArrayToStringWithoutEmpty('-', ['', 0, false, null]));
        self::assertEquals('', $testFunctionArrayToStringWithoutEmpty('-', ['', 0, false, null], false));
        self::assertEquals('0', $testFunctionArrayToStringWithoutEmpty('-', ['', 0, false, null], true));

        self::assertEquals('1', $testFunctionArrayToStringWithoutEmpty('-', ['', 0, 1, false, null]));
        self::assertEquals('1', $testFunctionArrayToStringWithoutEmpty('-', ['', 0, 1, false, null], false));
        self::assertEquals('0-1', $testFunctionArrayToStringWithoutEmpty('-', ['', 0, 1, false, null], true));

        // проверка языковых конструкций
        self::assertEquals('-0--', $testFunctionArrayToStringWithoutEmpty('-', ['', 0, 1, false, null], 'empty'));
        self::assertEquals('-0-1-', $testFunctionArrayToStringWithoutEmpty('-', ['', 0, 1, false, null], 'isset'));

        // проверка функций (с 1 аргументом)
        self::assertEquals('0-1-2', $testFunctionArrayToStringWithoutEmpty('-', ['', 0, 1, 2, false, null], 'is_int'));

        // проверка функций (с 2 аргументами)
        self::assertEquals(
            '0-2',
            $testFunctionArrayToStringWithoutEmpty(
                '-',
                [0, 1, 2],
                function (int $value, int $index) {
                    return $index === 0 || $value === 2;
                }
            )
        );
    }

    /**
     * Test for {@covers ArrayAndStringTools::subStringToArray()}
     *
     * @return void
     */
    public function testSubStringToArray(): void
    {
        $testFunctionSubStringToArray = ArrayAndStringTools::subStringToArray(...);

        self::assertEquals(['123', '456', '7'], $testFunctionSubStringToArray('1234567', 3));
        self::assertEquals(['123', '456', '7'], $testFunctionSubStringToArray('1234567', 3, false));
        self::assertEquals(['567', '234', '1'], $testFunctionSubStringToArray('1234567', 3, true));

        self::assertEquals(['ЯZZ', 'яzz'], $testFunctionSubStringToArray('ЯZZяzz', 3, false));
        self::assertEquals(['ЯZZ', 'яzz'], $testFunctionSubStringToArray('ЯZZяzz', 3, false, true));
        self::assertEquals(['ЯZ', 'Zя', 'zz'], $testFunctionSubStringToArray('ЯZZяzz', 3, false, false));
    }
}
