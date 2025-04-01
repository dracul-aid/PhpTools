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

use DraculAid\Php8forPhp7\LoaderPhp8Lib;
use DraculAid\Php8forPhp7\TypeValidator;
use DraculAid\PhpTools\tests\Classes\ClassNotPublicManagerTest;

/**
 * Подключение имитации {@see \WeakMap}
 *
 * @todo PHP8 убрать имитацию
 */
LoaderPhp8Lib::loadWeakMap();

/**
 * Класс для работы с непубличными элементами классов и объектов
 *
 * Оглавление:
 * <br>{@see ClassNotPublicManager::getInstanceFor()} - Вернет объект для работы с не публичными элементами
 * <br>--- "Процедурный стиль"
 * <br>{@see ClassNotPublicManager::readConstant()} - Чтение значения константы
 * <br>{@see ClassNotPublicManager::readProperty()} - Чтение значения свойства
 * <br>{@see ClassNotPublicManager::writeProperty()} - Запись значения свойства (списка свойств)
 * <br>{@see ClassNotPublicManager::callMethod()} - Вызов метода
 * <br>--- Объект для взаимодействия с непубличными элементами
 * <br>{@see self::$toObject} [const] - Для какого объекта создан объект
 * <br>{@see self::constant()} - Вернет значение указанной константы
 * <br>{@see self::get()} - Чтение статического свойства
 * <br>{@see self::getStatic()} - Чтение статического свойства
 * <br>{@see self::set()} - Установка свойства (или списка свойств)
 * <br>{@see self::setStatic()} - Установка статического свойства (или списка свойств)
 * <br>{@see self::call()} - Вызов метода
 * <br>{@see self::callStatic()} - Вызов статического метода
 *
 * Test cases for class {@see ClassNotPublicManagerTest}
 *
 * @todo PHP8 В коде много мест с get_class($this), можно поменять на $this::class
 */
final class ClassNotPublicManager
{
    /**
     * Массив объектов, для которых создан объект для взаимодействия с непубличными элементами
     * (ключи - объекты, значения - созданный для указанного объекта менеджер)
     *
     * Используется для реализации "синглтона", см {@see ClassNotPublicManager::getInstanceFor()}
     *
     * @var \WeakMap<object,ClassNotPublicManager>
     */
    public static \WeakMap $_notPublicObjects;

    /**
     * Массив классов, для которых создан объект для взаимодействия с непубличными элементами
     * (ключ массива - имена классов, значения - менеджеры для указанного класса)
     *
     * Используется для реализации "синглтона", см {@see ClassNotPublicManager::getInstanceFor()}
     *
     * @var array<string, ClassNotPublicManager>
     * @psalm-param array<class-string, ClassNotPublicManager>
     */
    public static array $_notPublicClasses = [];

    /**
     * Для какого объекта создан "объект для взаимодействия с непубличными элементами"
     *
     * @todo PHP8 readonly
     * @readonly
     */
    public object $toObject;

    /**
     * Массив с "функциями взаимодействия с классами", в качестве ключей выступают имена "методов-генератов" этих функций
     * @var array<string, \Closure>
     *
     * @todo Вынести в отдельный класс, вместе с методами-генераторами функций
     */
    private array $closureForObjects = [];

    /**
     *  Создание объектов должно проводиться с помощью {@see ClassNotPublicManager::getInstanceFor()}
     *
     * @param   object   $toObject   Для какого объекта создан "объект для взаимодействия с непубличными элементами"
     */
    private function __construct(object $toObject)
    {
        $this->toObject = $toObject;
    }

    /**
     * Вернет объект для взаимодействия с непубличными элементами класса и объекта
     *
     * @param   string|object   $objectOrClass   Для какого класса или объекта создается
     *
     * @return  static
     *
     * @todo PHP8 Типизация аргументов
     *
     * @psalm-param class-string|object $objectOrClass
     */
    public static function getInstanceFor($objectOrClass): self
    {
        TypeValidator::validateOr($objectOrClass, ['string', 'object']);

        /** @psalm-suppress RedundantPropertyInitializationCheck Псалм реально не знает, что это рабочая схема (она не требует расширения типов для свойства) */
        if (!isset(self::$_notPublicObjects)) self::$_notPublicObjects = new \WeakMap();

        // * * *

        // Создание менеджера для объекта
        if (is_object($objectOrClass))
        {
            if (empty(self::$_notPublicObjects[$objectOrClass]))
            {
                self::$_notPublicObjects[$objectOrClass] = new self($objectOrClass);
            }

            return self::$_notPublicObjects[$objectOrClass];
        }
        // Создание менеджера для класса (т.е. для взаимодействия со статическими элементами)
        else
        {
            if (empty(self::$_notPublicClasses[$objectOrClass]))
            {
                self::$_notPublicClasses[$objectOrClass] = new self(ClassTools::createObject($objectOrClass, false));
            }

            return self::$_notPublicClasses[$objectOrClass];
        }
    }

