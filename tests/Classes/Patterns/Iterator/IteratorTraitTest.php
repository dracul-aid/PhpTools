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

use DraculAid\PhpTools\Classes\Patterns\Iterator\IteratorInterface;
use DraculAid\PhpTools\Classes\Patterns\Iterator\IteratorTrait;

/**
 * Test for {@coversDefaultClass IteratorTrait}
 *
 * @run php tests/run.php tests/Classes/Patterns/Iterator/IteratorTraitTest.php
 */
class IteratorTraitTest extends AbstractIteratorTestFunctions
{
    /**
     * Создаст тестовый объект
     *
     * @return IteratorInterface
     */
    protected function getTestObject(): IteratorInterface
    {
        return new class() implements IteratorInterface {
            use IteratorTrait;

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

            public function current(): mixed
            {
                if ($this->__test__position === 2) return null;

                return $this->__test__position . 'abc';
            }

            public function key(): mixed
            {
                return $this->__test__position;
            }

            public function next(int $position = 1): self
            {
                $this->__test__position += $position;

                return $this;
            }

            public function rewind(): self
            {
                $this->__test__position = 0;

                return $this;
            }
        };
    }
}
