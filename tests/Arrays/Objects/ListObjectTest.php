<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests\Arrays\Objects;

use DraculAid\PhpTools\Arrays\Objects\ListObject;
use DraculAid\PhpTools\ExceptionTools\ExceptionTools;
use DraculAid\PhpTools\tests\Arrays\Objects\_resources\ListObjectForTesting;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@coversDefaultClass ListObject}
 *
 * @run php tests/run.php tests/Arrays/Objects/ListObjectTest.php
 *
 * @see \DraculAid\PhpTools\tests\Arrays\Objects\_resources\ListObjectForTesting Версия тестируемого класса, с раширенным функционалом для облегчения тестирования
 */
class ListObjectTest extends TestCase
{
    /**
     * Test for {@covers ListObject::__construct()}
     * Test for {@covers ListObject::exchangeArray()}
     * Test for {@covers ListObject::count()}
     * Test for {@covers ListObject::offsetSet()}
     * Test for {@covers ListObject::addEnd()}
     *
     * @return void
     *
     * @psalm-suppress UnusedVariable Псалм не умеет нормально работать с переменными-ссылками
     */
    public function testSetters(): void
    {
        // * * * Конструктор

        $testObject = $this->getTestListObject();
        self::assertEquals([], $testObject->getArrayCopy());
        self::assertEquals(0, $testObject->getCursor());
        self::assertEquals(0, $testObject->count());

        $testObject = $this->getTestListObject([1, 2, 3, 4]);
        self::assertEquals([1, 2, 3, 4], $testObject->getArrayCopy());
        self::assertEquals(0, $testObject->getCursor());
        self::assertEquals(4, $testObject->count());

        $testObject = $this->getTestListObject([1 => 123, 2, 33, 'c' => 45]);
        self::assertEquals([123, 2, 33, 45], $testObject->getArrayCopy());
        self::assertEquals(0, $testObject->getCursor());
        self::assertEquals(4, $testObject->count());

        // * * * Полное изменение списка

        $testObject->exchangeArray([]);
        self::assertEquals([], $testObject->getArrayCopy());
        self::assertEquals(0, $testObject->getCursor());
        self::assertEquals(0, $testObject->count());

        $testObject->exchangeArray([1, 2, 3, 4]);
        self::assertEquals([1, 2, 3, 4], $testObject->getArrayCopy());
        self::assertEquals(0, $testObject->getCursor());
        self::assertEquals(4, $testObject->count());

        $testObject->exchangeArray([1 => 123, 2, 33, 'c' => 45]);
        self::assertEquals([123, 2, 33, 45], $testObject->getArrayCopy());
        self::assertEquals(0, $testObject->getCursor());
        self::assertEquals(4, $testObject->count());

        $cursorLink = &$testObject->getCursor();
        $cursorLink = 12;
        self::assertEquals(12, $testObject->getCursor());
        $testObject->exchangeArray([11, 22, 33]);
        self::assertEquals([11, 22, 33], $testObject->getArrayCopy());
        self::assertEquals(0, $testObject->getCursor());
        self::assertEquals(3, $testObject->count());

        // * * * Запись, как в массив

        $testObject = $this->getTestListObject();
        $cursorLink = &$testObject->getCursor();
        $cursorLink = 1;

        $testObject[] = 1;
        $testObject[] = 2;
        self::assertEquals([1, 2], $testObject->getArrayCopy());
        self::assertEquals(1, $testObject->getCursor());
        self::assertEquals(2, $testObject->count());
        $testObject['x'] = 3;
        self::assertEquals([1, 2, 3], $testObject->getArrayCopy());
        self::assertEquals(1, $testObject->getCursor());
        self::assertEquals(3, $testObject->count());
        $testObject[0] = 0;
        $testObject[2] = 33;
        self::assertEquals([0, 2, 33], $testObject->getArrayCopy());
        self::assertEquals(1, $testObject->getCursor());
        self::assertEquals(3, $testObject->count());
        $testObject[10] = 10;
        self::assertEquals([0, 2, 33, 10], $testObject->getArrayCopy());
        self::assertEquals(1, $testObject->getCursor());
        self::assertEquals(4, $testObject->count());

        // * * * Функция добавления нескольких значений в конец

        self::assertTrue($testObject->addEnd(12) instanceof ListObject);
        self::assertEquals([0, 2, 33, 10, 12], $testObject->getArrayCopy());
        self::assertEquals(1, $testObject->getCursor());
        self::assertEquals(5, $testObject->count());

        $testObject->addEnd(13, 14, 15);
        self::assertEquals([0, 2, 33, 10, 12, 13, 14, 15], $testObject->getArrayCopy());
        self::assertEquals(1, $testObject->getCursor());
        self::assertEquals(8, $testObject->count());
    }

