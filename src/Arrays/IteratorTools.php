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
     *  (!) Безопасно перебрать {@see \Generator} невозможно!
     *
     * См также:
     * <br>{@see ArrayHelper::countSafe()} "Защищено" вызовет `count()` для переданного значения
     *
     * @param   array|object   $iterable
     *
     * @return  int
     * @throws  \LogicException Если передан {@see \Generator}, так как его безопасно перебрать невозможно
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
     * (!) Безопасно перебрать {@see \Generator} невозможно!
     *
     * См также:
     * <br>{@see IteratorSafeRunner} Более "богатый" на возможности вариант для безопасного перебора итераторов
     *
     * @param   array|object   $iterable
     *
     * @return  \Generator
     * @throws  \LogicException Если передан {@see \Generator}, так как его безопасно перебрать невозможно
     */
    public static function iterateAndRewind(array|object $iterable): \Generator
    {
        // Генератор невозможно "безопасно" перебрать, после того как генератор достиг конца, вызвать повторно его
        // больше нельзя, т.е. $a = getGenerator();  for($a) ... Отработает, а повторный for($a) выбросит ошибку
        if ($iterable instanceof \Generator) throw new \LogicException('iterateAndRewind() is not supported for Generator objects.');

        foreach ($iterable as $key => $value) yield $key => $value;

        if ($iterable instanceof \Iterator === true && $iterable instanceof \Generator === false) $iterable->rewind();
    }
}
