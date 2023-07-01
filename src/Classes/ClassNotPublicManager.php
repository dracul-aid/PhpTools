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
 * <br>{@see ClassNotPublicManager::readProperty()} - Чтение занчения свойства
 * <br>{@see ClassNotPublicManager::writeProperty()} - Запись значния свойства (списка свойств)
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
 */
final class ClassNotPublicManager
{
    /**
     * Массив объектов, для которых создан объект для взаимодействия с непубличными элементами
     * (ключи - объекты, значения - созданный для указанного объекта менеджер)
     *
     * Используется для реализации "синглтона", см  {@see ClassNotPublicManager::getInstanceFor()}
     *
     * @var array<object,ClassNotPublicManager>|\WeakMap
     */
    public static \WeakMap $_notPublicObjects;

    /**
     * Массив классов, для которых создан объект для взаимодействия с непубличными элементами
     * (ключ массива - имена классов, значения - менеджеры для указанного класса)
     *
     * Используется для реализации "синглтона", см  {@see ClassNotPublicManager::getInstanceFor()}
     *
     * @var array<string, ClassNotPublicManager>
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

    /** Создание объектов должно проводиться с помощью {@see ClassNotPublicManager::getInstanceFor()} */
    private function __construct() {}

    /**
     * Вернет объект для взаимодействия с непубличными элементами класса и объекта
     *
     * @param   string|object   $objectOrClass   Для какого класса или объекта создается
     *
     * @return  static
     *
     * @todo PHP8 Типизация аргументов
     */
    public static function getInstanceFor($objectOrClass): self
    {
        TypeValidator::validateOr($objectOrClass, ['string', 'object']);

        if (!isset(self::$_notPublicObjects)) self::$_notPublicObjects = new \WeakMap();

        // * * *

        if (is_object($objectOrClass))
        {
            if (empty(self::$_notPublicObjects[$objectOrClass]))
            {
                self::$_notPublicObjects[$objectOrClass] = new self();
                self::$_notPublicObjects[$objectOrClass]->toObject = $objectOrClass;
            }

            return self::$_notPublicObjects[$objectOrClass];
        }
        else
        {
            if (empty(self::$_notPublicClasses[$objectOrClass]))
            {
                self::$_notPublicClasses[$objectOrClass] = new self();
                self::$_notPublicClasses[$objectOrClass]->toObject = ClassTools::createObject($objectOrClass, false);
            }

            return self::$_notPublicClasses[$objectOrClass];
        }
    }

    /**
     * Вернет значение константы
     *
     * @param   string|object   $objectOrClass   Класс или объект, из которого будет проводиться чтение
     * @param   string          $name            Имя константы
     *
     * @return  mixed
     *
     * @todo PHP8 Типизация аргументов и возврата функции
     */
    public static function readConstant($objectOrClass, string $name)
    {
        TypeValidator::validateOr($objectOrClass, ['string', 'object']);

        return self::getInstanceFor($objectOrClass)->constant($name);
    }

    /**
     * Вернет значение указанной константы
     *
     * @param   string   $name   Имя константы
     *
     * @return  mixed
     *
     * @todo PHP8 Типизация возврата функции
     */
    public function constant(string $name)
    {
        return $this->getOrCreateFunctionForConstants()($name);
    }

    /**
     * Прочитает значение свойства объекта или статического свойства класса
     *
     * @param   string|object   $objectOrClass    Строка с именем класса (для чтения статических свойств) или объект (для чтения свойств объекта)
     * @param   string          $name             Имя свойства
     *
     * @return  mixed
     *
     * @todo PHP8 Типизация аргументов и возврата функции
     */
    public static function readProperty($objectOrClass, string $name)
    {
        TypeValidator::validateOr($objectOrClass, ['string', 'object']);

        if (is_object($objectOrClass)) return self::getInstanceFor($objectOrClass)->get($name);
        else return self::getInstanceFor($objectOrClass)->getStatic($name);
    }

    /**
     * Вернет значение указанного свойства объекта
     *
     * @param   string   $name   Имя свойства
     *
     * @return  mixed
     *
     * @todo PHP8 Типизация возврата функции
     */
    public function get(string $name)
    {
        return $this->getOrCreateFunctionForGetProperties()($name);
    }

    /**
     * Вернет значение указанного статического свойства
     *
     * @param   string   $name   Имя свойства
     *
     * @return  mixed
     *
     * @todo PHP8 Типизация возврата функции
     */
    public function getStatic(string $name)
    {
        return $this->getOrCreateFunctionForGetStaticProperties()($name);
    }

