<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\tests\Arrays\Objects\_resources;

/**
 * Тест класс "итератора" для тестирования функции {@see IteratorSafeRunnerTest)}
 */
class IteratorSafeRunnerTestClass implements \Iterator
{
    public int $cursor = 0;

    /** @var list */
    public array $array = [];

    /**
     * @psalm-suppress ImplementedReturnTypeMismatch Псалм ругается, что функция возвращает значение (о чем не говорит базовый интерфейс функции) и мы действительно так хотим
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->array[$this->cursor];
    }

    /**
     * @psalm-suppress ImplementedReturnTypeMismatch Псалм ругается, что функция возвращает значение (о чем не говорит базовый интерфейс функции) и мы действительно так хотим
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function next()
    {
        $this->cursor++;
    }

    /**
     * @psalm-suppress ImplementedReturnTypeMismatch Псалм ругается, что функция возвращает значение (о чем не говорит базовый интерфейс функции) и мы действительно так хотим
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return $this->cursor;
    }

    /**
     * @psalm-suppress ImplementedReturnTypeMismatch Псалм ругается, что функция возвращает значение (о чем не говорит базовый интерфейс функции) и мы действительно так хотим
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function valid()
    {
        return $this->cursor >= 0 && $this->cursor < count($this->array);
    }

    /**
     * @psalm-suppress ImplementedReturnTypeMismatch Псалм ругается, что функция возвращает значение (о чем не говорит базовый интерфейс функции) и мы действительно так хотим
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function rewind()
    {
        $this->cursor = 0;
    }
}
