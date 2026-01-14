<?php

namespace DraculAid\PhpTools\Arrays;

use DraculAid\PhpTools\Arrays\Objects\IteratorSafeRunner;

/**
 * Набор инструментов для работы с итераторами (массивы, перебор объектов, а также {@see \Traversable})
 *
 * Оглавление:
 * <br>{@see IteratorTools::count()} Вернет кол-во элементов в перебираемом
 * <br>{@see IteratorTools::iterateAndRewind()} Переберет перебираемое, если надо, перемотав его в начало после перебора
 */
class IteratorTools
{
    /**
     * Вернет кол-во элементов в перебираемом, даже если он не реализует {@see \Countable}
     *
     * (!) Обратите внимание, что если не реализован {@see \Countable}, то вычисление кол-ва элементов будет произведено
     *     с помощью перебора, для {@see Traversable} объектов это может быть опасно, например, если итератор/генератор
     *     должен выполнить какую-то работу, он ее выполнит.
     *
     * (!) Также обратите внимание, объекты реализующие перебор через {@see \Iterator} после перебора будут "перемотаны"
     *     в начало с помощью {@see \Iterator::rewind()}
     *
     * (!) Если передан {@see \Generator}, то для его перебора будет создан его клон (так как объекты-генераторы можно выполнить только 1 раз)
     *
     * См также:
     * <br>{@see ArrayHelper::countSafe()} "Защищено" вызовет `count()` для переданного значения
     *
     * @param   array|object   $iterable
     *
     * @return int
     */
    public static function count(array|object $iterable): int
    {
        if (is_array($iterable) || $iterable instanceof \Countable) return count($iterable);

        // * * * Если у перебираемого нет поддержки работы с функцией `count()`

        $count = 0;
        foreach (self::iterateAndRewind($iterable) as $value) {
            $count++;
        }

        return $count;
    }

    /**
     * Перебирает "перебираемое", в случае надобности, после перебора перемотает в начало
     *
     * (!) Объекты реализующие {@see \Iterator} будут "перемотаны" в начало
     *
     * (!) Если передан {@see \Generator}, то для его перебора будет создан его клон (так как объекты-генераторы можно выполнить только 1 раз)
     *
     * См также:
     * <br>{@see IteratorSafeRunner} Более "богатый" на возможности вариант для безопасного перебора итераторов
     *
     * @param   array|object   $iterable
     *
     * @return  \Generator
     */
    public static function iterateAndRewind(array|object $iterable): \Generator
    {
        // Генераторы нельзя "перемотать назад"
        if ($iterable instanceof \Generator) $iterable = clone $iterable;

        foreach ($iterable as $key => $value) yield $key => $value;

        if ($iterable instanceof \Iterator === true && $iterable instanceof \Generator === false) $iterable->rewind();
    }
}
