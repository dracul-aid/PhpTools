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

use DraculAid\PhpTools\Arrays\ArrayHelper;
use DraculAid\PhpTools\Arrays\Objects\Interfaces\ArrayInterface;
use DraculAid\PhpTools\tests\Classes\ClassToolsTest;

/**
 * Статический класс с функциями, для работы с классами
 *
 * Оглавление:
 * <br>{@see ClassTools::createObject()} - Создаст объект и установит в него свойства
 * <br>{@see ClassTools::isLoad()} - Проверит, данное имя является загруженным классом, трейтом, перечислением или интерфейсом
 * <br>{@see ClassTools::isInternal()} - Проверит, является ли указанный класс встроенным в PHP классом
 * <br>{@see ClassTools::isAsArray()} - Проверит, реализует ли класс (объект) полный доступ "как к массиву"
 * <br>--- Имя класса и его пространство имен
 * <br>{@see ClassTools::getNamespace()} - Вернет пространство имен класса
 * <br>{@see ClassTools::getNameWithoutNamespace()} - Вернет имя класса, без пространства имен
 * <br>{@see ClassTools::getNameAndNamespace()} - Вернет имя класса и его пространство имен
 *
 * Test cases for class {@see ClassToolsTest}
 */
final class ClassTools
{
    /**
     * Создаст объект указанного класса и установит для него свойства.
     *
     * <br>+ Создаст объект, возможно без вызова конструктора
     * <br>+ Может выполнить конструктор, даже если он private или protected
     * <br>+ Установит свойства, даже если они private или protected
     *
     * @param   class-string   $class         Полное имя класса
     * @param   false|array    $arguments     Массив аргументов для конструктора:
     *                                        <br>* FALSE: конструктор не будет вызван
     *                                        <br>* array: список аргументов для конструктора
     * @param   array          $properties    Массив свойств необходимых для установки в объекте
     *
     * @return  object  Вернет созданный объект
     *
     * @psalm-template RealInstanceType of object
     * @psalm-param class-string<RealInstanceType> $class
     * @psalm-return RealInstanceType
     * @psalm-suppress InvalidReturnType Псалм запутывается в ожидаемом типе возвращаемого значения (а вот Шторм - нет)
     * @psalm-suppress InvalidReturnStatement Псалм запутывается в ожидаемом типе возвращаемого значения (а вот Шторм - нет)
     */
    public static function createObject(string $class, false|array $arguments = false, array $properties = []): object
    {
        $reflectionClass = new \ReflectionClass($class);
        $object = $reflectionClass->newInstanceWithoutConstructor();
        $objectNotPublicManager = ClassNotPublicManager::getInstanceFor($object);

        if ($arguments !== false) $objectNotPublicManager->call('__construct', $arguments);
        if (count($properties) > 0) $objectNotPublicManager->set($properties);

        return $object;
    }

    /**
     * Проверит, данное имя является загруженным классом, трейтом, перечислением или интерфейсом
     * (т.е. загружен класс или нет)
     *
     * @param   class-string   $className   Имя класса любого типа
     *
     * @return  bool
     */
    public static function isLoad(string $className): bool
    {
        // Проверка enum_exists($className, false) не проводится, так как class_exists() проверяет и обычные классы и перечисления
        return class_exists($className, false)
            || interface_exists($className, false)
            || trait_exists($className, false);
    }

    /**
     * Проверит, реализует ли класс (объект) полный доступ "как к массиву"
     * (т.е. перебор в foreach, получение элементов "как в массивах" и кол-ва элементов через count())
     *
     * @see ArrayHelper::isAsArray() Проверит, является ли объект похожим на массив
     * @see ArrayInterface Интерфейс для объектов, схожих с массивами
     *
     * @param   class-string|object   $classOrObject   Класс или объект для проверки
     * @param   bool                  $countable       Должно ли переданное значение корректно отрабатываться функцией {@see count()}
     * 
     * @return  bool
     */
    public static function isAsArray(string|object $classOrObject, bool $countable = true): bool
    {
        if (is_subclass_of($classOrObject, \Traversable::class) === false || is_subclass_of($classOrObject, \ArrayAccess::class) === false) return false;

        if ($countable) return is_subclass_of($classOrObject, \Countable::class);
        return true;
    }

    /**
     * Проверит, является ли указанный класс встроенным в PHP классом
     *
     * @param   string   $className   Имя класса любого типа
     *
     * @return  bool
     *
     * @throws  \ReflectionException   Если не удалось получить рефлексию для класса
     *
     * @psalm-param class-string|trait-string $className
     */
    public static function isInternal(string $className): bool
    {
        if (!self::isLoad($className)) return false;

        return (new \ReflectionClass($className))->isInternal();
    }

    /**
     * Вернет пространство имен класса
     *
     * @param   string   $class   Полное имя класса
     *
     * @return  string   Вернет пространство имен класса, если это "глобальное" пространство имен - вернет пустую строку
     */
    public static function getNamespace(string $class): string
    {
        $position = strrpos($class, '\\');

        if ($position === false) return '';
        else return substr($class, 0, $position);
    }

    /**
     * Вернет имя класса, без пространства имен
     *
     * @param   string   $class   Полное имя класса
     *
     * @return  string
     */
    public static function getNameWithoutNamespace(string $class): string
    {
        $position = strrpos($class, '\\');

        if ($position === false) return $class;
        else return substr($class, $position + 1);
    }

    /**
     * Вернет имя класса и пространство имен
     *
     * @param   string        $class       Полное имя класса
     *
     * @return  string[] Вернет массив, 0-ой элемент "пространство имен", 1-ый "Имя класса"
     */
    public static function getNameAndNamespace(string $class): array
    {
        $position = strrpos($class, '\\');

        if ($position === false)
        {
            return ['', $class];
        }
        else
        {
            return [
                substr($class, 0, $position), // получение пространства имен
                substr($class, $position + 1), // получение имени класса
            ];
        }
    }
}
