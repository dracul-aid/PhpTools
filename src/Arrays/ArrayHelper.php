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

use DraculAid\PhpTools\Arrays\Objects\Interfaces\ArrayInterface;
use DraculAid\PhpTools\Classes\ClassTools;
use DraculAid\PhpTools\tests\Arrays\ArrayHelperTest;

/**
 * Набор функций для работы с массива
 * (Также работает с массивоподобными объектами, см {@see ArrayInterface})
 *
 * - См также:
 * <br>{@see ClassTools::isAsArray()} Проверит, похож ли объект на массив
 *
 * - Оглавление:
 * <br>--- Проверки
 * <br>{@see ArrayHelper::isAsArray()} Проверит, что значение является массивом или объектом, похожим на массив
 * <br>{@see ArrayHelper::keyExist()} Проверит, есть ли в массиве или объекте схожем с массивом значение, по указанному ключу
 ** <br>--- Работа с данными
 * <br>{@see ArrayHelper::getNewIndex()} Вернет числовой индекс, который будет присвоен новому элементу массива
 * <br>{@see ArrayHelper::countSafe()} Вернет кол-во элементов в массиве, если посчитать невозможно - вернет "значение по умолчанию"
 * <br>{@see ArrayHelper::setInPositionAndMoveOldValues()} Вставит новые значения в массив (начиная с указанной позиции)
 * <br>{@see ArrayHelper::getByIndexes()} Вернет срез массива, по списку указанных индексов массива
 *
 * Test cases for class {@see ArrayHelperTest}
 */
final class ArrayHelper
{
    /**
     * Проверит, что переданное значение массив, или объект похожий на массив
     *
     * (!) Объект считается похожим на массив, если его можно перебирать (реализован {@see \Traversable}) и к элементам
     *     разрешен доступ, как к элементам массива ({@see \ArrayAccess}). Поддержка функции {@see count()} опциональна
     *
     * @see ClassTools::isAsArray() Проверит, похож ли класс или объект на массив
     * @see ArrayInterface Интерфейс для объектов, схожих с массивами
     *
     * @param   mixed   $asArray     Значение для проверки
     * @param   bool    $countable   Должно ли переданное значение корректно отрабатываться функцией {@see count()}
     *
     * @return  bool
     *
     * @todo PHP8 Типизация для аргументов
     */
    public static function isAsArray($asArray, bool $countable = true): bool
    {
        if (is_array($asArray)) return true;

        // все случаи дальше имеют смысл только для массивов
        if (!is_object($asArray)) return false;

        if ($asArray instanceof \Traversable === false || $asArray instanceof \ArrayAccess === false) return false;

        if ($countable) return $asArray instanceof \Countable;

        return true;
    }

    /**
     * Защищено вызовет {@see count()} для значения, если значение не поддерживает подсчет кол-ва элементов, вернет
     * заранее установленное значение
     *
     * @param   mixed   $asArray   Значение для вызова count()
     * @param   int     $ifError   Что вернет функция, если подсчет невозможен
     *
     * @return int
     */
    public static function countSafe($asArray, int $ifError = 0): int
    {
        if (is_array($asArray) || $asArray instanceof \Countable) return count($asArray);

        return $ifError;
    }

    /**
     * Проверит, существует ли в массиве ключ
     * (Также работает с массивоподобными объектами)
     *
     * @param   array|(\ArrayAccess&\Traversable)   $asArray               Массив или массивоподобный объект
     * @param   mixed                               $key                   Ключ
     * @param   bool                                $realSearchForObject   Как будет проверяться объекты:
     *                                                                     <br>FALSE: с помощью isset()
     *                                                                     <br>TRUE: с помощью перебора элементов (может серьезно замедлить проверку)
     *
     * @return  bool
     */
    public static function keyExist($asArray, $key, bool $realSearchForObject = false): bool
    {
        // @todo PHP8.2 условие с !ArrayHelper::isAsArray теряет смысл
        if (!self::isAsArray($asArray, false)) throw new \TypeError('$array can be array or array-object, call is a ' .gettype($asArray));

        if (is_array($asArray)) return array_key_exists($key, $asArray);

        if (self::countSafe($asArray, 1) === 0) return false;
        elseif (isset($asArray[$key])) return true;
        elseif (!$realSearchForObject) return false;

        foreach ($asArray as $index => $value)
        {
            if ($key == $index) return true;
        }

        return false;
    }

