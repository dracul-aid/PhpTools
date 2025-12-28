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

use DraculAid\PhpTools\Classes\ObjectTools;
use DraculAid\PhpTools\ExceptionTools\ExceptionTools;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see ObjectTools}
 *
 * @run php tests/run.php tests/Classes/ObjectToolsTest.php
 */
class ObjectToolsTest extends TestCase
{
    /**
     * Test for {@see ObjectTools::getStringNewInstance()}
     */
    public function testGetStringNewInstance(): void
    {
        // * * * Пустые конструкторы

        self::assertEquals(
            'new \stdClass()',
            ObjectTools::getStringNewInstance(\stdClass::class, [])
        );

        self::assertEquals(
            'new \stdClass()',
            ObjectTools::getStringNewInstance(\stdClass::class, new \ArrayObject([]))
        );

        // * * * Конструкторы, с параметрами переданными ввиде списка

        self::assertEquals(
            "new \stdClass('first', 222, true, false, NULL)",
            ObjectTools::getStringNewInstance(\stdClass::class, ["first", 222, true, false, null])
        );

        self::assertEquals(
            "new \stdClass('first', 222, true, false, NULL)",
            ObjectTools::getStringNewInstance(\stdClass::class, ['1n' => "first", 222, true, false, '2n' => null], true)
        );

        self::assertEquals(
            "new \stdClass('first', 222, true, false, NULL)",
            ObjectTools::getStringNewInstance(\stdClass::class, ['x1' => "first", 'x2'=> 222, 'x3' => true, 'x4' => false, 'x5' => null], true)
        );

        self::assertEquals(
            'new \stdClass(1, 2, 3)',
            ObjectTools::getStringNewInstance(\stdClass::class, new \ArrayObject([1, 2, 3]))
        );

        // * * * Конструкторы, с параметрами, переданными ввиде именованных аргументов

        self::assertEquals(
            "new \stdClass(x1: 'first', x2: 222, x3: true, x4: false, x5: NULL)",
            ObjectTools::getStringNewInstance(\stdClass::class, ['x1' => "first", 'x2'=> 222, 'x3' => true, 'x4' => false, 'x5' => null])
        );

        self::assertEquals(
            "new \stdClass(x1: 'first', x2: 222, x3: true, x4: false, x5: NULL)",
            ObjectTools::getStringNewInstance(\stdClass::class, ['x1' => "first", 'x2'=> 222, 'x3' => true, 'x4' => false, 'x5' => null], false)
        );

        // * * * Смешанные параметры - будет выброшена ошибка, перехватим ее

        self::assertTrue(
            ExceptionTools::wasCalledWithException([ObjectTools::class, 'getStringNewInstance'], [\stdClass::class, ['1n' => "first", 222, true, false, '2n' => null]], \InvalidArgumentException::class)
        );

        self::assertTrue(
            ExceptionTools::wasCalledWithException([ObjectTools::class, 'getStringNewInstance'], [\stdClass::class, ['1n' => "first", 222, true, false, '2n' => null], false], \InvalidArgumentException::class)
        );

        // * * * TODO реализовать следующие кейсы
        // 1. Передача аргументов "массивом" с ключами-объектами, должно падать (если объект не имеет метод __toString())
    }

    /**
     * Test for {@see ObjectTools::propertiesFor()}
     * Test for {@see ObjectTools::toArray()}
     */
    public function testToArrayAndPropertiesFor(): void
    {
        self::assertEquals(['public_1' => 'public-1', 'public_2' => 'public-2'], ObjectTools::toArray($this->createObjectForTestToArray()));
        self::assertEquals(['public_1' => 'public-1', 'public_2' => 'public-2'], ObjectTools::toArray($this->createObjectForTestToArray(), false));
        self::assertEquals(['public-1', 'public-2'], ObjectTools::toArray($this->createObjectForTestToArray(), true));

        self::assertEquals(['public_1' => 'public-1', 'public_2' => 'public-2'], ObjectTools::toArray($this->createObjectForTestToArray(), false, \ReflectionProperty::IS_PUBLIC));
        self::assertEquals(['protected_1' => 'protected-1', 'protected_2' => 'protected-2'], ObjectTools::toArray($this->createObjectForTestToArray(), false, \ReflectionProperty::IS_PROTECTED));
        self::assertEquals(['private_1' => 'private-1', 'private_2' => 'private-2'], ObjectTools::toArray($this->createObjectForTestToArray(), false, \ReflectionProperty::IS_PRIVATE));

        self::assertEquals(
            ['protected_1' => 'protected-1', 'protected_2' => 'protected-2', 'private_1' => 'private-1', 'private_2' => 'private-2'],
            ObjectTools::toArray($this->createObjectForTestToArray(), false, \ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PRIVATE)
        );

        self::assertEquals(
            ['public_1' => 'public-1', 'public_2' => 'public-2', 'protected_1' => 'protected-1', 'protected_2' => 'protected-2', 'private_1' => 'private-1', 'private_2' => 'private-2'],
            ObjectTools::toArray($this->createObjectForTestToArray(), false, null)
        );
    }

    private function createObjectForTestToArray(): object
    {
        return new class() implements \IteratorAggregate {
            public string $public_1 = 'public-1';
            public string $public_2 = 'public-2';
            protected string $protected_1 = 'protected-1';
            protected string $protected_2 = 'protected-2';
            private string $private_1 = 'private-1';
            private string $private_2 = 'private-2';

            public function getIterator(): \Generator
            {
                throw new \RuntimeException("test is failed");
            }
        };
    }
}
