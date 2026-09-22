<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests\Classes;

use DraculAid\PhpTools\Classes\ClassConstructorTools;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@covers ClassConstructorTools}
 *
 * @run php tests/run.php tests/Classes/ClassConstructorToolsTest.php
 */
class ClassConstructorToolsTest extends TestCase
{
    /**
     * Test for {@covers ClassConstructorTools::getPublicProperties()}
     *
     * @return void
     * @throws \ReflectionException Ошибка рефлекии указывает, что не работает тестируемая функция
     */
    public function testGetPublicProperties(): void
    {
        $testFunction = ClassConstructorTools::getPublicProperties(...);

        $testObject = new class {};
        self::assertEquals([], $testFunction($testObject::class));

        $testObject = new class {
            public function __construct() {}
        };
        self::assertEquals([], $testFunction($testObject::class));

        $testObject = new class(1) {
            public function __construct(int $a) {}
        };
        self::assertEquals([], $testFunction($testObject::class));

        $testObject = new class(10, 20) {
            public function __construct(int $a, public int $b) {}
        };
        self::assertEquals([1 => 'b'], $testFunction($testObject::class));

        $testObject = new class(10, 20, 30, 40) {
            public function __construct(public int $a, protected int $b, public int $c, int $d) {}
        };
        self::assertEquals([0 => 'a', 2 => 'c'], $testFunction($testObject::class));
    }

    /**
     * Test for {@covers ClassConstructorTools::isHasArguments()}
     *
     * @return void
     * @throws \ReflectionException Ошибка рефлекии указывает, что не работает тестируемая функция
     */
    public function testIsHasArguments()
    {
        $testFunction = ClassConstructorTools::isHasArguments(...);

        $testObject = new class {};
        self::assertFalse($testFunction($testObject::class));

        $testObject = new class {
            public function __construct() {}
        };
        self::assertFalse($testFunction($testObject::class));

        $testObject = new class(1) {
            public function __construct(int $a) {}
        };
        self::assertTrue($testFunction($testObject::class));
    }

    /**
     * Test for {@covers ClassConstructorTools::isHasOnlyPublicProperties()}
     *
     * @return void
     * @throws \ReflectionException Ошибка рефлекии указывает, что не работает тестируемая функция
     */
    public function testIsHasOnlyPublicProperties(): void
    {
        $testFunction = ClassConstructorTools::isHasOnlyPublicProperties(...);

        $testObject = new class {};
        self::assertTrue($testFunction($testObject::class));

        $testObject = new class {
            public function __construct() {}
        };
        self::assertTrue($testFunction($testObject::class));

        $testObject = new class(10, 20) {
            public function __construct(public int $a, public int $b) {}
        };
        self::assertTrue($testFunction($testObject::class));

        $testObject = new class(10) {
            public function __construct(int $a) {}
        };
        self::assertFalse($testFunction($testObject::class));

        $testObject = new class(10, 20) {
            public function __construct(int $a, public int $b) {}
        };
        self::assertFalse($testFunction($testObject::class));

        $testObject = new class(10, 20) {
            public function __construct(protected int $a, public int $b) {}
        };
        self::assertFalse($testFunction($testObject::class));
    }
}
