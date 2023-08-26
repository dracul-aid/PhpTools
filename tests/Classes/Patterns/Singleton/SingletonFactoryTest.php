<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests\Classes\Patterns\Singleton;

use DraculAid\PhpTools\Classes\Patterns\Singleton\SingletonFactory;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see SingletonFactory}
 *
 * @run php tests/run.php tests/Classes/Patterns/Singleton/SingletonFactoryTest.php
 */
class SingletonFactoryTest extends TestCase
{
    /**
     * Test for {@see SingletonFactory::createObject()}
     */
    public function testCreateObject(): void
    {
        $object1 = SingletonFactory::createObject(\stdClass::class);
        $object2 = SingletonFactory::createObject(\stdClass::class);
        $objectNotSingleton = new \stdClass;

        self::assertTrue($object1 === $object2);
        self::assertFalse($object1 === $objectNotSingleton);

        self::assertCount(1, SingletonFactory::$singletonObjects);
    }

    /**
     * Test for {@see SingletonFactory::createObjectForIndex()}
     */
    public function testCreateObjectForIndex(): void
    {
        $object1 = SingletonFactory::createObjectForIndex('index1', \stdClass::class);
        $object2 = SingletonFactory::createObjectForIndex('index1', \stdClass::class);
        $object3 = SingletonFactory::createObjectForIndex('index2', \stdClass::class);
        $object4 = SingletonFactory::createObject(\stdClass::class);
        $objectNotSingleton = new \stdClass;

        self::assertTrue($object1 === $object2);
        self::assertFalse($object1 === $objectNotSingleton);
        self::assertFalse($object1 === $object3);
        self::assertFalse($object1 === $object4);

        self::assertCount(2, SingletonFactory::$uniqKeyObjects);
    }
}
