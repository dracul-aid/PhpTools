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

/**
 * Test for {@coversDefaultClass AbstractIterator}
 *
 * @run php tests/run.php tests/Classes/Patterns/Iterator/AbstractIteratorTest.php
 */
class AbstractIteratorTest extends AbstractIteratorTestFunctions
{
    /**
     * Создаст тестовый объект
     *
     * @return AbstractIterator
     */
    protected function getTestObject(): AbstractIterator
    {
        return new class() extends AbstractIterator {

            public int $__test__maxPosition = 10;
            public int $__test__position = 0;

            public function valid(): bool
            {
                return ($this->__test__position >= 0 && $this->__test__position < $this->__test__maxPosition);
            }

            public function getIterator(): \Traversable
            {
                throw new \LogicException('This method should not be called');
            }

            public function current()
            {
                if ($this->__test__position === 2) return null;

                return $this->__test__position . 'abc';
            }

            public function key()
            {
                return $this->__test__position;
            }

            public function next(int $position = 1)
            {
                $this->__test__position += $position;

                return $this;
            }

            public function rewind()
            {
                $this->__test__position = 0;

                return $this;
            }
        };
    }
}
