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

use DraculAid\PhpTools\tests\Code\FunctionAsPropertyObjectTest;

/**
 * Позволяет использовать функцию в качестве свойства классов
 *
 * Позволяет типизировать свойства классов, используя их для хранения анонимных функций ({@see \Closure}), обычных функций,
 * методов классов, а также языковых конструкций PHP
 *
 * Оглавление:
 * <br>{@see FunctionAsPropertyObject::getOrCreate()} Вернет (если надо создаст) объект, хранящий функцию
 * <br>{@see self::setFunction()} Заменит установленную функцию
 * <br>{@see self::call()} Произведет вызов
 * <br>{@see self::callSafe()} Произведет защищенный вызов
 * <br>{@see self::getFunction()} Вернет установленную функцию
 *
 * Test cases for class {@see FunctionAsPropertyObjectTest}
 */
class FunctionAsPropertyObject
{
    /**
     * Хранимая функция является "функцией" или нет
     *
     * Возможные варианты
     * <br>FALSE: это не функция, а языковая конструкция PHP
     * <br>TRUE: это функция (точнее один из вариантов вызываемого, см PHP псевдотип `callable`)
     */
    protected bool $is_callable = false;

    /**
     * Будет произведен "защищенный вызов"
     * (если при вызове передано больше аргументов, чем может принять функция, лишние аргументы будут отброшены)
     */
    protected bool $safeCall = true;

    /**
     * Хранимая функция
     *
     * @var string|callable
     *
     * (!) В PHP8 нельзя указать тип callable для свойств класса
     */
    protected mixed $function;

    /**
     * Создаст объект, хранящий функцию
     *
     * @param   string|callable   $function   Функция или языковая конструкция
     * @param   bool              $safeCall   Будет произведен "защищенный вызов" (если при вызове передано больше аргументов,
     *                                        чем может принять функция, лишние аргументы будут отброшены)
     */
    public function __construct(string|callable $function, bool $safeCall = true)
    {
        $this->setFunction($function, $safeCall);
    }

    /**
     * Вернет объект хранящий функцию:
     * <br>Если передана функция или языковая конструкция - вернет объект хранящий функцию
     * <br>Если передан объект хранящий функцию, вернет именно его
     *
     * @param   string|callable|FunctionAsPropertyObject   $functionOrObject
     *
     * @return  static
     */
    public static function getOrCreate(string|callable|FunctionAsPropertyObject $functionOrObject): object
    {
        if ($functionOrObject instanceof static) return $functionOrObject;

        return new static($functionOrObject);
    }

    /**
     * Создаст объект, хранящий функцию
     *
     * @param   string|callable   $function   Функция или языковая конструкция
     * @param   bool              $safeCall   Будет произведен "защищенный вызов" (если при вызове передано больше аргументов,
     *                                        чем может принять функция, лишние аргументы будут отброшены)
     */
    public function setFunction(string|callable $function, bool $safeCall = true): self
    {
        $this->is_callable = is_callable($function);
        $this->safeCall = $safeCall;
        $this->function = $function;

        return $this;
    }

    /**
     * Вызовет функцию
     *
     * @param   mixed   ...$arguments   Список аргументов
     *
     * @return  mixed
     * @throws  \ReflectionException   Если не удалось получить объект-рефлексию для установленной функции
     */
    public function call(mixed ...$arguments): mixed
    {
        if ($this->safeCall || !$this->is_callable) return CallFunctionHelper::exe($this->function, ... $arguments);
        else return call_user_func_array($this->function, $arguments);
    }

    /**
     * Вызовет функцию в защищенном режиме
     * (если при вызове передано больше аргументов, чем может принять функция, лишние аргументы будут отброшены)
     *
     * @param   mixed   ...$arguments   Список аргументов
     *
     * @return  mixed
     *
     * @throws  \ReflectionException   Если не удалось получить объект-рефлексию для установленной функции
     */
    public function callSafe(mixed ...$arguments): mixed
    {
        return CallFunctionHelper::exe($this->function, ... $arguments);
    }

    /**
     * Вернет установленную функцию
     *
     * @return string|callable
     */
    public function getFunction(): string|callable
    {
        return $this->function;
    }

    /**
     * Вызов объекта, как функции
     *
     * @param   mixed   ...$arguments   Список аргументов
     *
     * @return  mixed
     */
    public function __invoke(mixed ...$arguments): mixed
    {
        return $this->call(...$arguments);
    }
}
