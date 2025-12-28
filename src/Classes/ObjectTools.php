<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\Classes;

use DraculAid\PhpTools\tests\Classes\ObjectToolsTest;

/**
 * Статический класс для работы с объектами
 *
 * Оглавление:
 * <br>{@see ObjectTools::propertiesFor()} Генератор для перебора свойств объекта (именно свойства, даже если реализован {@see \Traversable})
 * <br>{@see ObjectTools::toArray()} Вернет массив свойств объекта (именно свойства, даже если реализован {@see \Traversable})
 * <br>{@see ObjectTools::getStringNewInstance()} Вернет строку создания нового экземпляра класса, используется, когда нужно динамически создать PHP код с созданием объекта
 *
 * Test cases for class {@see ObjectToolsTest}
 */
final class ObjectTools
{
    /**
     * Генератор для перебора свойств объекта
     * (перебирает свойства, даже если у объекта есть один из интерфейсов {@see \Traversable})
     *
     * @param   object     $object    Объект, свойства которого будет произведен перебор
     * @param   bool       $intKey    TRUE для числовых ключей
     * @param   null|int   $filter    Тип выбираемых свойств (битовая маска):
     *                                * NULL: все свойства
     *                                * {@see \ReflectionProperty::IS_PUBLIC}: Только публичные свойства
     *                                * {@see \ReflectionProperty::IS_PROTECTED}: Только protected свойства
     *                                * {@see \ReflectionProperty::IS_PRIVATE}: Только private свойства
     *
     * @return  \Generator
     */
    public static function propertiesFor(object $object, bool $intKey = false, null|int $filter = \ReflectionProperty::IS_PUBLIC): \Generator
    {
        $objectReader = ClassNotPublicManager::getInstanceFor($object);

        $reflectionClass = new \ReflectionObject($object);

        foreach ($reflectionClass->getProperties($filter) as $position => $property)
        {
            if ($property->isStatic()) continue;

            yield $intKey ? $position : $property->getName() => $objectReader->get($property->getName());
        }
    }

    /**
     * Вернет массив свойств объекта
     * (перебирает свойства, даже если у объекта есть один из интерфейсов {@see \Traversable})
     *
     * @param   object     $object    Объект, свойства которого будет произведен перебор
     * @param   bool       $intKey    TRUE для числовых ключей
     * @param   null|int   $filter    Тип выбираемых свойств (битовая маска):
     *                                * NULL: все свойства
     *                                * {@see \ReflectionProperty::IS_PUBLIC}: Только публичные свойства
     *                                * {@see \ReflectionProperty::IS_PROTECTED}: Только protected свойства
     *                                * {@see \ReflectionProperty::IS_PRIVATE}: Только private свойства
     *
     * @return  array
     */
    public static function toArray(object $object, bool $intKey = false, null|int $filter = \ReflectionProperty::IS_PUBLIC): array
    {
        return iterator_to_array(self::propertiesFor($object, $intKey, $filter));
    }

    /**
     * Вернет строку создания нового экземпляра класса, используется, когда нужно динамически создать PHP код с созданием объекта
     *
     * (!) Вставка аргументов в строку происходит с помощью функции {@see var_export()}
     *
     * Пример аргументов ввиде списка
     * ```php
     * $arguments = ['first', 222, true];
     * echo ObjectTools::getStringNewInstance(SomeClass::class, $arguments);
     * // Выведет:
     * // SomeClass::new('first', 222, true)
     * ```
     *
     * Пример аргументов ввиде ассоциативного массива
     * ```php
     * $arguments = ['name' => 'first', 'count' => 222, 'isActive' => true];
     * echo ObjectTools::getStringNewInstance(SomeClass::class, $arguments);
     * // Выведет:
     * // SomeClass::new(name: 'first', count: 222, isActive: true)
     * ```
     *
     * Пример аргументов ввиде смешанного массива
     * ```php
     * $arguments = ['name' => 'first', 222, 'isActive' => true, false];
     * echo ObjectTools::getStringNewInstance(SomeClass::class, $arguments);
     * // Выведет:
     * // SomeClass::new(name: 'first', 222, isActive: true, false)
     * ```
     *
     * @param   string     $class                  Полное имя класса
     * @param   iterable   $constructorArguments   Массив с данными для конструктора.
     *                                             <br>* Если передан список - аргументы будут переданы в порядке, указанном в массиве.
     *                                             <br>* Если передан ассоциативный массив cо ключами-строками - аргументы будут переданы по именам.
     *                                             <br>* Если передан ассоциативный массив cо строковыми и числовыми ключами - будет выброшено исключение
     * @param   bool         $argumentsAsList      TRUE - если аргументы переданы списком, FALSE - если функция сама определит, как были переданы аргументы
     *
     * @return  string    Вернет готовый к использованию PHP код создания нового экземпляра класса
     * @throws \InvalidArgumentException Если аргументы конструктора переданы ввиде ассоциативного массива, в котором есть не строковые ключи
     */
    public static function getStringNewInstance(string $class, iterable $constructorArguments, bool $argumentsAsList = false): string
    {
        /** Аргументы для создания PHP кода создания объекта */
        $argumentsArray = [];

        $asList = true;
        $argumentPosition = -1;
        foreach ($constructorArguments as $index => $item)
        {
            if (is_object($index) && method_exists($index, '__toString'))
            {
                $index = (string)$index;
            }

            if ($argumentsAsList || (is_int($index) && $asList))
            {
                $argumentsArray[] = var_export($item, true);
            }
            elseif (is_string($index))
            {
                $asList = false;
                $argumentsArray[] = "{$index}: " . var_export($item, true);
            }
            else
            {
                throw new \InvalidArgumentException("Arguments for constructor must be an associative array with string keys or a list with integer keys, found argument #{$argumentPosition} with type: " . gettype($index));
            }

            $argumentPosition++;
        }

        return "new \\{$class}(" . implode(', ', $argumentsArray) . ')';
    }
}
