<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\Code;

use DraculAid\PhpTools\Classes\ClassNotPublicManager;
use DraculAid\PhpTools\Classes\ClassTools;
use DraculAid\PhpTools\Classes\Patterns\Runner\StaticRunnerInterface;
use DraculAid\PhpTools\tests\Code\CallFunctionHelperTest;

/**
 * Функционал, облегчающий вызов функций
 *
 * 1. Позволяет вызывать языковые конструкции, как функции
 * 2. Позволяет вызывать функции, передавая в них больше аргументов, чем готова принять функция
 *
 * Оглавление
 * <br>{@see CallFunctionHelper::STRUCTURES} Список структур, которые можно вызвать через {@see CallFunctionHelper::exe()}
 * <br>{@see CallFunctionHelper::exe()} Вызовет функцию или языковую конструкцию
 * <br>{@see CallFunctionHelper::callMethodFromEmptyObject()} Позволяет вызвать метод класса (объект для вызова будет создан без вызова конструктора)
 * <br>{@see CallFunctionHelper::isStructures()} Проверяет переданное значение, является оно языковой конструкцией или нет
 * <br>{@see CallFunctionHelper::isClassCallable()} Проверит, "вызываемое" является вызовом метода или нет
 * <br>{@see CallFunctionHelper::isCallable()} Проверяет, может ли переданная строка (иное `callable`) быть вызвано как "функция"
 * <br>{@see CallFunctionHelper::getReflectionForCallable()} Вернет рефлексию для "Вызываемого", определив чем оно является, методом или функцией
 *
 * Test cases for class {@see CallFunctionHelperTest}
 */
final class CallFunctionHelper implements StaticRunnerInterface
{
    /**
     * Список структур, которые можно вызвать через {@see CallFunctionHelper::exe()}
     */
    public const STRUCTURES = [
        'isset', /** {@see isset()} */
        'empty', /** {@see empty()} */
        'require', /** {@see require()} */
        'require_once', /** {@see require_once()} */
        'include', /** {@see include()} */
        'include_once', /** {@see include_once()} */
        'echo', /** {@see echo()} */
        'print', /** {@see print()} */
        'eval', /** {@see eval()} */
    ];

    /**
     * Вызовет функцию или языковую конструкцию (корректно отработав случаи, в которых аргументов будет больше аргументов функции)
     *
     * (!) В {@see isset()} передается только 1 аргумент.
     *
     * @param   string|callable       $function     Функция или языковая конструкция
     *                                              <br>Если передана строка, начинающаяся с new - то будет создан объект
     *                                              указанного класса. Пример: "new stdClass" создаст объект {@see \stdClass} класса
     * @param   mixed              ...$arguments    Передаваемые аргументы
     *
     * @return  mixed
     *
     * @throws  \LogicException       Если $function не может быть вызвана
     * @throws  \ReflectionException  В случае провала получения рефлексии для функции или метода
     *
     * @todo PHP8 аргументы и ответ функции
     * @todo PHP8 Реализовать - Если нужно передать все аргументы, воспользуйтесь 'isset_list'
     * @todo PHP8 match()
     * @todo Реализовать - больше тестов для различных конструкций языка
     * @todo Реализовать - тесты для вызова методов классов
     */
    public static function exe($function, ... $arguments)
    {
        if (is_callable($function)) return self::exeCallable($function, $arguments);

        if (!is_string($function)) throw new \LogicException("{$function} is not callable and not PHP code construction");

        /** Создание нового класса */
        if ($function[0] === 'n' && $function[1] === 'e' && $function[2] === 'w' && $function[3] === ' ')
        {
            $function = substr($function, 4);
            return new $function(...$arguments);
        }
        elseif ($function === 'isset') return isset($arguments[0]);
        elseif ($function === 'empty') return empty($arguments[0]);
        elseif ($function === 'require') return require($arguments[0]);
        elseif ($function === 'require_once') return require_once($arguments[0]);
        elseif ($function === 'include') return include($arguments[0]);
        elseif ($function === 'include_once') return include_once($arguments[0]);
        elseif ($function === 'eval') return eval($arguments[0]);
        elseif ($function === 'echo')
        {
            foreach ($arguments as $value) echo $value;
            return null;
        }
        elseif ($function === 'print') return print($arguments[0]);
        elseif ($function === 'array') return array(...$arguments);
        else throw new \LogicException("{$function} is not callable");
    }

