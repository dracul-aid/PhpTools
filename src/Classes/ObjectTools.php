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
     *
     * @todo PHP8 типизация аргументов
     */
    public static function propertiesFor(object $object, bool $intKey = false, ?int $filter = \ReflectionProperty::IS_PUBLIC): \Generator
    {
        $objectReader = ClassNotPublicManager::getInstanceFor($object);

        $reflectionClass = new \ReflectionObject($object);

        /**
         * @todo PHP8 При переходе на 8-ку от $propertyList можно будет избавиться, так как `getProperties()` сможет принимать NULL
         * @psalm-suppress ArgumentTypeCoercion Мы специально намутили проверку, что бы $filter не был NULL, псалм просто тупой(
         */
        $propertyList = $filter === null ? $reflectionClass->getProperties() : $reflectionClass->getProperties($filter);
        foreach ($propertyList as $position => $property)
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
     *
     * @todo PHP8 типизация аргументов
     */
    public static function toArray(object $object, bool $intKey = false, ?int $filter = \ReflectionProperty::IS_PUBLIC): array
    {
        return iterator_to_array(self::propertiesFor($object, $intKey, $filter));
    }
}
