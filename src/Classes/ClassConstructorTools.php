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

/**
 * Инструменты, для облегчения работы с конструкторами классов (объектов)
 *
 * Оглавление:
 * <br>{@see ClassConstructorTools::getPublicProperties()} - Вернет массив публичных свойств, которые были объявлены в конструкторе класса
 * <br>{@see ClassConstructorTools::isHasOnlyPublicProperties()} - Проверяет, содержит ли конструктор класса только публичные свойства
 *
 * См также:
 * <br>{@see ClassTools::createObject()} Облегчает создание объектов, позволяя создавать объекты без вызова конструктора и(или) установить также и свойства
 */
final class ClassConstructorTools
{
    /**
     * Вернет массив публичных свойств, которые были объявлены в конструкторе класса
     *
     * @param   class-string   $class   Объект для обработки
     *
     * @return  array<int, string>   Массив публичных свойств, которые были объявлены в конструкторе объекта. Ключ - позиция в конструкторе
     * @throws  \ReflectionException Если не удалось получить рефлексию для класса
     */
    public static function getPublicProperties(string $class): array
    {
        $reflection = new \ReflectionClass($class);
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return [];
        }

        $properties = [];
        foreach ($constructor->getParameters() as $position => $parameter) {
            $propertyName = $parameter->getName();
            if ($reflection->hasProperty($propertyName) && $reflection->getProperty($propertyName)->isPublic())
            {
                $properties[$position] = $propertyName;
            }
        }

        return $properties;
    }

    /**
     * Проверяет, имеет ли конструктор класса аргументы
     *
     * @param   string   $class
     *
     * @return  bool
     * @throws  \ReflectionException Если не удалось получить рефлексию для класса
     */
    public static function isHasArguments(string $class): bool
    {
        $reflection = new \ReflectionClass($class);
        $constructor = $reflection->getConstructor();

        return (bool)$constructor?->getNumberOfParameters();
    }

    /**
     * Проверяет, содержит ли конструктор класса только публичные свойства (или вообще не имеет аргументов)
     *
     * @param   class-string   $class   Имя класса для проверки
     *
     * @return  bool TRUE если все параметры конструктора соответствуют только публичным свойствам
     * @throws  \ReflectionException Если не удалось получить рефлексию для класса
     */
    public static function isHasOnlyPublicProperties(string $class): bool
    {
        $reflection = new \ReflectionClass($class);
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return true;
        }

        foreach ($constructor->getParameters() as $parameter) {
            $propertyName = $parameter->getName();
            if (!$reflection->hasProperty($propertyName) || !$reflection->getProperty($propertyName)->isPublic()) {
                return false;
            }
        }

        return true;
    }
}