    /**
     * Вернет значение константы
     *
     * @param   string|object   $objectOrClass   Класс или объект, из которого будет проводиться чтение
     * @param   string          $name            Имя константы
     * @param   class-string    $classContext    Если чтение нужно произвести из конкретной области видимости (т.е. из родителя)
     *
     * @return  mixed
     *
     * @todo PHP8 Типизация аргументов и возврата функции
     *
     * @psalm-param class-string|object $objectOrClass
     * @psalm-param string|empty  $classContext
     */
    public static function readConstant($objectOrClass, string $name, string $classContext = '')
    {
        TypeValidator::validateOr($objectOrClass, ['string', 'object']);

        return self::getInstanceFor($objectOrClass)->constant($name, $classContext);
    }

    /**
     * Вернет значение указанной константы
     *
     * @param   string          $name            Имя константы
     * @param   class-string    $classContext    Если чтение нужно произвести из конкретной области видимости (т.е. из родителя)
     *
     * @return  mixed
     *
     * @todo PHP8 Типизация возврата функции
     *
     * @psalm-param string|empty $classContext
     */
    public function constant(string $name, string $classContext = '')
    {
        $readFunction = $this->getOrCreateFunctionForConstants();

        if ($classContext === '' || get_class($this->toObject) === $classContext) return $readFunction($name);

        /** @psalm-suppress PossiblyNullFunctionCall Если не удастся сменить область видимости то пусть падает TypeError, так и должно быть */
        return ($readFunction->bindTo($this->toObject, $classContext))($name, $classContext);
    }

    /**
     * Прочитает значение свойства объекта или статического свойства класса
     *
     * @param   string|object   $objectOrClass    Строка с именем класса (для чтения статических свойств) или объект (для чтения свойств объекта)
     * @param   string          $name             Имя свойства
     * @param   class-string    $classContext     Если чтение нужно произвести из конкретной области видимости (т.е. из родителя)
     *
     * @return  mixed
     *
     * @todo PHP8 Типизация аргументов и возврата функции
     *
     * @psalm-param class-string|object $objectOrClass
     * @psalm-param string|empty  $classContext
     */
    public static function readProperty($objectOrClass, string $name, string $classContext = '')
    {
        TypeValidator::validateOr($objectOrClass, ['string', 'object']);

        if (is_object($objectOrClass)) return self::getInstanceFor($objectOrClass)->get($name, $classContext);
        else return self::getInstanceFor($objectOrClass)->getStatic($name, $classContext);
    }

    /**
     * Вернет значение указанного свойства объекта
     *
     * @param   string         $name           Имя свойства
     * @param   class-string   $classContext   Если чтение нужно произвести из конкретной области видимости (т.е. из родителя)
     *
     * @return  mixed
     *
     * @todo PHP8 Типизация возврата функции
     *
     * @psalm-param string|empty $classContext
     */
    public function get(string $name, string $classContext = '')
    {
        $readFunction = $this->getOrCreateFunctionForGetProperties();

        if ($classContext === '' || get_class($this->toObject) === $classContext) return $readFunction($name);

        /** @psalm-suppress PossiblyNullFunctionCall Если не удастся сменить область видимости то пусть падает TypeError, так и должно быть */
        return ($readFunction->bindTo($this->toObject, $classContext))($name);
    }

