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
}
