<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests\Code;

use DraculAid\PhpTools\Code\CallFunctionHelper;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see CallFunctionHelper}
 *
 * @run php tests/run.php tests/Code/CallFunctionHelperTest.php
 */
class CallFunctionHelperTest extends TestCase
{
    /**
     * Test for {@see CallFunctionHelper::STRUCTURES}
     * Test for {@see CallFunctionHelper::isStructures()}
     * Test for {@see CallFunctionHelper::isCallable()}
     *
     * @return void
     */
    public function testIsStructures(): void
    {
        foreach (CallFunctionHelper::STRUCTURES as $name)
        {
            self::assertTrue(CallFunctionHelper::isStructures($name), "isStructures: Error for {$name}");
            self::assertTrue(CallFunctionHelper::isCallable($name), "isCallable: Error for {$name}");
        }

        self::assertFalse(CallFunctionHelper::isStructures('is_int'));
        self::assertTrue(CallFunctionHelper::isCallable('is_int'));
        self::assertFalse(CallFunctionHelper::isStructures(CallFunctionHelper::class . '::isStructures'));
        self::assertTrue(CallFunctionHelper::isCallable(CallFunctionHelper::class . '::isStructures'));

        self::assertTrue(CallFunctionHelper::isCallable([CallFunctionHelper::class, 'isStructures']));
        self::assertTrue(CallFunctionHelper::isCallable([$this, 'testIsStructures']));
    }

    /**
     * Test for {@see CallFunctionHelper::exe()}
     * Test for {@see CallFunctionHelper::exeCallable()}
     * Test for {@see CallFunctionHelper::isClassCallable()} in {@see CallFunctionHelper::exeCallable()}
     * Test for {@see CallFunctionHelper::getReflectionForCallable()} in {@see CallFunctionHelper::exeCallable()}
     *
     * @return void
     */
    public function testExe(): void
    {
        // isset()
        $t = null;
        self::assertFalse(CallFunctionHelper::exe('isset', $t));
        $t = false;
        self::assertTrue(CallFunctionHelper::exe('isset', $t));
        $t = false; $a = null;
        self::assertTrue(CallFunctionHelper::exe('isset', $t, $a));

        // empty()
        $t = null; $a = null;
        self::assertTrue(CallFunctionHelper::exe('empty', $t, $a));
        $t = false;
        self::assertTrue(CallFunctionHelper::exe('empty', $t, $a));
        $t = '123';
        self::assertFalse(CallFunctionHelper::exe('empty', $t, $a));

        // * * * Вызов функций

        $t = 'XXX';
        self::assertFalse(CallFunctionHelper::exe('is_int', $t));
        $t = 0;
        self::assertTrue(CallFunctionHelper::exe('is_int', $t));
        $t = 1; $a = 'XYZ';
        self::assertTrue(CallFunctionHelper::exe('is_int', $t, $a));

        // вызов методов
        $testObject = $this->getTestObject();
        self::assertEquals(3, CallFunctionHelper::exe([$testObject, 'f2'], 5, 2));
    }

    private function getTestObject(): object
    {
        return new class() {
            public static function f1(int $a, int $b): int
            {
                return $a + $b;
            }
            public function f2(int $a, int $b): int
            {
                return $a - $b;
            }
        };
    }
}