    /**
     * Прочитает значение свойства объекта или статического свойства класса
     *
     * @param   string|object   $objectOrClass    Строка с именем класса (для чтения статических свойств) или объект (для чтения свойств объекта)
     * @param   string|array    $name             Имя свойства или массив со списком свойств (имя свойства => значение)
     * @param   mixed           $data             Значение для установки
     *
     * @return  mixed
     *
     * @todo PHP8 Типизация аргументов и возврата функции
     */
    public static function writeProperty($objectOrClass, $name, $data = null)
    {
        TypeValidator::validateOr($objectOrClass, ['string', 'object']);
        TypeValidator::validateOr($name, ['string', 'array']);

        if (is_object($objectOrClass)) return self::getInstanceFor($objectOrClass)->set($name, $data);
        else return self::getInstanceFor($objectOrClass)->setStatic($name, $data);
    }

    /**
     * Установит значение указанному свойству объекта
     *
     * @param   string|array   $var    Имя свойства или массив со списком свойств (имя свойства => значение)
     * @param   mixed          $data   Значение для установки
     *
     * @return  $this
     *
     * @todo PHP8 Типизация аргументов функции
     */
    public function set($var, $data = null): self
    {
        TypeValidator::validateOr($var, ['string', 'array']);

        if (is_string($var)) $this->getOrCreateFunctionForSetProperties()($var, $data);
        else foreach ($var as $name => $data) $this->getOrCreateFunctionForSetProperties()($name, $data);

        return $this;
    }

    /**
     * Установит значение статическому свойству
     *
     * @param   string|array   $var    Имя свойства или массив со списком свойств (имя свойства => значение)
     * @param   mixed          $data   Значение для установки
     *
     * @return  $this
     *
     * @todo PHP8 Типизация аргументов функции
     */
    public function setStatic($var, $data = null): self
    {
        TypeValidator::validateOr($var, ['string', 'array']);

        if (is_string($var)) $this->getOrCreateFunctionForSetStaticProperties()($var, $data);
        else foreach ($var as $name => $data) $this->getOrCreateFunctionForSetStaticProperties()($name, $data);

        return $this;
    }

    /**
     * Вызов метода
     *
     * @param   array   $methodAsArray   Вызываемый метод в формате массива [$objectOrClass, $method]
     * @param   array   $arguments       Список аргументов
     *
     * @return  mixed
     *
     * @todo PHP8 Типизация возврата функции
     */
    public static function callMethod(array $methodAsArray, array $arguments = [])
    {
        if (!self::isCallable($methodAsArray))
        {
            throw new \TypeError('$methodAsArray can be callable, see format: [$objectOrClass, $method]');
        }

        if (is_object($methodAsArray[0])) return self::getInstanceFor($methodAsArray[0])->call($methodAsArray[1], $arguments);
        else return self::getInstanceFor($methodAsArray[0])->callStatic($methodAsArray[1], $arguments);
    }

    /**
     * Проведет вызов метода
     *
     * @param   string   $name        Имя метода
     * @param   mixed    $arguments   Список аргументов
     *
     * @return  mixed
     *
     * @todo PHP8 Типизация возврата функции
     */
    public function call(string $name, array $arguments = [])
    {
        return $this->getOrCreateFunctionForCall()($name, $arguments);
    }

    /**
     * Проведет вызов статического метода
     *
     * @param   string   $name        Имя статического метода
     * @param   mixed    $arguments   Список аргументов
     *
     * @return  mixed
     *
     * @todo PHP8 Типизация возврата функции
     */
    public function callStatic(string $name, array $arguments = [])
    {
        return $this->getOrCreateFunctionForCallStatic()($name, $arguments);
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
            $this->closureForObjects[__FUNCTION__] = function($name) {
                return constant(get_class($this) . "::{$name}");
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
            $this->closureForObjects[__FUNCTION__] = function($name) {
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
            $this->closureForObjects[__FUNCTION__] = function($name) {
                return (get_class($this))::$$name;
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
            $this->closureForObjects[__FUNCTION__] = function($name, $data) {
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
     */
    private function getOrCreateFunctionForSetStaticProperties(): \Closure
    {
        if (empty($this->closureForObjects[__FUNCTION__]))
        {
            $this->closureForObjects[__FUNCTION__] = function($name, $data) {
                get_class($this)::$$name = $data;
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
            $this->closureForObjects[__FUNCTION__] = function($name, $arguments) {
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
            $this->closureForObjects[__FUNCTION__] = function($name, $arguments) {
                return [get_class($this), $name](...$arguments);
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
