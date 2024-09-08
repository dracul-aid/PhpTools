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

/**
 * Абстрактный класс для классов, реализующих функционал итераторов {@see IteratorInterface}
 *
 * "Сахарные методы" из {@see IteratorInterface} также реализованны в трейте {@see IteratorTrait}
 */
abstract class AbstractIterator implements IteratorInterface
{
    /** @inheritdoc */
    public function currentValueAndNext(int $position = 1)
    {
        if (!$this->valid()) return null;

        $value = $this->current();
        $this->next($position);

        return $value;
    }

    /** @inheritdoc */
    public function currentElementAndNext(int $position = 1): array
    {
        if (!$this->valid()) return [null, null, false];

        $key = $this->key();
        $value = $this->current();

        $this->next($position);

        return [$key, $value, true];
    }
}
