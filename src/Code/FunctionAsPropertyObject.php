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
     * @todo PHP8 типизация
     */
    protected $function;

    /**
     * Создаст объект, хранящий функцию
     *
     * @param   string|callable   $function   Функция или языковая конструкция
     * @param   bool              $safeCall   Будет произведен "защищенный вызов" (если при вызове передано больше аргументов,
     *                                        чем может принять функция, лишние аргументы будут отброшены)
     *
     * @todo PHP8 типизация аргументов функции
     */
    public function __construct($function, bool $safeCall = true)
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
     *
     * @todo PHP8 типизация аргументов функции, также часть проверок в коде станет ненужной
     */
    public static function getOrCreate($functionOrObject): self
    {
        if ($functionOrObject instanceof self) return $functionOrObject;

        // @todo PHP8 проверка теряет смысл
        if (is_callable($functionOrObject) || is_string($functionOrObject)) return new static($functionOrObject);

        // @todo PHP8 удаляем, так как никогда не будет выполняться
        throw new \TypeError('$functionOrObject can be string|callable|FunctionAsPropertyObject');
    }

    /**
     * Создаст объект, хранящий функцию
     *
     * @param   string|callable   $function   Функция или языковая конструкция
     * @param   bool              $safeCall   Будет произведен "защищенный вызов" (если при вызове передано больше аргументов,
     *                                        чем может принять функция, лишние аргументы будут отброшены)
     *
     * @todo PHP8 типизация аргументов функции
     */
    public function setFunction($function, bool $safeCall = true): self
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
     *
     * @todo PHP8 типизация аргументов и ответа функции
     */
    public function call(...$arguments)
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
     * @todo PHP8 типизация аргументов и ответа функции
     */
    public function callSafe(...$arguments)
    {
        return CallFunctionHelper::exe($this->function, ... $arguments);
    }

    /**
     * Вернет установленную функцию
     *
     * @return string|callable
     *
     * @todo PHP8 типизация ответа функции
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * Вызов объекта, как функции
     *
     * @param   mixed   ...$arguments   Список аргументов
     *
     * @return  mixed
     *
     * @todo PHP8 типизация аргументов и ответа функции
     */
    public function __invoke(...$arguments)
    {
        return $this->call(...$arguments);
    }
}