    /**
     * Позволяет вызвать метод класса, объект для вызова будет создан без вызова конструктора
     * (Используется в случаях, когда нужно вызвать метод, но создание объекта сопряжено с высокими накладными расходами)
     *
     * (!) Может вызвать и непубличный метод
     *
     * @param   string[]               $classAndMethod   Вызываемый метод в формате массива [$class, $method]
     * @param   array                  $arguments        Аргументы, с которыми будет вызван метод
     * @param   array<string, mixed>   $properties       Список свойств для установки создаваемому объекту (в том числе и непубличных)
     *
     * @return  mixed   Вернет результат работы вызванного метода
     *
     * @todo PHP8 ответ функции
     */
    public static function callMethodFromEmptyObject(array $classAndMethod, array $arguments = [], array $properties = [])
    {
        if (!is_string($classAndMethod[0] ?? null) || !is_string($classAndMethod[1] ?? null))
        {
            throw new \TypeError('$classAndMethod can be callable, see format: [$class, $method]');
        }

        // Создаем объект для вызова метода (без вызова конструктора)
        $object = ClassTools::createObject($classAndMethod[0], false, $properties);

        return ClassNotPublicManager::callMethod([$object, $classAndMethod[1]], $arguments);
    }

    /**
     * Проверяет переданное значение, является оно языковой конструкцией или нет
     *
     * (!) Вернет TRUE только для языковых конструкций, которые могут быть вызваны в {@see CallFunctionHelper::exe()}
     *     (т.е. конструкции, которые часто воспринимаются, как функции)
     *
     * @param   string   $testValue
     *
     * @return  bool
     */
    public static function isStructures(string $testValue): bool
    {
        return $testValue === 'isset' /** {@see isset()} */
            || $testValue === 'empty' /** {@see empty()} */
            || $testValue === 'require' /** {@see require()} */
            || $testValue === 'require_once' /** {@see require_once()} */
            || $testValue === 'include' /** {@see include()} */
            || $testValue === 'include_once' /** {@see include_once()} */
            || $testValue === 'echo' /** {@see echo()} */
            || $testValue === 'print' /** {@see print()} */
            || $testValue === 'eval'; /** {@see eval()} */
    }

    /**
     * Проверяет, может ли переданная строка (иное `callable`) быть вызвано как "функция" в {@see CallFunctionHelper::exe()}
     *
     * @param   string|callable   $function
     *
     * @return bool
     *
     * @todo PHP8 аргументы функции
     */
    public static function isCallable($function): bool
    {
        return is_callable($function) || self::isStructures($function);
    }

    /**
     * Проверит, "вызываемое" является вызовом метода или нет
     *
     * @param   callable   $function      "Вызываемое" для проверки
     *
     * @return bool
     */
    public static function isClassCallable(callable $function): bool
    {
        return is_array($function) || (is_string($function) && strpos($function, '::') > 1);
    }

    /**
     * Вернет рефлексию для "Вызываемого", определив чем оно является, методом или функцией
     *
     * (!) Конструкции языка (вроде `isset()`) не являются `callable` и для них невозможно получить рефлексию
     *
     * @param   callable   $function
     *
     * @return  \ReflectionFunctionAbstract
     *
     * @throws  \ReflectionException  В случае провала получения рефлексии для функции
     */
    public static function getReflectionForCallable(callable $function): \ReflectionFunctionAbstract
    {
        if (self::isClassCallable($function))
        {
            if (is_array($function)) return new \ReflectionMethod($function[0], $function[1]);
            else return new \ReflectionMethod($function);
        }

        return new \ReflectionFunction($function);
    }

    /**
     * Вызовет функцию, корректно отработав случаи, что передано больше аргументов, чем готова принять функция
     *
     * @param   callable   $function    Функция
     * @param   array      $arguments   Аргументы
     *
     * @return  mixed
     *
     * @throws  \ReflectionException  В случае провала получения рефлексии для функции
     */
    private static function exeCallable(callable $function, array $arguments)
    {
        if (count($arguments) === 0) return call_user_func($function);
        elseif (count($arguments) === 1 && array_key_exists(0, $arguments)) return call_user_func($function, $arguments[0]);

        // * * *

        $functionReflection = self::getReflectionForCallable($function);

        // если аргументов передано больше, чем готова принять функция - урежем кол-во аргументов
        if ($functionReflection->getNumberOfParameters() < count($arguments))
        {
            return call_user_func_array(
                $function,
                array_slice($arguments, 0, $functionReflection->getNumberOfParameters())
            );
        }
        else
        {
            return call_user_func_array($function, $arguments);
        }
    }
}
