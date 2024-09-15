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

use DraculAid\PhpTools\Strings\Objects\StringIterator\Utf8IteratorObject;
use DraculAid\PhpTools\TestTools\PhpUnit\PhpUnitExtendTestCase;

/**
 * Test for {@see Utf8IteratorObject}
 *
 * @run php tests/run.php tests/Strings/Objects/StringIterator/Utf8IteratorObjectTest.php
 */
class Utf8IteratorObjectTest extends PhpUnitExtendTestCase
{
    /**
     * Test for {@see Utf8IteratorObject::calculationCharLen()}
     */
    public function testCalculationCharLen(): void
    {
        self::assertEquals(1, Utf8IteratorObject::calculationCharLen(mb_chr(127)));

        self::assertEquals(2, Utf8IteratorObject::calculationCharLen(mb_chr(128)));
        self::assertEquals(2, Utf8IteratorObject::calculationCharLen(mb_chr(2047)));

        self::assertEquals(3, Utf8IteratorObject::calculationCharLen(mb_chr(2048)));
    }

    /**
     * Test for {@see Utf8IteratorObject::__construct()}
     * Test for {@see Utf8IteratorObject::toPosition()}
     * Test for {@see Utf8IteratorObject::toStart()}
     * Test for {@see Utf8IteratorObject::current()}
     * Test for {@see Utf8IteratorObject::next()}
     * Test for {@see Utf8IteratorObject::getCharLen()}
     * Test for {@see Utf8IteratorObject::key()}
     * Test for {@see Utf8IteratorObject::valid()}
     * Test for {@see Utf8IteratorObject::rewind()}
     *
     * @return void
     */
    public function testRun(): void
    {
        $testObject = new Utf8IteratorObject('sfxzwЯqБФuП');

        self::assertPropertyEquals($testObject, 'stringForIterator', 'sfxzwЯqБФuП');

        // чтение текущего символа (латиница)
        self::assertEquals($testObject->current(), 's');
        self::assertEquals($testObject->key(), 0);

        // чтение текущего символа (без смещения курсора)
        self::assertEquals($testObject->current(), 's');
        self::assertEquals($testObject->getCharLen(), 1);
        self::assertEquals($testObject->key(), 0);
        self::assertEquals($testObject->key(false), 0);
        self::assertEquals($testObject->key(true), 0);

        // смещаем курсор на 1 шаг после латинского символа (вариант смещения "по умолчанию") и читаем текущий символ (кириллица)
        $testObject->next();
        self::assertEquals($testObject->current(), 'f');
        self::assertEquals($testObject->getCharLen(), 1);
        self::assertEquals($testObject->key(), 1);
        self::assertEquals($testObject->key(false), 1);
        self::assertEquals($testObject->key(true), 1);

        // смещаем курсор на 1 шаг после латинского символа и читаем текущий символ (кириллица)
        $testObject->next(1);
        self::assertEquals($testObject->current(), 'x');
        self::assertEquals($testObject->getCharLen(), 1);
        self::assertEquals($testObject->key(), 2);
        self::assertEquals($testObject->key(false), 2);
        self::assertEquals($testObject->key(true), 2);

        // смещаем курсор на 2 шага после латинского символа и читаем текущий символ (кириллица)
        $testObject->next(2);
        self::assertEquals($testObject->current(), 'w');
        self::assertEquals($testObject->getCharLen(), 1);
        self::assertEquals($testObject->key(), 4);
        self::assertEquals($testObject->key(false), 4);
        self::assertEquals($testObject->key(true), 4);

        // смещаем курсор на 1 шаг после латинского символа (вариант смещения "по умолчанию") и читаем текущий символ (кириллица)
        $testObject->next();
        self::assertEquals($testObject->current(), 'Я');
        self::assertEquals($testObject->getCharLen(), 2);
        self::assertEquals($testObject->key(), 5);
        self::assertEquals($testObject->key(false), 5);
        self::assertEquals($testObject->key(true), 5);

        // смещаем курсор на 1 шаг после кириллического символа и читаем текущий символ (латиница)
        $testObject->next(1);
        self::assertEquals($testObject->current(), 'q');
        self::assertEquals($testObject->getCharLen(), 1);
        self::assertEquals($testObject->key(), 6);
        self::assertEquals($testObject->key(false), 6);
        self::assertEquals($testObject->key(true), 7);

        // смещаем курсор на 2 шага после латинского символа и читаем текущий символ (кириллица)
        // смещение идет по 2 кириллическим символам
        $testObject->next(2);
        self::assertEquals($testObject->current(), 'Ф');
        self::assertEquals($testObject->getCharLen(), 2);
        self::assertEquals($testObject->key(), 8);
        self::assertEquals($testObject->key(false), 8);
        self::assertEquals($testObject->key(true), 10);

        // смещаем курсор на 2 шага после кириллического символа и читаем текущий символ (кириллица)
        // смещение идет по 1 латинскому и 1 кириллическому символу
        $testObject->next(2);
        self::assertEquals($testObject->current(), 'П');
        self::assertEquals($testObject->getCharLen(), 2);
        self::assertEquals($testObject->key(), 10);
        self::assertEquals($testObject->key(false), 10);
        self::assertEquals($testObject->key(true), 13);
    }
}
