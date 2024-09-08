<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests\Console;

use DraculAid\PhpTools\Arrays\Objects\ListObject;
use DraculAid\PhpTools\Classes\ClassNotPublicManager;
use DraculAid\PhpTools\Classes\ClassTools;
use DraculAid\PhpTools\Console\ConsoleArgumentsObject;
use DraculAid\PhpTools\ExceptionTools\ExceptionTools;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@coversDefaultClass ConsoleArgumentsObject}
 *
 * @run php tests/run.php tests/Console/ConsoleArgumentsObjectTest.php
 */
class ConsoleArgumentsObjectTest extends TestCase
{
    public function testRun(): void
    {
        $this->testConstructorAndProperty();
        $this->testGetIteratorAndCounts();
        $this->testSetArgument();
        $this->testSetName();

        $this->testGetByPosition();
        $this->testGetByName();
        $this->testGetNameByPosition();
        $this->testGetPositionByName();

        $this->testOffsetExists();
        $this->testOffsetGet();
        $this->testOffsetSet();
        $this->testOffsetUnset();

        $this->testToString();
    }

    /**
     * Test for {@covers ConsoleArgumentsObject::__construct()}
     * Test for {@covers ConsoleArgumentsObject::$script}
     * Test for {@covers ConsoleArgumentsObject::$arguments}
     * Test for {@covers ConsoleArgumentsObject::$nameAndPosition}
     * Test for {@covers ConsoleArgumentsObject::$positionAndName}
     *
     * @return void
     */
    private function testConstructorAndProperty(): void
    {
        $testObject = new ConsoleArgumentsObject();

        self::assertEquals('', $testObject->script);
        $testObject->script = 'test.php';
        self::assertEquals('test.php', $testObject->script);

        self::assertEquals(0, ClassNotPublicManager::readProperty($testObject, 'arguments')->count());
        self::assertEquals([], ClassNotPublicManager::readProperty($testObject, 'nameAndPosition'));
        self::assertEquals([], ClassNotPublicManager::readProperty($testObject, 'positionAndName'));
    }

    /**
     * Test for {@covers ConsoleArgumentsObject::getIterator()}
     * Test for {@covers ConsoleArgumentsObject::count()}
     * Test for {@covers ConsoleArgumentsObject::countNames()}
     *
     * @return void
     */
    private function testGetIteratorAndCounts(): void
    {
        $testObject = ClassTools::createObject(ConsoleArgumentsObject::class);

        ClassNotPublicManager::writeProperty($testObject, 'arguments', new ListObject(['aaa', 'bbb', true]));
        self::assertEquals(['aaa', 'bbb', true], iterator_to_array($testObject));
        self::assertEquals(['aaa', 'bbb', true], iterator_to_array($testObject->getIterator()));
        self::assertEquals(['aaa', 'bbb', true], iterator_to_array($testObject->getIterator(false)));
        self::assertEquals([], iterator_to_array($testObject->getIterator(true)));
        self::assertEquals(3, $testObject->count());
        self::assertEquals(0, $testObject->countNames());

        ClassNotPublicManager::writeProperty($testObject, 'nameAndPosition', ['a' => 0, 'x' => 2]);
        self::assertEquals(['aaa', 'bbb', true], iterator_to_array($testObject));
        self::assertEquals(['aaa', 'bbb', true], iterator_to_array($testObject->getIterator()));
        self::assertEquals(['aaa', 'bbb', true], iterator_to_array($testObject->getIterator(false)));
        self::assertEquals(['a' => 'aaa', 'x' => true], iterator_to_array($testObject->getIterator(true)));
        self::assertEquals(3, $testObject->count());
        self::assertEquals(2, $testObject->countNames());
    }

    /**
     * Test for {@covers ConsoleArgumentsObject::setArgument()}
     *
     * @return void
     * @throws \Throwable
     */
    private function testSetArgument(): void
    {
        $testObject = new ConsoleArgumentsObject();

        // Создание новых элементов

        $testObject->setArgument(0, 'abc');
        $testObject->setArgument(1, true);
        $testObject->setArgument(2, 'xxxx');

        self::assertEquals(['abc', true, 'xxxx'], iterator_to_array($testObject));
        self::assertEquals([], iterator_to_array($testObject->getIterator(true)));

        // редактирование

        $testObject->setArgument(1, 'ggg');

        self::assertEquals(['abc', 'ggg', 'xxxx'], iterator_to_array($testObject));
        self::assertEquals([], iterator_to_array($testObject->getIterator(true)));

        // запись с неверной позицией - будут выброшены ошибки

        self::assertTrue(
            ExceptionTools::wasCalledWithException(
                [$testObject, 'setArgument'],
                [-1, 'fffffff'],
                \RangeException::class
            )
        );

        self::assertEquals(3, $testObject->count());
        self::assertTrue(
            ExceptionTools::wasCalledWithException(
                [$testObject, 'setArgument'],
                [4, 'fffffff'],
                \RangeException::class
            )
        );
    }

