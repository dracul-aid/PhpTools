<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\ExceptionTools;

use DraculAid\PhpTools\tests\ExceptionTools\ResultExceptionTest;

/**
 * Исключение, для организации "Всплытия" результатов работы
 *
 * (!) Это исключение не должно использоваться для генерации ошибок
 *
 * Оглавление:
 * <br>{@see ResultException::call()} Выполнит функцию, ожидая, что она может вернуть как обычны ответ, так и всплывающий ответ
 * <br>---
 * <br>{@see self::$result}        Хранит результат работы
 * <br>{@see self::__invoke()}     Вернет результат работы (в том числе и по ссылке)
 * <br>{@see self::__construct()}  Создаст исключение и запишет результат работы
 *
 * Test cases for class {@see ResultExceptionTest}
 */
final class ResultException extends \Exception
{
    /**
     * Хранит результат работы
     *
     * @var mixed
     *
     * @todo PHP8 Типизация свойства
     */
    public $result;

    /**
     * Создаст исключение и запишет результат работы
     *
     * @param   mixed   $result   Результат работы
     *
     * @todo PHP8 Типизация аргументов
     */
    public function __construct($result)
    {
        $this->result = $result;
    }

    /**
     * Выполнит функцию, ожидая, что она может вернуть как обычны ответ, так и всплывающий ответ (т.е. исключение {@see ResultException})
     *
     * (!) Функция не подходит для случаев, в которых вызываемая функция должна вернуть значение по ссылке
     *
     * @param   callable   $function        Вызываемая функция
     * @param   array      $arguments       Аргументы функции
     * @param   bool      &$withException   Сюда будет помещен TRUE (если вызов функции закончился перехватом всплывающего ответа [{@see ResultException}]) или FALSE если функция вернула стандартный ответ (т.е. сработал return;)
     *
     * @return  mixed
     */
    public static function call(callable $function, array $arguments = [], ?bool &$withException = null)
    {
        try
        {
            $withException = false;
            return $function(... $arguments);
        }
        catch (ResultException $resultException)
        {
            $withException = true;
            return $resultException->result;
        }
    }

    /**
     * Вернет результат работы (в том числе и по ссылке)
     *
     * @return  mixed
     *
     * @todo PHP8 Типизация аргументов
     */
    public function __invoke()
    {
        return $this->result;
    }
}
