<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests\Classes\Patterns\Iterator;

use DraculAid\PhpTools\Classes\Patterns\Iterator\AbstractIterator;
use DraculAid\PhpTools\Classes\Patterns\Iterator\IteratorInterface;
use DraculAid\PhpTools\Classes\Patterns\Iterator\IteratorTrait;
use PHPUnit\Framework\TestCase;

/**
 * Абстрактный класс для тестирования {@see IteratorTrait} и {@see AbstractIterator}
 */
abstract class AbstractIteratorTestFunctions extends TestCase
{
    /** Сгенерирует тестовый объект (реализует класс для трейта или класс для абстрактного класса) */
    abstract protected function getTestObject(): IteratorInterface;

    public function test(): void
    {
        $this->testCurrentValueAndNext();
        $this->testCurrentElementAndNext();
    }

    /**
     * Test for {@covers IteratorTrait::currentValueAndNext()}
     * Test for {@covers AbstractIterator::currentValueAndNext()}
     *
     * @return void
     */
    protected function testCurrentValueAndNext(): void
    {
        $testObject = $this->getTestObject();

        self::assertEquals('0abc', $testObject->currentValueAndNext());
        self::assertEquals('1abc', $testObject->currentValueAndNext());

        // для ключа с значением "2" захардкоженно, что он должен вернуть NULL, если бы не хардкод, вернул бы '2abc'
        self::assertEquals(2, $testObject->key());
        self::assertEquals(null, $testObject->currentValueAndNext());

        // явное указание дальнейшего перемещения
        self::assertEquals('3abc', $testObject->currentValueAndNext(1));
        self::assertEquals(4, $testObject->key());
        self::assertEquals('4abc', $testObject->currentValueAndNext(2));
        self::assertEquals('6abc', $testObject->current());
        self::assertEquals(6, $testObject->key());

        // перемещение "назад"
        self::assertEquals('6abc', $testObject->currentValueAndNext(-2));
        self::assertEquals('4abc', $testObject->current());
        self::assertEquals(4, $testObject->key());

        // перемещение "за пределы" возможно
        self::assertEquals('4abc', $testObject->currentValueAndNext(-100));
        self::assertEquals(-96, $testObject->key());
        self::assertEquals(null, $testObject->currentValueAndNext());

        /** @psalm-suppress NoInterfaceProperties В докблоках нельзя типизировать определенную переменную, как объект определенного анонимного класса */
        $testObject->__test__position = 0;
        self::assertEquals('0abc', $testObject->currentValueAndNext(200));
        self::assertEquals(200, $testObject->key());
        self::assertEquals(null, $testObject->currentValueAndNext());

        /** @psalm-suppress NoInterfaceProperties В докблоках нельзя типизировать определенную переменную, как объект определенного анонимного класса */
        $testObject->__test__position = 9;
        self::assertEquals('9abc', $testObject->currentValueAndNext());
        self::assertNull($testObject->currentValueAndNext());
    }

    /**
     * Test for {@covers IteratorTrait::currentElementAndNext()}
     * Test for {@covers AbstractIterator::currentElementAndNext()}
     *
     * @return void
     */
    protected function testCurrentElementAndNext(): void
    {
        $testObject = $this->getTestObject();

        self::assertEquals([0, '0abc', true], $testObject->currentElementAndNext());
        self::assertEquals([1, '1abc', true], $testObject->currentElementAndNext());

        // для ключа с значением "2" захардкоженно, что он должен вернуть NULL, если бы не хардкод, вернул бы '2abc'
        self::assertEquals(2, $testObject->key());
        self::assertEquals([2, null, true], $testObject->currentElementAndNext());

        // явное указание дальнейшего перемещения
        self::assertEquals([3, '3abc', true], $testObject->currentElementAndNext(1));
        self::assertEquals(4, $testObject->key());
        self::assertEquals([4, '4abc', true], $testObject->currentElementAndNext(2));
        self::assertEquals('6abc', $testObject->current());
        self::assertEquals(6, $testObject->key());

        // перемещение "назад"
        self::assertEquals([6, '6abc', true], $testObject->currentElementAndNext(-2));
        self::assertEquals('4abc', $testObject->current());
        self::assertEquals(4, $testObject->key());

        // перемещение "за пределы" возможно
        self::assertEquals([4, '4abc', true], $testObject->currentElementAndNext(-100));
        self::assertEquals(-96, $testObject->key());
        self::assertEquals([null, null, false], $testObject->currentElementAndNext());

        /** @psalm-suppress NoInterfaceProperties В докблоках нельзя типизировать определенную переменную, как объект определенного анонимного класса */
        $testObject->__test__position = 0;
        self::assertEquals([0, '0abc', true], $testObject->currentElementAndNext(200));
        self::assertEquals(200, $testObject->key());
        self::assertEquals([null, null, false], $testObject->currentElementAndNext());

        /** @psalm-suppress NoInterfaceProperties В докблоках нельзя типизировать определенную переменную, как объект определенного анонимного класса */
        $testObject->__test__position = 9;
        self::assertEquals([9, '9abc', true], $testObject->currentElementAndNext());
        self::assertEquals([null, null, false], $testObject->currentElementAndNext());
    }
}