    /**
     * Test for {@covers ConsoleArgumentsObject::setName()}
     *
     * @return void
     * @throws \Throwable
     */
    private function testSetName(): void
    {
        $testObject = new ConsoleArgumentsObject();

        $testObject->setArgument(0, 'abc');
        $testObject->setArgument(1, true);
        $testObject->setArgument(2, 'xxxx');

        // установка имени

        $testObject->setName(0, 'first');

        self::assertEquals(['abc', true, 'xxxx'], iterator_to_array($testObject));
        self::assertEquals(['first' => 'abc'], iterator_to_array($testObject->getIterator(true)));

        // установка еще одного имени

        $testObject->setName(1, 'second');

        self::assertEquals(['abc', true, 'xxxx'], iterator_to_array($testObject));
        self::assertEquals(['first' => 'abc', 'second' => true], iterator_to_array($testObject->getIterator(true)));

        // переименование

        $testObject->setName(0, 'new-first'); // элемент с именем "first" изменит имя на "new-first"

        self::assertEquals(['abc', true, 'xxxx'], iterator_to_array($testObject));
        self::assertEquals(['new-first' => 'abc', 'second' => true], iterator_to_array($testObject->getIterator(true)));

        self::assertEquals(['new-first' => 0, 'second' => 1], ClassNotPublicManager::readProperty($testObject, 'nameAndPosition'));
        self::assertEquals([0 => 'new-first', 1 => 'second'], ClassNotPublicManager::readProperty($testObject, 'positionAndName'));

        // установка уже занятого имени

        $testObject->setName(2, 'new-first');

        self::assertEquals(['abc', true, 'xxxx'], iterator_to_array($testObject));
        self::assertEquals(['new-first' => 'xxxx', 'second' => true], iterator_to_array($testObject->getIterator(true)));

        self::assertEquals(['new-first' => 2, 'second' => 1], ClassNotPublicManager::readProperty($testObject, 'nameAndPosition'));
        self::assertEquals([2 => 'new-first', 1 => 'second'], ClassNotPublicManager::readProperty($testObject, 'positionAndName'));

        // попытка установить имя элементу, которого нет

        self::assertTrue(
            ExceptionTools::wasCalledWithException(
                [$testObject, 'setName'],
                [-1, 'fffffff'],
                \RangeException::class
            )
        );

        self::assertEquals(3, $testObject->count());
        self::assertTrue(
            ExceptionTools::wasCalledWithException(
                [$testObject, 'setName'],
                [4, 'fffffff'],
                \RangeException::class
            )
        );

        // удаление имени

        $testObject->setName(2, '');

        self::assertEquals(['abc', true, 'xxxx'], iterator_to_array($testObject));
        self::assertEquals(['second' => true], iterator_to_array($testObject->getIterator(true)));

        self::assertEquals(['second' => 1], ClassNotPublicManager::readProperty($testObject, 'nameAndPosition'));
        self::assertEquals([1 => 'second'], ClassNotPublicManager::readProperty($testObject, 'positionAndName'));
    }

    /**
     * Test for {@covers ConsoleArgumentsObject::getByPosition()}
     *
     * @return void
     * @throws \Throwable
     */
    private function testGetByPosition(): void
    {
        $testObject = new ConsoleArgumentsObject();

        $testObject->setArgument(0, 'abc');
        $testObject->setArgument(1, true);
        $testObject->setArgument(2, null);

        // Ищем существующие элементы

        self::assertEquals('abc', $testObject->getByPosition(0));
        self::assertEquals(true, $testObject->getByPosition(1));
        self::assertEquals(null, $testObject->getByPosition(2));

        // Ищем несуществующие элементы

        self::assertEquals(null, $testObject->getByPosition(-1, false));
        self::assertEquals(null, $testObject->getByPosition(4, false));

        self::assertTrue(
            ExceptionTools::wasCalledWithException(
                [$testObject, 'getByPosition'],
                [4],
                \RangeException::class
            )
        );
        self::assertTrue(
            ExceptionTools::wasCalledWithException(
                [$testObject, 'getByPosition'],
                [-1, true],
                \RangeException::class
            )
        );
    }

