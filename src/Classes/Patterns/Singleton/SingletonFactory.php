<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\Classes\Patterns\Singleton;

use DraculAid\Php8forPhp7\TypeValidator;
use DraculAid\PhpTools\Classes\ClassTools;

/**
 * Позволяет работать с любым классом, как с классом-одиночкой
 *
 * Оглавление:
 * <br>{@see SingletonFactory::createObject()} - Создаст объект и установит в него свойства
 * <br>{@see SingletonFactory::createObjectForIndex()} - Создаст уникальный объект для строкового индекса
 * <br>* * *
 * <br>{@see SingletonFactory::$singletonObjects} - Созданные синглтон-объекты
 * <br>{@see SingletonFactory::$uniqKeyObjects} - Созданные синглтон-объекты
 */
final class SingletonFactory
{
    /**
     * Созданные объекты-синглтоны, {@see SingletonFactory::createObject()}
     * (в качестве индексов - имена классов)
     *
     * @var array<string, object>
     */
    public static array $singletonObjects = [];

    /**
     * Созданные объекты в стиле "синглтонов" для строкового ключа, {@see SingletonFactory::createObjectForIndex()}
     * (в качестве индексов - ключи)
     *
     * @var array<string, object>
     */
    public static array $uniqKeyObjects = [];

    /**
     * Создаст объект-синглтон и установит в него свойства
     *
     * <br>+ Создаст объект, возможно без вызова конструктора
     * <br>+ Может выполнить конструктор, даже если он private или protected
     * <br>+ Установит свойства, даже если они private или protected
     *
     * @param   string        $class         Полное имя класса
     * @param   false|array   $arguments     Массив аргументов для конструктора:
     *                                       <br>* FALSE: конструктор не будет вызван
     *                                       <br>* array: список аргументов для конструктора ([] - Вызов без аргументов)
     * @param   array         $properties    Массив свойств необходимых для установки в объекте
     *
     * @return  object  Вернет созданный объект
     *
     * @todo PHP8 Типизация аргументов
     *
     * @psalm-template RealInstanceType of object
     * @psalm-param class-string<RealInstanceType> $class
     * @psalm-param array<string|mixed> $properties
     * @psalm-return RealInstanceType
     */
    public static function createObject(string $class, $arguments = false, array $properties = []): object
    {
        TypeValidator::validateOr($arguments, ['false', 'array']);

        if (empty(self::$singletonObjects[$class]))
        {
            self::$singletonObjects[$class] = ClassTools::createObject($class, $arguments, $properties);
        }

        return self::$singletonObjects[$class];
    }

    /**
     * Создаст объект для уникального ключа и установит в него свойства, если для указанного ключа ранее уже был создан объект - он будет возвращен
     *
     * <br>+ Создаст объект, возможно без вызова конструктора
     * <br>+ Может выполнить конструктор, даже если он private или protected
     * <br>+ Установит свойства, даже если они private или protected
     *
     * @param   string        $index         Идентификатор к которому привязывается объект
     * @param   string        $class         Полное имя класса
     * @param   false|array   $arguments     Массив аргументов для конструктора:
     *                                       <br>* FALSE: конструктор не будет вызван
     *                                       <br>* array: список аргументов для конструктора ([] - Вызов без аргументов)
     * @param   array         $properties    Массив свойств необходимых для установки в объекте
     *
     * @return  object  Вернет созданный объект
     *
     * @todo PHP8 Типизация аргументов
     *
     * @psalm-template RealInstanceType of object
     * @psalm-param class-string<RealInstanceType> $class
     * @psalm-return RealInstanceType
     */
    public static function createObjectForIndex(string $index, string $class, $arguments = false, array $properties = []): object
    {
        TypeValidator::validateOr($arguments, ['false', 'array']);

        if (empty(self::$uniqKeyObjects[$index]))
        {
            self::$uniqKeyObjects[$index] = ClassTools::createObject($class, $arguments, $properties);
        }

        return self::$uniqKeyObjects[$index];
    }
}