    /**
     * Вернет значение указанного статического свойства
     *
     * @param   string         $name           Имя свойства
     * @param   class-string   $classContext   Если чтение нужно произвести из конкретной области видимости (т.е. из родителя)
     *
     * @return  mixed
     *
     * @todo PHP8 Типизация возврата функции
     *
     * @psalm-param string|empty $classContext
     */
    public function getStatic(string $name, string $classContext = '')
    {
        $readFunction = $this->getOrCreateFunctionForGetStaticProperties();

        if ($classContext === '' || get_class($this->toObject) === $classContext) return $readFunction($name);

        /** @psalm-suppress PossiblyNullFunctionCall Если не удастся сменить область видимости то пусть падает TypeError, так и должно быть */
        return ($readFunction->bindTo($this->toObject, $classContext))($name, $classContext);
    }

    /**
     * Прочитает значение свойства объекта или статического свойства класса
     *
     * Если $name передан как массив и указана конкретная область видимости - будет выброшена ошибка
     *
     * @param   string|object   $objectOrClass    Строка с именем класса (для чтения статических свойств) или объект (для чтения свойств объекта)
     * @param   string|array    $name             Имя свойства или массив со списком свойств (имя свойства => значение)
     * @param   mixed           $data             Значение для установки
     * @param   class-string    $classContext     Если чтение нужно произвести из конкретной области видимости (т.е. из родителя)
     *
     * @return  $this
     *
     * @throws  \LogicException Если $var передан как массив и указана конкретная область видимости
     *
     * @todo PHP8 Типизация аргументов и возврата функции
     *
     * @psalm-param class-string|object $objectOrClass
     * @psalm-param string|empty  $classContext
     */
    public static function writeProperty($objectOrClass, $name, $data = null, string $classContext = '')
    {
        TypeValidator::validateOr($objectOrClass, ['string', 'object']);
        TypeValidator::validateOr($name, ['string', 'array']);

        if (is_array($name) && $classContext !== '') throw new \LogicException('Cannot pass $classContext if $var is an array');

        if (is_object($objectOrClass)) return self::getInstanceFor($objectOrClass)->set($name, $data, $classContext);
        else return self::getInstanceFor($objectOrClass)->setStatic($name, $data, $classContext);
    }

    /**
     * Установит значение указанному свойству объекта
     *
     * Если $var передан как массив и указана конкретная область видимости - будет выброшена ошибка
     *
     * @param   string|array   $var            Имя свойства или массив со списком свойств (имя свойства => значение)
     * @param   mixed          $data           Значение для установки
     * @param   class-string   $classContext   Если чтение нужно произвести из конкретной области видимости (т.е. из родителя)
     *
     * @return  $this
     *
     * @throws  \LogicException Если $var передан как массив и указана конкретная область видимости
     *
     * @todo PHP8 Типизация аргументов функции
     *
     * @psalm-param string|empty $classContext
     *
     * @psalm-suppress PossiblyNullFunctionCall Если не удастся сменить область видимости то пусть падает TypeError, так и должно быть
     */
    public function set($var, $data = null, string $classContext = ''): self
    {
        TypeValidator::validateOr($var, ['string', 'array']);

        if (is_array($var) && $classContext !== '') throw new \LogicException('Cannot pass $classContext if $var is an array');

        $readFunction = $this->getOrCreateFunctionForSetProperties();
        if ($classContext !== '' && get_class($this->toObject) !== $classContext) $readFunction = $readFunction->bindTo($this->toObject, $classContext);

        if (is_string($var)) $readFunction($var, $data);
        else foreach ($var as $name => $data) $readFunction($name, $data);

        return $this;
    }

    /**
     * Установит значение статическому свойству
     *
     * Если $var передан как массив и указана конкретная область видимости - будет выброшена ошибка
     *
     * @param   string|array   $var            Имя свойства или массив со списком свойств (имя свойства => значение)
     * @param   mixed          $data           Значение для установки
     * @param   class-string   $classContext   Если чтение нужно произвести из конкретной области видимости (т.е. из родителя)
     *
     * @return  $this
     *
     * @throws  \LogicException Если $var передан как массив и указана конкретная область видимости
     *
     * @todo PHP8 Типизация аргументов функции
     *
     * @psalm-param string|empty $classContext
     *
     * @psalm-suppress PossiblyNullFunctionCall Если не удастся сменить область видимости то пусть падает TypeError, так и должно быть
     */
    public function setStatic($var, $data = null, string $classContext = ''): self
    {
        TypeValidator::validateOr($var, ['string', 'array']);

        if (is_array($var) && $classContext !== '') throw new \LogicException('Cannot pass $classContext if $var is an array');

        $readFunction = $this->getOrCreateFunctionForSetStaticProperties();
        if ($classContext !== '' && get_class($this->toObject) !== $classContext) $readFunction = $readFunction->bindTo($this->toObject, $classContext);

        if (is_string($var)) $readFunction($var, $data, $classContext);
        else foreach ($var as $name => $data) $readFunction($name, $data, $classContext);

        return $this;
    }