    /**
     * Вернет числовой индекс, который будет присвоен новому элементу массива
     *
     * Может вернуть 0 (для пустого массива), значение функции count(), в случае, если в массиве есть значения, или иное
     * число, если индекс с указанным номером уже существует
     *
     * @param   array|(\Countable&\ArrayAccess&\Traversable)   $asArray
     *
     * @return  int
     *
     * @todo PHP8 Типизация для аргументов
     * @todo PHP8.2 Типизация для аргументов
     */
    public static function getNewIndex($asArray): int
    {
        // @todo PHP8.2 условие с !ArrayHelper::isAsArray теряет смысл
        if (!ArrayHelper::isAsArray($asArray)) throw new \TypeError('$array can be array or array-object, call is a ' .gettype($asArray));

        if (count($asArray) === 0) return 0;

        $resultIndex = count($asArray);
        if (self::keyExist($asArray, $resultIndex))
        {
            while (self::keyExist($asArray, $resultIndex));
        }

        return $resultIndex;
    }

    /**
     * Вставит в указанную позицию массива один или несколько элементов (сдвинув остальные элементы массива)
     *
     * - ВНИМАНИЕ
     * <br>(!) Сдвигаемые элементы, поменяют свои числовые индексы
     * <br>(!) Работает только с массивами, не поддерживает объекты похоже на массивы. Это связано с тем, что массивоподобные
     * структуры могут иметь сложную логику связи индекса и значения, из-за чего "сдвиг" может привести к непредвиденным последствиям
     *
     * @param   array     $array      Массив, или объект похожий на массив
     * @param   int       $position   Позиция для вставки (если отрицательное число, позиция будет найден с конца массива)
     * @param   mixed[]   $values     Значения для вставки
     *
     * @return  array
     */
    public static function setInPositionAndMoveOldValues(array $array, int $position, ... $values): array
    {
        if (count($array) === 0) return $values;
        if (count($values) === 0) return $array;

        if ($position < 0) $position = count($array) + $position;

        // * * * Если длина массива меньше указанной позиции - вставим в конец массива

        if (count($array) <= $position)
        {
            foreach ($values as $value) $array[] = $value;
            return $array;
        }

        // * * * Если вставка будет проводиться внутрь существующего массива

        // срез массива "до" и "после" позиции
        $arrayAfter = array_slice($array, $position);
        // срез массива "до" позиции
        $arrayResult = array_slice($array, 0, $position);

        // новые значения
        foreach ($values as $value) $arrayResult[] = $value;
        // срез массива "после" позиции
        foreach ($arrayAfter as $key => $value)
        {
            if (is_int($key) || (string)(int)$key === $key) $arrayResult[] = $value;
            else $arrayResult[$key] = $value;
        }

        return $arrayResult;
    }

    /**
     * Вернет срез массива, по списку указанных индексов массива
     *
     * @param   array|\ArrayAccess   $array      Массив, или объект допускающий доступ к себе, как к массиву
     * @param   array                $indexes    Список индексов для возврата
     * @param   mixed                $default    Значение по умолчанию, если элемент не был найден
     *
     * @return  array
     *
     * @todo PHP8 типизация аргументов
     */
    public static function getByIndexes($array, array $indexes, $default = null): array
    {
        $_return = [];

        foreach ($indexes as $keyName) $_return[$keyName] = $array[$keyName] ?? $default;

        return $_return;
    }
}