    /**
     * Test for {@covers ListObject::insert()}
     *
     * @return void
     *
     * @psalm-suppress UnusedVariable Псалм не умеет нормально работать с переменными-ссылками
     */
    public function testInsert(): void
    {
        // * * * Вставка в пустой список и Вставка "в начало"

        $testObject = $this->getTestListObject();
        $cursorLink = &$testObject->getCursor();
        $cursorLink = 1;

        $testObject->insert(0, 2, 3);
        self::assertEquals([2, 3], $testObject->getArrayCopy());
        self::assertEquals(1, $testObject->getCursor());
        self::assertEquals(2, $testObject->count());

        $testObject->insert(0, 0, 1);
        self::assertEquals([0, 1, 2, 3], $testObject->getArrayCopy());
        self::assertEquals(1, $testObject->getCursor());
        self::assertEquals(4, $testObject->count());

        $testObject->insert(0, -1, -2);
        self::assertEquals([-1, -2, 0, 1, 2, 3], $testObject->getArrayCopy());
        self::assertEquals(1, $testObject->getCursor());
        self::assertEquals(6, $testObject->count());

        // * * * Вставка в указанную позицию (положительная позиция)

        $testObject = $this->getTestListObject([0, 1, 4, 5]);
        $cursorLink = &$testObject->getCursor();
        $cursorLink = 1;

        $testObject->insert(2, 2, 3);
        self::assertEquals([0, 1, 2, 3, 4, 5], $testObject->getArrayCopy());
        self::assertEquals(1, $testObject->getCursor());
        self::assertEquals(6, $testObject->count());

        $testObject->insert(77, 6, 7);
        self::assertEquals(1, $testObject->getCursor());
        self::assertEquals(8, $testObject->count());

        // * * * Вставка в указанную позицию (отрицательная позиция)

        $testObject = $this->getTestListObject([0, 1, 2, 5]);
        $cursorLink = &$testObject->getCursor();
        $cursorLink = 1;

        $testObject->insert(-1, 3, 4);
        self::assertEquals([0, 1, 2, 3, 4, 5], $testObject->getArrayCopy());
        self::assertEquals(1, $testObject->getCursor());
        self::assertEquals(6, $testObject->count());

        $testObject->insert(-99, -1, -2);
        self::assertEquals([-1, -2, 0, 1, 2, 3, 4, 5], $testObject->getArrayCopy());
        self::assertEquals(1, $testObject->getCursor());
        self::assertEquals(8, $testObject->count());
    }