    /**
     * Test for {@covers ConsoleArgumentsObject::getByName()}
     *
     * @return void
     * @throws \Throwable
     */
    private function testGetByName(): void
    {
        $testObject = new ConsoleArgumentsObject();

        $testObject->setArgument(0, 'abc');
        $testObject->setArgument(1, true);
        $testObject->setArgument(2, null);

        $testObject->setName(0, 'string');
        $testObject->setName(1, 'first');
        $testObject->setName(2, 'second');

        // Ищем существующие элементы

        self::assertEquals('abc', $testObject->getByName('string'));
        self::assertEquals(true, $testObject->getByName('first'));
        self::assertEquals(null, $testObject->getByName('second'));

        // Ищем несуществующие элементы

        self::assertEquals(null, $testObject->getByName('xxx', false));

        self::assertTrue(
            ExceptionTools::wasCalledWithException(
                [$testObject, 'getByName'],
                ['xxx'],
                \RangeException::class
            )
        );

    }

    /**
     * Test for {@covers ConsoleArgumentsObject::getNameByPosition()}
     *
     * @return void
     * @throws \Throwable
     */
    private function testGetNameByPosition(): void
    {
        $testObject = new ConsoleArgumentsObject();

        $testObject->setArgument(0, 'abc');
        $testObject->setArgument(1, true);

        $testObject->setName(0, 'string');
        $testObject->setName(1, 'first');

        // Ищем существующие элементы

        self::assertEquals('string', $testObject->getNameByPosition(0));
        self::assertEquals('first', $testObject->getNameByPosition(1));

        // Ищем несуществующие элементы

        self::assertEquals(null, $testObject->getNameByPosition(-1, false));
        self::assertEquals(null, $testObject->getNameByPosition(4, false));

        self::assertTrue(
            ExceptionTools::wasCalledWithException(
                [$testObject, 'getNameByPosition'],
                [4],
                \RangeException::class
            )
        );
        self::assertTrue(
            ExceptionTools::wasCalledWithException(
                [$testObject, 'getNameByPosition'],
                [-1, true],
                \RangeException::class
            )
        );
    }

    /**
     * Test for {@covers ConsoleArgumentsObject::getPositionByName()}
     *
     * @return void
     * @throws \Throwable
     */
    private function testGetPositionByName(): void
    {
        $testObject = new ConsoleArgumentsObject();

        $testObject->setArgument(0, 'abc');
        $testObject->setArgument(1, true);

        $testObject->setName(0, 'string');
        $testObject->setName(1, 'first');

        // Ищем существующие элементы

        self::assertEquals(0, $testObject->getPositionByName('string'));
        self::assertEquals(1, $testObject->getPositionByName('first'));

        // Ищем несуществующие элементы

        self::assertEquals(null, $testObject->getPositionByName('xxx', false));

        self::assertTrue(
            ExceptionTools::wasCalledWithException(
                [$testObject, 'getPositionByName'],
                ['xxx'],
                \RangeException::class
            )
        );
    }

    /**
     * Test for {@covers ConsoleArgumentsObject::offsetExists()}
     *
     * @return void
     * @throws \Throwable
     */
    private function testOffsetExists(): void
    {
        $testObject = new ConsoleArgumentsObject();

        $testObject->setArgument(0, '');
        $testObject->setArgument(1, true);

        $testObject->setName(0, 'string');
        $testObject->setName(1, 'first');

        // Ищем существующие элементы

        self::assertTrue(isset($testObject[0]));
        self::assertTrue(isset($testObject[1]));
        self::assertTrue(isset($testObject['string']));
        self::assertTrue(isset($testObject['first']));

        // Ищем несуществующие элементы

        self::assertFalse(isset($testObject[-1]));
        self::assertFalse(isset($testObject[2]));
        self::assertFalse(isset($testObject['xxx']));
    }

    /**
     * Test for {@covers ConsoleArgumentsObject::offsetGet()}
     *
     * @return void
     * @throws \Throwable
     */
    private function testOffsetGet(): void
    {
        $testObject = new ConsoleArgumentsObject();

        $testObject->setArgument(0, 'aaa');
        $testObject->setArgument(1, true);

        $testObject->setName(0, 'string');
        $testObject->setName(1, 'first');

        // Ищем существующие элементы

        self::assertEquals('aaa', $testObject[0]);
        self::assertEquals(true, $testObject[1]);
        self::assertEquals('aaa', $testObject['string']);
        self::assertEquals(true, $testObject['first']);

        // Ищем несуществующие элементы

        self::assertNull($testObject[-1]);
        self::assertNull($testObject[2]);
        self::assertNull($testObject['xxxx']);
    }

    /**
     * Test for {@covers ConsoleArgumentsObject::offsetSet()}
     *
     * @return void
     * @throws \Throwable
     */
    private function testOffsetSet(): void
    {

    }

    /**
     * Test for {@covers ConsoleArgumentsObject::offsetUnset()}
     *
     * @return void
     * @throws \Throwable
     */
    private function testOffsetUnset(): void
    {

    }

    /**
     * Test for {@covers ConsoleArgumentsObject::__toString()}
     *
     * @return void
     * @throws \Throwable
     */
    private function testToString(): void
    {

    }
}
