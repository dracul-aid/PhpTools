<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\Classes\Patterns\Iterator;

use DraculAid\PhpTools\tests\Classes\Patterns\Iterator\IteratorTraitTest;

/**
 * Трейт с реализацией некоторых "сахарных" методов интерфейса {@see IteratorInterface}
 *
 * Вы также можете использовать {@see AbstractIterator} абстрактный класс реализующий {@see IteratorInterface}
 *
 * Test cases for class {@see IteratorTraitTest}
 *
 * @psalm-require-implements IteratorInterface
 */
trait IteratorTrait
{
    /**
     * Вернет текущее значение итератора или NULL, если вышли "за пределы содержимого"
     *
     * <code>
     * while($value = currentValueAndNext()) {echo $value;}
     * </code>
     *
     * @see IteratorInterface::currentElementAndNext() Подробности по работе метода
     */
    public function currentValueAndNext(int $position = 1)
    {
        /** @var IteratorInterface $this */

        if (!$this->valid()) return null;

        $value = $this->current();
        $this->next($position);

        return $value;
    }

    /**
     * Вернет текущее значение итератора, текущий ключ и указание, не вышли ли за пределы содержимого
     *
     * <code>
     * [$key, $value, $valid] = currentElementAndNext()
     * if (!$valid) echo 'Перебрали все варианты';
     * </code>
     *
     * Описание см в @see IteratorInterface::currentElementAndNext()
     */
    public function currentElementAndNext(int $position = 1): array
    {
        /** @var IteratorInterface $this */

        if (!$this->valid()) return [null, null, false];

        $key = $this->key();
        $value = $this->current();

        $this->next($position);

        return [$key, $value, true];
    }
}
