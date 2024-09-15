<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests\Strings\Objects\StringIterator;

use DraculAid\PhpTools\Strings\Objects\StringIterator\StringIteratorObject;
use DraculAid\PhpTools\TestTools\PhpUnit\PhpUnitExtendTestCase;

/**
 * Test for {@see StringIteratorObject}
 *
 * @run php tests/run.php tests/Strings/Objects/StringIterator/StringIteratorObjectTest.php
 */
class StringIteratorObjectTest extends PhpUnitExtendTestCase
{
    /**
     * Test for {@see StringIteratorObject::__construct()}
     *
     * @return void
     */
    public function testConstructor(): void
    {
        $testObject = new StringIteratorObject('string-ЯБФ-test', 2);

        self::assertPropertyEquals($testObject, 'stringForIterator', 'string-ЯБФ-test');
        self::assertPropertyEquals($testObject, 'charLen', 2);

        self::assertEquals('string-ЯБФ-test', (string)$testObject);
    }

    /**
     * Test for {@see StringIteratorObject::move()}
     * Test for {@see StringIteratorObject::toPosition()}
     * Test for {@see StringIteratorObject::toStart()}
     * Test for {@see StringIteratorObject::current()}
     * Test for {@see StringIteratorObject::next()}
     * Test for {@see StringIteratorObject::key()}
     * Test for {@see StringIteratorObject::valid()}
     * Test for {@see StringIteratorObject::rewind()}
     *
     * @return void
     */
    public function testRun(): void
    {
        // Части читаемой строки
        // для "упрощения тестирования" будет использованы одно байтовые символы, но при переборе размер символа
        // будет указан в "2", благодаря чему, каждый раз будет возвращено по 2 символа
        $stringPats = ['12', '34', '56', '78', '90'];

        $testObject = new StringIteratorObject(implode('', $stringPats), 2);

        // чтение текущего символа (без смещения курсора)
        self::assertEquals($testObject->current(), '12');
        self::assertEquals($testObject->key(), 0);

        // чтение текущего символа (без смещения курсора)
        self::assertEquals($testObject->current(), '12');
        self::assertEquals($testObject->key(), 0);
        self::assertEquals($testObject->key(false), 0);
        self::assertEquals($testObject->key(true), 0);

        // смещаем курсор на 1 шаг (вариант смещения "по умолчанию") и читаем текущий символ
        $testObject->move();
        self::assertEquals($testObject->current(), '34');
        self::assertEquals($testObject->key(), 1);
        self::assertEquals($testObject->key(false), 1);
        self::assertEquals($testObject->key(true), 2);

        // смещаем курсор на 1 шаг и читаем текущий символ
        $testObject->move(1);
        self::assertEquals($testObject->current(), '56');
        self::assertEquals($testObject->key(), 2);
        self::assertEquals($testObject->key(false), 2);
        self::assertEquals($testObject->key(true), 4);

        // перемещаемся в начало
        $testObject->rewind();
        self::assertEquals($testObject->current(), '12');
        self::assertEquals($testObject->key(), 0);

        // смещаем курсор на 3 шага и читаем текущий символ
        $testObject->move(3);
        self::assertEquals($testObject->current(), '78');
        self::assertEquals($testObject->key(), 3);
        self::assertEquals($testObject->key(false), 3);
        self::assertEquals($testObject->key(true), 6);

        // смещаем за пределы строки
        self::assertTrue($testObject->valid());
        $testObject->move(2);
        self::assertFalse($testObject->valid());
        self::assertEquals($testObject->current(), '');
        self::assertEquals($testObject->key(), 5);

        // проверяем работу "отрицательного смещения"
        $testObject->toPosition(3);
        self::assertTrue($testObject->valid());
        self::assertEquals($testObject->current(), '78');
        self::assertEquals($testObject->key(), 3);
    }
}