    /**
     * Test for {@covers ListObject::getArrayCopy()}
     * Test for {@covers ListObject::get()}
     * Test for {@covers ListObject::offsetGet()}
     *
     * @return void
     *
     * @psalm-suppress UnusedVariable Псалм не умеет нормально работать с переменными-ссылками
     */
    public function testGetter(): void
    {
        // * * * Возвращение списка значений

        $testObject = $this->getTestListObject();
        $cursorLink = &$testObject->getCursor();
        $cursorLink = 1;
        self::assertEquals([], $testObject->getArrayCopy());
        self::assertEquals(1, $testObject->getCursor());

        $testObject = $this->getTestListObject([1, 2, 3]);
        $cursorLink = &$testObject->getCursor();
        $cursorLink = 1;
        self::assertEquals([1, 2, 3], $testObject->getArrayCopy());
        self::assertEquals(1, $testObject->getCursor());

        $testObject = $this->getTestListObject([1, 2, 3]);
        $cursorLink = &$testObject->getCursor();
        $cursorLink = 1;
        self::assertEquals([1, 2, 3], $testObject->getArrayCopy());
        self::assertEquals(1, $testObject->getCursor());

        // * * * Возвращение конкретного значения по номеру

        $testObject = $this->getTestListObject([0, 1, 2, 3]);
        $cursorLink = &$testObject->getCursor();
        $cursorLink = 1;

        self::assertEquals(0, $testObject->get(0));
        self::assertEquals(1, $testObject->get(1));
        self::assertEquals(2, $testObject->get(2));
        self::assertEquals(1, $testObject->getCursor());

        self::assertEquals(3, $testObject->get(-1));
        self::assertEquals(2, $testObject->get(-2));
        self::assertEquals(1, $testObject->get(-3));
        self::assertEquals(1, $testObject->getCursor());

        $cursorLink = 2;
        self::assertTrue(ExceptionTools::wasCalledWithException([$testObject, 'get'], [2222], \RangeException::class));
        self::assertTrue(ExceptionTools::wasCalledWithException([$testObject, 'get'], [-2222], \RangeException::class));
        self::assertEquals(2, $testObject->getCursor());

        // * * * Возвращение конкретного значения, как из массива

        $testObject = $this->getTestListObject([1, 2, 3]);
        $cursorLink = &$testObject->getCursor();
        $cursorLink = 1;
        self::assertEquals(1, $testObject[0]);
        self::assertEquals(3, $testObject[2]);
        self::assertEquals(null, $testObject[17]);
        self::assertEquals(null, $testObject['x']);
        self::assertEquals(null, $testObject[array()]);
        self::assertEquals(1, $testObject->getCursor());
    }

    /**
     * Test for {@covers ListObject::offsetExists()}
     * Test for {@covers ListObject::keyExists()}
     *
     * @return void
     *
     * @psalm-suppress UnusedVariable Псалм не умеет нормально работать с переменными-ссылками
     */
    public function testIsset(): void
    {
        // * * * isset()

        $testObject = $this->getTestListObject([0, 1, false, null, '', [], 3]);
        $cursorLink = &$testObject->getCursor();
        $cursorLink = 1;

        self::assertTrue(isset($testObject[0]));
        self::assertTrue(isset($testObject[1]));
        self::assertTrue(isset($testObject[2]));
        // 3-ий элемент вернет "ЛОЖ"
        self::assertTrue(isset($testObject[4]));
        self::assertTrue(isset($testObject[5]));
        self::assertTrue(isset($testObject[6]));

        self::assertFalse(isset($testObject[-1]));
        self::assertFalse(isset($testObject[3]));
        self::assertFalse(isset($testObject['x']));

        self::assertEquals(1, $testObject->getCursor());

        // * * * Проверка наличия ключа

        self::assertTrue($testObject->keyExists(0));
        self::assertTrue($testObject->keyExists(1));
        self::assertTrue($testObject->keyExists(2));
        self::assertTrue($testObject->keyExists(3));
        self::assertTrue($testObject->keyExists(4));
        self::assertTrue($testObject->keyExists(5));
        self::assertTrue($testObject->keyExists(6));

        self::assertTrue($testObject->keyExists(-1));
        self::assertTrue($testObject->keyExists(-2));
        self::assertTrue($testObject->keyExists(-3));
        self::assertTrue($testObject->keyExists(-4));
        self::assertTrue($testObject->keyExists(-5));
        self::assertTrue($testObject->keyExists(-6));
        self::assertTrue($testObject->keyExists(-7));

        self::assertFalse($testObject->keyExists(7));
        self::assertFalse($testObject->keyExists(-8));

        self::assertEquals(1, $testObject->getCursor());
    }