    /**
     * Вызов метода
     *
     * @param   array          $methodAsArray   Вызываемый метод в формате массива [$objectOrClass, $method]
     * @param   array          $arguments       Список аргументов
     * @param   class-string   $classContext    Если чтение нужно произвести из конкретной области видимости (т.е. из родителя)
     *
     * @return  mixed
     *
     * @todo PHP8 Типизация возврата функции
     *
     * @psalm-param callable-array $methodAsArray
     * @psalm-param string|empty $classContext
     *
     * @psalm-suppress PossiblyNullFunctionCall Если не удастся сменить область видимости то пусть падает TypeError, так и должно быть
     */
    public static function callMethod(array $methodAsArray, array $arguments = [], string $classContext = '')
    {
        if (!self::isCallable($methodAsArray))
        {
            throw new \TypeError('$methodAsArray can be callable, see format: [$objectOrClass, $method]');
        }

        if (is_object($methodAsArray[0])) return self::getInstanceFor($methodAsArray[0])->call($methodAsArray[1], $arguments, $classContext);
        else return self::getInstanceFor($methodAsArray[0])->callStatic($methodAsArray[1], $arguments, $classContext);
    }

    /**
     * Проведет вызов метода
     *
     * @param   string         $name           Имя метода
     * @param   mixed          $arguments      Список аргументов
     * @param   class-string   $classContext   Если чтение нужно произвести из конкретной области видимости (т.е. из родителя)
     *
     * @return  mixed
     *
     * @todo PHP8 Типизация возврата функции
     *
     * @psalm-param string|empty $classContext
     */
    public function call(string $name, array $arguments = [], string $classContext = '')
    {
        $callFunction = $this->getOrCreateFunctionForCall();

        if ($classContext !== '' && get_class($this->toObject) !== $classContext) $callFunction = $callFunction->bindTo($this->toObject, $classContext);

        /** @psalm-suppress PossiblyNullFunctionCall Если не удастся сменить область видимости то пусть падает TypeError, так и должно быть */
        return $callFunction($name, $arguments);
    }

    /**
     * Проведет вызов статического метода
     *
     * @param   string         $name           Имя статического метода
     * @param   mixed          $arguments      Список аргументов
     * @param   class-string   $classContext   Если чтение нужно произвести из конкретной области видимости (т.е. из родителя)
     *
     * @return  mixed
     *
     * @todo PHP8 Типизация возврата функции
     *
     * @psalm-param string|empty $classContext
     */
    public function callStatic(string $name, array $arguments = [], string $classContext = '')
    {
        $callFunction = $this->getOrCreateFunctionForCallStatic();

        if ($classContext !== '' && get_class($this->toObject) !== $classContext) $callFunction = $callFunction->bindTo($this->toObject, $classContext);

        /** @psalm-suppress PossiblyNullFunctionCall Если не удастся сменить область видимости то пусть падает TypeError, так и должно быть */
        return $callFunction($name, $arguments, $classContext);
    }

    /**
     * Создание (если надо) функции, для чтения значения константы
     *
     * @return   \Closure
     */
    private function getOrCreateFunctionForConstants(): \Closure
    {
        if (empty($this->closureForObjects[__FUNCTION__]))
        {
            $this->closureForObjects[__FUNCTION__] = function(string $name, $context = '') {
                return constant(($context ? $context : get_class($this)) . "::{$name}");
            };
            $this->closureForObjects[__FUNCTION__] = $this->closureForObjects[__FUNCTION__]->bindTo($this->toObject, $this->toObject);
        }

        return $this->closureForObjects[__FUNCTION__];
    }

    /**
     * Создание (если надо) функции, для чтения значения свойства
     *
     * @return   \Closure
     */
    private function getOrCreateFunctionForGetProperties(): \Closure
    {
        if (empty($this->closureForObjects[__FUNCTION__]))
        {
            $this->closureForObjects[__FUNCTION__] = function(string $name) {
                return $this->{$name};
            };
            $this->closureForObjects[__FUNCTION__] = $this->closureForObjects[__FUNCTION__]->bindTo($this->toObject, $this->toObject);
        }

        return $this->closureForObjects[__FUNCTION__];
    }

