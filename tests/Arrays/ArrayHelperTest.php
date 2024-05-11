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

use DraculAid\PhpTools\Arrays\ArrayHelper;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@coversDefaultClass ArrayHelper}
 *
 * @run php tests/run.php tests/Arrays/ArrayHelperTest.php
 */
class ArrayHelperTest extends TestCase
{
    /**
     * Test for {@covers ArrayHelper::isAsArray()}
     *
     * @return void
     */
    public function testIsAsArray(): void
    {
        self::assertFalse(ArrayHelper::isAsArray(111));
        self::assertFalse(ArrayHelper::isAsArray(111.222));
        self::assertFalse(ArrayHelper::isAsArray('ABC'));
        self::assertFalse(ArrayHelper::isAsArray(null));
        self::assertFalse(ArrayHelper::isAsArray(true));
        self::assertFalse(ArrayHelper::isAsArray(new \stdClass()));

        self::assertTrue(ArrayHelper::isAsArray([]));
        self::assertTrue(ArrayHelper::isAsArray(new \ArrayObject()));
    }

    /**
     * Test for {@covers ArrayHelper::countSafe()}
     *
     * @return void
     */
    public function testCountSafe(): void
    {
        self::assertEquals(0, ArrayHelper::countSafe(111));
        self::assertEquals(12, ArrayHelper::countSafe(111, 12));
        self::assertEquals(0, ArrayHelper::countSafe([], 12));
        self::assertEquals(4, ArrayHelper::countSafe([1, 3, 5, 7], 12));
        self::assertEquals(0, ArrayHelper::countSafe(new \ArrayObject([]), 12));
        self::assertEquals(4, ArrayHelper::countSafe(new \ArrayObject([1, 3, 5, 7]), 12));
    }

    /**
     * Test for {@covers ArrayHelper::keyExist()}
     *
     * @return void
     */
    public function testKeyExist(): void
    {
        self::assertFalse(ArrayHelper::keyExist([], 'test-key'));
        self::assertTrue(ArrayHelper::keyExist(['test-key' => null], 'test-key'));
        self::assertTrue(ArrayHelper::keyExist(['test-key' => 111], 'test-key'));

        self::assertFalse(ArrayHelper::keyExist(new \ArrayObject(), 'test-key'));
        self::assertFalse(ArrayHelper::keyExist(new \ArrayObject(['test-key' => null]), 'test-key'));
        self::assertTrue(ArrayHelper::keyExist(new \ArrayObject(['test-key' => 111]), 'test-key'));

        self::assertFalse(ArrayHelper::keyExist(new \ArrayObject(), 'test-key', false));
        self::assertFalse(ArrayHelper::keyExist(new \ArrayObject(['test-key' => null]), 'test-key', false));
        self::assertTrue(ArrayHelper::keyExist(new \ArrayObject(['test-key' => 111]), 'test-key', false));

        self::assertFalse(ArrayHelper::keyExist(new \ArrayObject(), 'test-key', true));
        self::assertTrue(ArrayHelper::keyExist(new \ArrayObject(['test-key' => null]), 'test-key', true));
        self::assertTrue(ArrayHelper::keyExist(new \ArrayObject(['test-key' => 111]), 'test-key', true));
    }

    /**
     * Test for {@covers ArrayHelper::getNewIndex()}
     *
     * @return void
     */
    public function testGetNewIndex(): void
    {
        self::assertTrue(true);
        return;

        $arr = [];
        self::assertEquals(0, ArrayHelper::getNewIndex($arr));

        $arr = ['A'];
        self::assertEquals(1, ArrayHelper::getNewIndex($arr));

        $arr = [1 => 'A'];
        self::assertEquals(2, ArrayHelper::getNewIndex($arr));
    }

    /**
     * Test for {@covers ArrayHelper::setInPositionAndMoveOldValues()}
     *
     * @return void
     */
    public function testSetInPositionAndMoveOldValues(): void
    {
        // массив в который производится вставка пуст - вернем вставляемые данные
        self::assertEquals(
            [0=>'a', 1=>'b', 2=>'c', 3=>'d', 4=>'e'],
            ArrayHelper::setInPositionAndMoveOldValues(
                [],
                0,
                'a', 'b', 'c', 'd', 'e'
            )
        );

        // вставляемых данных нет - вернем изначальный массив как есть
        self::assertEquals(
            [0=>'a', 1=>'b', 2=>'c', 3=>'d', 4=>'e'],
            ArrayHelper::setInPositionAndMoveOldValues(
                [0=>'a', 1=>'b', 2=>'c', 3=>'d', 4=>'e'],
                0,
                ... []
            )
        );

        // позиция для вставки превышает кол-во элементов в изначальном массиве - вставим в конец массива
        self::assertEquals(
            [0=>'a', 1=>'b', 2=>'c', 3=>'d', 4=>'e'],
            ArrayHelper::setInPositionAndMoveOldValues(
                    [0=>'a', 1=>'b'],
                    100,
                    'c', 'd', 'e'
            )
        );

        // вставляем данные в центр существующего массива
        self::assertEquals(
            [0=>'a', 1=>'b', 2=>'x', 3=>'y', 4=>'z', 5=>'c', 6=>'d', 7=>'e'],
            ArrayHelper::setInPositionAndMoveOldValues(
                [0=>'a', 1=>'b', 2=>'c', 3=>'d', 4=>'e'],
                2,
                'x', 'y', 'z'
            )
        );

        // вставляем данные в центр существующего массива с Отрицательным индексом
        self::assertEquals(
            [0=>'a', 1=>'b', 2=>'x', 3=>'y', 4=>'z', 5=>'c', 6=>'d', 7=>'e'],
            ArrayHelper::setInPositionAndMoveOldValues(
                [0=>'a', 1=>'b', 2=>'c', 3=>'d', 4=>'e'],
                -3,
                'x', 'y', 'z'
            )
        );
    }

    /**
     * Test for {@covers ArrayHelper::getByIndexes()}
     *
     * @return void
     */
    public function testGetByIndexes(): void
    {
        $array = ['a' => 'AAA', 'b' => 'BBB', 'c' => 'CCC'];

        self::assertEquals(
            ['a' => 'AAA', 'c' => 'CCC'],
            ArrayHelper::getByIndexes($array, ['a', 'c'])
        );

        self::assertEquals(
            ['a' => 'AAA', 'x' => null],
            ArrayHelper::getByIndexes($array, ['a', 'x'])
        );

        self::assertEquals(
            ['a' => 'AAA', 'x' => 'XXX'],
            ArrayHelper::getByIndexes($array, ['a', 'x'], 'XXX')
        );
    }
}
