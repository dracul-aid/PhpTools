<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests\Arrays;

use DraculAid\PhpTools\Arrays\ArrayIterator;
use DraculAid\PhpTools\Classes\ClassNotPublicManager;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@coversDefaultClass ArrayIterator}
 *
 * @run php tests/run.php tests/Arrays/ArrayIteratorTest.php
 */
class ArrayIteratorTest extends TestCase
{
    /**
     * Test for {@covers ArrayIterator::map()}
     *
     * @return void
     */
    public function testAsArray(): void
    {
        self::assertFalse(false);

        // * * * Для массивов

        $arrayTest = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];

        self::assertEquals(
            [1, 2, 3, 4],
            iterator_to_array(ArrayIterator::map($arrayTest))
        );
        self::assertEquals(
            [1, 2, 3, 4],
            iterator_to_array(ArrayIterator::map($arrayTest, false))
        );
        self::assertEquals(
            ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4],
            iterator_to_array(ArrayIterator::map($arrayTest, true))
        );
        self::assertEquals(
            ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4],
            iterator_to_array(ArrayIterator::map($arrayTest, true, false))
        );

        // * * * Для объектов, имеющих доступ, как к массиву

        $arrayTest = new \ArrayObject(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4]);

        self::assertEquals(
            [1, 2, 3, 4],
            iterator_to_array(ArrayIterator::map($arrayTest))
        );
        self::assertEquals(
            [1, 2, 3, 4],
            iterator_to_array(ArrayIterator::map($arrayTest, false))
        );
        self::assertEquals(
            ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4],
            iterator_to_array(ArrayIterator::map($arrayTest, true))
        );
        self::assertEquals(
            ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4],
            iterator_to_array(ArrayIterator::map($arrayTest, true, false))
        );
    }

    /**
     * Test for {@covers ArrayIterator::mapGetKey()}
     *
     * @return void
     */
    public function testMapGetKey(): void
    {
        $notPublicProxy = ClassNotPublicManager::getInstanceFor(ArrayIterator::class);

        self::assertEquals(
            12,
            $notPublicProxy->callStatic('mapGetKey', [12, ['a' => 'aaa', 'b'=>'bbb'], true])
        );

        self::assertEquals(
            'aaa',
            $notPublicProxy->callStatic('mapGetKey', [12, ['a' => 'aaa', 'b'=>'bbb'], 'a'])
        );

        self::assertEquals(
            '333',
            $notPublicProxy->callStatic('mapGetKey', [12, ['a' => 'aaa', 'b'=>'bbb', 3 => '333'], 3])
        );
    }

    /**
     * Test for {@covers ArrayIterator::mapGetValues()}
     *
     * @return void
     */
    public function testMapGetValues(): void
    {
        $notPublicProxy = ClassNotPublicManager::getInstanceFor(ArrayIterator::class);

        // * * * Вложенный элемент строка

        self::assertEquals(
            'ABCD',
            $notPublicProxy->callStatic('mapGetValues', ['ABCD', false])
        );

        self::assertEquals(
            'B',
            $notPublicProxy->callStatic('mapGetValues', ['ABCD', 1])
        );

        // * * * Вложенный элемент масссив

        $data = ['a' => 'aaa', 'b'=>'bbb', 3 => '333'];

        self::assertEquals(
            ['a' => 'aaa', 'b'=>'bbb', 3 => '333'],
            $notPublicProxy->callStatic('mapGetValues', [$data, false])
        );

        self::assertEquals(
            'aaa',
            $notPublicProxy->callStatic('mapGetValues', [$data, 'a'])
        );

        self::assertEquals(
            '333',
            $notPublicProxy->callStatic('mapGetValues', [$data, 3])
        );

        self::assertEquals(
            ['a' => 'aaa', 3 => '333'],
            $notPublicProxy->callStatic('mapGetValues', [$data, ['a', 3]])
        );

        // * * * Вложенный элемент объект схожий с массивом

        $data = new \ArrayObject(['a' => 'aaa', 'b'=>'bbb', 3 => '333']);

        self::assertEquals(
            new \ArrayObject(['a' => 'aaa', 'b'=>'bbb', 3 => '333']),
            $notPublicProxy->callStatic('mapGetValues', [$data, false])
        );

        self::assertEquals(
            'aaa',
            $notPublicProxy->callStatic('mapGetValues', [$data, 'a'])
        );

        self::assertEquals(
            '333',
            $notPublicProxy->callStatic('mapGetValues', [$data, 3])
        );

        self::assertEquals(
            ['a' => 'aaa', 3 => '333'],
            $notPublicProxy->callStatic('mapGetValues', [$data, ['a', 3]])
        );
    }
}
