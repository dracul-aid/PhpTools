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
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see ObjectTools}
 *
 * @run php tests/run.php tests/Classes/ObjectToolsTest.php
 */
class ObjectToolsTest extends TestCase
{
    public function testRun(): void
    {
        $this->runTestToArray();
    }

    /**
     * Test for {@see ObjectTools::propertiesFor()}
     * Test for {@see ObjectTools::toArray()}
     */
    private function runTestToArray(): void
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
