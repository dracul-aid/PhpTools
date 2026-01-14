<?php

namespace DraculAid\PhpTools\tests\Arrays;

use DraculAid\PhpTools\Arrays\IteratorTools;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@coversDefaultClass IteratorTools}
 *
 * @run php tests/run.php tests/Arrays/IteratorToolsTest.php
 */
class IteratorToolsTest extends TestCase
{
    /**
     * Test for {@covers IteratorTools::count()}
     * Test for {@covers IteratorTools::iterateAndRewind()} Будет вызван во всех вариантах во время работы функции подсчета кол-ва элементов
     *
     * @return void
     *
     * @psalm-suppress NoInterfaceProperties Псалм не понимает аннотацию return в {@see self::getTestIterator()}
     */
    public function testCount(): void
    {
        self::assertEquals(0, IteratorTools::count([]));
        self::assertEquals(3, IteratorTools::count([1, 3, 5]));

        self::assertEquals(2, IteratorTools::count($this->getTestObject()));

        self::assertEquals(222, IteratorTools::count($this->getTestCountable()));

        self::assertEquals(3, IteratorTools::count($this->getTestGenerator()));

        $testIterator = $this->getTestIterator();
        self::assertEquals(5, IteratorTools::count($testIterator));
        self::assertEquals(0, $testIterator->cursor);
    }

    private function getTestObject(): object
    {
        return new class() {
            public int $public_var = 123;
            public string $public_var_2 = 'ABC';

            protected int $protected_var = 456;
            private int $private_var = 789;

            public function f1(): string
            {
                return 'test';
            }
        };

    }

    private function getTestCountable(): \Countable
    {
        return new class() implements \Countable {
            public function count(): int
            {
                return 222;
            }
        };
    }

    public function getTestGenerator(): \Generator
    {
        yield 'A';
        yield 'B';
        yield 'C';
    }

    /**
     * @return \Iterator&object{array: array<int, string>, cursor: int}
     *
     * @psalm-suppress InvalidReturnType PSALM, в отличие от Шторма не понимает аннотацию return в этом методе
     * @psalm-suppress InvalidReturnStatement PSALM, в отличие от Шторма не понимает аннотацию return в этом методе
     */
    public function getTestIterator(): \Iterator
    {
        return new class () implements \Iterator {
            public $array = ['A', 'B', 'C', 'D', 'E'];
            public $cursor = 0;

            public function current(): mixed
            {
                return $this->array[$this->cursor];
            }

            public function next(): void
            {
                $this->cursor++;
            }

            public function key(): mixed
            {
                return $this->cursor;
            }

            public function valid(): bool
            {
                return isset($this->array[$this->cursor]);
            }

            public function rewind(): void
            {
                $this->cursor = 0;
            }
        };
    }
}