    /**
     * Создание (если надо) функции, для чтения значения статического свойства
     *
     * @return   \Closure
     */
    private function getOrCreateFunctionForGetStaticProperties(): \Closure
    {
        if (empty($this->closureForObjects[__FUNCTION__]))
        {
            $this->closureForObjects[__FUNCTION__] = function(string $name, string $classContext = '') {
                return ($classContext ? $classContext : get_class($this))::$$name;
            };
            $this->closureForObjects[__FUNCTION__] = $this->closureForObjects[__FUNCTION__]->bindTo($this->toObject, $this->toObject);
        }

        return $this->closureForObjects[__FUNCTION__];
    }

    /**
     * Создание (если надо) функции, для установки значения свойства
     *
     * @return   \Closure   Вернет функцию для
     */
    private function getOrCreateFunctionForSetProperties(): \Closure
    {
        if (empty($this->closureForObjects[__FUNCTION__]))
        {
            // @todo PHP8 типизация аргументов анонимной функции
            $this->closureForObjects[__FUNCTION__] = function(string $name, $data) {
                $this->{$name} = $data;
            };
            $this->closureForObjects[__FUNCTION__] = $this->closureForObjects[__FUNCTION__]->bindTo($this->toObject, $this->toObject);
        }

        return $this->closureForObjects[__FUNCTION__];
    }

    /**
     * Создание (если надо) функции, для установки значения статического свойства
     *
     * @return   \Closure   Вернет функцию для
     *
     * @psalm-suppress UnusedClosureParam PSALM считает, что $classContext никогда не будет передан и использован в анонимной функции (как он ошибается, лол)
     */
    private function getOrCreateFunctionForSetStaticProperties(): \Closure
    {
        if (empty($this->closureForObjects[__FUNCTION__]))
        {
            // @todo PHP8 типизация аргументов анонимной функции
            $this->closureForObjects[__FUNCTION__] = function(string $name, $data, string $classContext) {
                ($classContext ? $classContext : get_class($this))::$$name = $data;
            };
            $this->closureForObjects[__FUNCTION__] = $this->closureForObjects[__FUNCTION__]->bindTo($this->toObject, $this->toObject);
        }

        return $this->closureForObjects[__FUNCTION__];
    }

    /**
     * Создание (если надо) функции, для вызова метода
     *
     * @return   \Closure   Вернет функцию для
     */
    private function getOrCreateFunctionForCall(): \Closure
    {
        if (empty($this->closureForObjects[__FUNCTION__]))
        {
            $this->closureForObjects[__FUNCTION__] = function(string $name, array $arguments) {
                return $this->{$name}(...$arguments);
            };
            $this->closureForObjects[__FUNCTION__] = $this->closureForObjects[__FUNCTION__]->bindTo($this->toObject, $this->toObject);
        }

        return $this->closureForObjects[__FUNCTION__];
    }

    /**
     * Создание (если надо) функции, для вызова статического метода
     *
     * @return   \Closure   Вернет функцию для
     */
    private function getOrCreateFunctionForCallStatic(): \Closure
    {
        if (empty($this->closureForObjects[__FUNCTION__]))
        {
            $this->closureForObjects[__FUNCTION__] = function(string $name, array $arguments, string $classContext) {
                return [$classContext ? $classContext : get_class($this), $name](...$arguments);
            };
            $this->closureForObjects[__FUNCTION__] = $this->closureForObjects[__FUNCTION__]->bindTo($this->toObject, $this->toObject);
        }

        return $this->closureForObjects[__FUNCTION__];
    }

    /**
     * Проверит, переданный массив является callable массивом или нет, в отличие от {@see is_callable()} считает корректным
     * если переданный метод является protected или private
     *
     * @param   array   $methodAsArray   Вызываемый метод в формате массива [$objectOrClass, $method]
     *
     * @return  bool
     */
    private static function isCallable(array $methodAsArray): bool
    {
        return count($methodAsArray) === 2
            && is_string($methodAsArray[1])
            && (is_object($methodAsArray[0]) || is_string($methodAsArray[0]));
    }
}
