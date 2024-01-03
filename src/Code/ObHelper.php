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

/**
 * Позволяет вызвать функцию (или языковую конструкцию) с перехватом потока вывода
 * (т.е., то что выводилось в вызываемой функции в echo, print и похожих функциях)
 *
 * @see ScriptLoader Позволяет перехватить поток вывода для загружаемого скрипта или выполняемого PHP кода
 * @link https://www.php.net/manual/ru/ref.outcontrol.php Функии PHP для контроля вывода
 *
 * Оглавление:
 * <br>{@see ObHelper::callFunction()} Функцию
 * <br>{@see ObHelper::callWithFunctionHelper()} Функцию и языковую конструкцию
 * <br>{@see ObHelper::callNotPublicMethod()} Непубличный метод класса (в том числе и статический)
 * <br>{@see ObHelper::callMethodFromEmptyObject()} Позволяет вызвать метод "пустого объекта"
 */
final class ObHelper
{
    /**
     * Перехватит вывод в поток для вызванной функции (но не языковую конструкцию)
     *
     * @param   callable     $function     Вызываемая функция
     * @param   array        $arguments    Массив аргументов для функции
     * @param   mixed        $_return      Ответ вызванной функции
     *
     * @return  string    Вернет выведенные в поток вывода функцией данные
     *
     * @todo PHP8 Типизация для аргументов функции
     */
    public static function callFunction(callable $function, array $arguments = [], &$_return = null): string
    {
        ob_start();

        $_return = $function(... $arguments);

        $function_return = ob_get_contents();
        ob_end_clean();

        return $function_return;
    }

    /**
     * Перехватит вывод в поток для вызванной функции или языковую конструкцию (вызов устойчив к тому, что может быть
     * передано больше чем надо аргументов)
     *
     * Для вызова `require()`, `include()` или `eval()` может быть более удобен {@see ScriptLoader}, так как он позволяет
     * передать в языковую конструкцию переменные (включая передачу по ссылке)
     *
     * @param   string|callable   $function     Вызываемая функция или конструкция
     * @param   array             $arguments    Массив аргументов для функции
     * @param   mixed             $_return      Ответ вызванной функции
     *
     * @return  string    Вернет выведенные в поток вывода функцией данные
     *
     * @throws  \ReflectionException  Будет выброшен, в случае невозможности получения рефлексии для вызываемой функции
     *
     * @todo PHP8 Типизация для аргументов функции
     */
    public static function callWithFunctionHelper($function, array $arguments = [], &$_return = null): string
    {
        ob_start();

        $_return = CallFunctionHelper::exe($function, ... $arguments);

        $function_return = ob_get_contents();
        ob_end_clean();

        return $function_return;
    }

    /**
     * Перехватит вывод в поток для вызова непубличного метода класса (в том числе и статический)
     *
     * @param   string[]     $method      Вызываемый метод в формате массива [$objectOrClass, $method]
     * @param   array        $arguments   Массив аргументов для метода
     * @param   mixed        $_return     Ответ вызванного метода
     *
     * @return  string   Вернет выведенные в поток вывода функцией данные
     *
     * @throws  \TypeError   В случае, если передан не массив вида [$objectOrClass, $method]
     *
     * @todo PHP8 Типизация для аргументов функции
     */
    public static function callNotPublicMethod(array $method, array $arguments = [], &$_return = null): string
    {
        ob_start();

        $_return = ClassNotPublicManager::callMethod($method, $arguments);

        $function_return = ob_get_contents();
        ob_end_clean();

        return $function_return;
    }

    /**
     * Перехватит вывод в поток для вызова метода пустого объекта (объекта, созданного без вызова конструктора)
     *
     * @param   string[]               $method       Вызываемый метод в формате массива [$class, $method]
     * @param   array                  $arguments    Массив аргументов для метода
     * @param   array<string, mixed>   $properties   Список свойств для установки создаваемому объекту (в том числе и непубличных)
     * @param   mixed                  $_return      Ответ вызванного метода
     *
     * @return  string   Вернет выведенные в поток вывода функцией данные
     *
     * @throws  \TypeError   В случае, если передан не массив вида [$class, $method]
     *
     * @todo PHP8 Типизация для аргументов функции
     */
    public static function callMethodFromEmptyObject(array $method, array $arguments = [], array $properties = [], &$_return = null): string
    {
        ob_start();

        $_return = CallFunctionHelper::callMethodFromEmptyObject($method, $arguments, $properties);

        $function_return = ob_get_contents();
        ob_end_clean();

        return $function_return;
    }
}
