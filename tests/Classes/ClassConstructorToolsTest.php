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
     */
    public function testGetPublicProperties(): void
    {
        $testObject = new class {};
        self::assertEquals([], ClassConstructorTools::getPublicProperties($testObject::class));

        $testObject = new class {
            public function __construct() {}
        };
        self::assertEquals([], ClassConstructorTools::getPublicProperties($testObject::class));

        $testObject = new class(1) {
            public function __construct(int $a) {}
        };
        self::assertEquals([], ClassConstructorTools::getPublicProperties($testObject::class));

        $testObject = new class(10, 20) {
            public function __construct(int $a, public int $b) {}
        };
        self::assertEquals([1 => 'b'], ClassConstructorTools::getPublicProperties($testObject::class));

        $testObject = new class(10, 20, 30, 40) {
            public function __construct(public int $a, protected int $b, public int $c, int $d) {}
        };
        self::assertEquals([0 => 'a', 2 => 'c'], ClassConstructorTools::getPublicProperties($testObject::class));
    }

    /**
     * Test for {@covers ClassConstructorTools::isHasArguments()}
     *
     * @return void
     */
    public function testIsHasArguments()
    {
        $testObject = new class {};
        self::assertFalse(ClassConstructorTools::isHasArguments($testObject::class));

        $testObject = new class {
            public function __construct() {}
        };
        self::assertFalse(ClassConstructorTools::isHasArguments($testObject::class));

        $testObject = new class(1) {
            public function __construct(int $a) {}
        };
        self::assertTrue(ClassConstructorTools::isHasArguments($testObject::class));
    }

    /**
     * Test for {@covers ClassConstructorTools::isHasOnlyPublicProperties()}
     *
     * @return void
     */
    public function testIsHasOnlyPublicProperties(): void
    {
        $testObject = new class {};
        self::assertTrue(ClassConstructorTools::isHasOnlyPublicProperties($testObject::class));

        $testObject = new class {
            public function __construct() {}
        };
        self::assertTrue(ClassConstructorTools::isHasOnlyPublicProperties($testObject::class));

        $testObject = new class(10, 20) {
            public function __construct(public int $a, public int $b) {}
        };
        self::assertTrue(ClassConstructorTools::isHasOnlyPublicProperties($testObject::class));

        $testObject = new class(10) {
            public function __construct(int $a) {}
        };
        self::assertFalse(ClassConstructorTools::isHasOnlyPublicProperties($testObject::class));

        $testObject = new class(10, 20) {
            public function __construct(int $a, public int $b) {}
        };
        self::assertFalse(ClassConstructorTools::isHasOnlyPublicProperties($testObject::class));

        $testObject = new class(10, 20) {
            public function __construct(protected int $a, public int $b) {}
        };
        self::assertFalse(ClassConstructorTools::isHasOnlyPublicProperties($testObject::class));
    }
}