    /**
     * Test for {@covers ListObject::offsetUnset()}
     *
     * @return void
     *
     * @psalm-suppress UnusedVariable Псалм не умеет нормально работать с переменными-ссылками
     */
    public function testUnset(): void
    {
        // попытка удаления из пустого списка
        $testObject = $this->getTestListObject();
        $testObject->offsetUnset(0);
        // попытка удаления неверного ключа
        $testObject->offsetUnset('x');

        // удаление единственного элемента

        $testObject = $this->getTestListObject(['000']);
        $testObject->offsetUnset(0);
        self::assertEquals([], $testObject->getArrayCopy());

        // * * * Удаление элемента с явно указанным номером

        $testObject = $this->getTestListObject([0, 1, 2, 3]);
        $cursorLink = &$testObject->getCursor();
        $cursorLink = 4;

        self::assertEquals($testObject, $testObject->offsetUnset(0));
        self::assertEquals([1, 2, 3], $testObject->getArrayCopy());
        self::assertEquals(4, $testObject->getCursor());
        unset($testObject[0]);
        self::assertEquals([2, 3], $testObject->getArrayCopy());
        self::assertEquals(4, $testObject->getCursor());

        // * * * проверяем, что попытка удаления "за пределами" списка не приведет к падению
        $testObject = $this->getTestListObject([0, 1, 2, 3]);
        unset($testObject[-10]);
        unset($testObject[10]);
    }

    /**
     * Test for {@covers ListObject::getIterator()}
     *
     * @return void
     *
     * @psalm-suppress UnusedVariable Псалм не умеет нормально работать с переменными-ссылками
     */
    public function testIteratorAggregate(): void
    {
        // * * * Получение массива из итератора, проверяем, что курсор не изменит свое положение

        $testObject = $this->getTestListObject([0, 1, 2, 3]);
        $cursorLink = &$testObject->getCursor();
        $cursorLink = 1;

        self::assertEquals([0, 1, 2, 3], iterator_to_array($testObject));
        self::assertEquals(1, $testObject->getCursor());

        // * * * Перебор в foreach

        $cursorLink = 2;

        $tmpArray = [];
        foreach ($testObject as $key => $value) $tmpArray[$key] = $value;
        self::assertEquals([0, 1, 2, 3], $tmpArray);
        self::assertEquals(2, $testObject->getCursor());
    }

    /**
     * Test for {@covers ListObject::current()}
     * Test for {@covers ListObject::key()}
     * Test for {@covers ListObject::next()}
     * Test for {@covers ListObject::rewind()}
     * Test for {@covers ListObject::valid()}
     * Test for {@covers ListObject::setCursor()}
     *
     * @return void
     */
    public function testIterator(): void
    {
        $testObject = $this->getTestListObject([0, 1, 2, 3]);

        $tmpArray = [];
        do {
            $tmpArray[$testObject->key()] = $testObject->current();
        } while ($testObject->next()->valid());
        self::assertEquals([0, 1, 2, 3], $tmpArray);
        self::assertEquals(4, $testObject->getCursor());

        $testObject->rewind();
        self::assertEquals(0, $testObject->getCursor());

        $testObject->setCursor(-10);
        self::assertEquals(-10, $testObject->getCursor());
        self::assertFalse($testObject->valid());

        $testObject->setCursor(99);
        self::assertEquals(99, $testObject->getCursor());
        self::assertFalse($testObject->valid());
    }

    /**
     * Создаст тестовый объект {@see ListObject}, расширив его функцией, для получения ссылки на "список"
     *
     * @param   null|array   $list   NULL создаст объект без передачи параметров в конструктор, иначе - передаст $list в конструктор
     *
     * @return ListObjectForTesting
     */
    private function getTestListObject($list = null): ListObjectForTesting
    {
        if ($list === null) return new ListObjectForTesting();

        return new ListObjectForTesting($list);
    }
}
