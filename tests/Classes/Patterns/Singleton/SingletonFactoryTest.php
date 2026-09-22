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
 * Test for {@coversDefaultClass SingletonFactory}
 *
 * @run php tests/run.php tests/Classes/Patterns/Singleton/SingletonFactoryTest.php
 */
class SingletonFactoryTest extends TestCase
{
    /**
     * Test for {@covers SingletonFactory::createObject()}
     */
    public function testCreateObject(): void
    {
        $testFunction = SingletonFactory::createObject(...);

        $object1 = $testFunction(\stdClass::class);
        $object2 = $testFunction(\stdClass::class);
        $objectNotSingleton = new \stdClass;

        self::assertTrue($object1 === $object2);
        self::assertFalse($object1 === $objectNotSingleton);

        self::assertCount(1, SingletonFactory::$singletonObjects);
    }

    /**
     * Test for {@covers SingletonFactory::createObjectForIndex()}
     */
    public function testCreateObjectForIndex(): void
    {
        $testFunction = SingletonFactory::createObjectForIndex(...);

        $object1 = $testFunction('index1', \stdClass::class);
        $object2 = $testFunction('index1', \stdClass::class);
        $object3 = $testFunction('index2', \stdClass::class);
        $object4 = SingletonFactory::createObject(\stdClass::class);
        $objectNotSingleton = new \stdClass;

        self::assertTrue($object1 === $object2);
        self::assertFalse($object1 === $objectNotSingleton);
        self::assertFalse($object1 === $object3);
        self::assertFalse($object1 === $object4);

        self::assertCount(2, SingletonFactory::$uniqKeyObjects);
    }
}
