<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\Arrays;

use DraculAid\PhpTools\tests\Arrays\ArrayIteratorTest;

/**
 * Набор итераторов массивов
 *
 * - Оглавление:
 * <br>{@see ArrayIterator::map()} Позволяет перебрать массив, указав что будет "индексом" и "значениями" массива
 *
 * Test cases for class {@see ArrayIteratorTest}
 */
final class ArrayIterator
{
    /**
     * Позволяет перебрать массив (перебираемое похожее на массив). Можно указать правило формирования индекса и список возвращаемых
     * значений каждым элементом
     *
     * (!) Похожесть с массивом означает, что в ряде случаев может понадобиться обращаться к объекту, как к массиву, т.е. `$object[$index]`
     *     а также, что в случае, если значение строка - к ней тоже может быть обращение, "как к массиву" (т.е. к номерам байт)
     *
     * @param   array|iterable|(\ArrayAccess&\Traversable)   $array        Массив, или иное перебираемое
     * @param   bool|int|string                              $keyRule      Ключ элемента:
     *                                                                     <br>-FALSE: нет ключа (т.е. элементы генератора будут подобны списку)
     *                                                                     <br>-TRUE: ключ аналогичен ключу перебираемого
     *                                                                     <br>-int|string: имя поля пер
     * @param   false|int|string|array                       $valuesRule   Значения элемента генератора:
     *                                                                     <br>-FALSE: вернет все элементы
     *                                                                     <br>-int|string: имя элемента массива, который перебирается
     *                                                                     <br>-array: список индексов элементов
     *
     * @return \Generator
     *
     * @todo PHP8.2 типизация аргументов ($array)
     */
    public static function map(iterable $array, bool|int|string $keyRule = false, false|int|string|array $valuesRule = false): \Generator
    {
        foreach ($array as $index => $data)
        {
            if (!$keyRule) yield self::mapGetValues($data, $valuesRule);
            else yield self::mapGetKey($index, $data, $keyRule) => self::mapGetValues($data, $valuesRule);
        }
    }

    /**
     * Вернет для {@see self::map()} ключ
     *
     * @param   mixed                   $index     Текущий индекс
     * @param   mixed                   $data      Данные перебираемые элемента
     * @param   true|int|string|array   $keyRule   Правила формирования ключа
     *
     * @return  mixed
     *
     * @todo PHP8.2 типизация аргументов ($keyRule)
     */
    private static function mapGetKey(mixed $index, mixed $data, bool|int|string|array $keyRule): mixed
    {
        if ($keyRule === true) return $index;
        else return $data[$keyRule];
    }

    /**
     * Вернет для {@see self::map()} значение
     *
     * @param   mixed                    $data         Данные перебираемые элемента
     * @param   false|int|string|array   $valuesRule   Правила формирования значения
     *
     * @return  mixed
     */
    private static function mapGetValues(mixed $data, false|int|string|array $valuesRule): mixed
    {
        return match (true) {
            $valuesRule === false => $data,
            is_array($valuesRule) => ArrayHelper::getByIndexes($data, $valuesRule),
            default => $data[$valuesRule],
        };
    }
}
