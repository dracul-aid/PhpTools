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

/**
 * Инструменты, для облегчения работы с исключениями
 *
 * Оглавление:
 * <br>{@see ExceptionTools::safeCallWithResult()} - Безопасно вызовет функцию, в случае возникновения исключения, вернет переданное значение
 * <br>{@see ExceptionTools::safeCallWithCallable()} - Безопасно вызовет функцию, в случае возникновения исключения - перехватит его и выполнит переданную функцию
 * <br>{@see ExceptionTools::safeCallFunctions()} -  Безопасно вызовет список функций. Результаты выполнения функций - игнорируются
 * <br>{@see ExceptionTools::callAndReturnException()} - Выполнит функцию, и если ее выполнение привело к исключению, перехватит это исключение и вернет его
 * <br>{@see ExceptionTools::wasCalledWithException()} - Выполнит функцию, и проверит, не вернула ли она необходимое исключение
 */
final class ExceptionTools
{
    /**
     * Безопасно вызовет функцию, в случае возникновения исключения, вернет переданное значение (по умолчанию NULL)
     *
     * @param   callable   $function             Функция
     * @param   array      $arguments            Аргументы для вызова функции
     * @param   mixed      $returnForException   Что вернет функция, если было перехвачено исключение
     *
     * @return  mixed
     *
     * @todo PHP8 типизация аргументов и ответа функции
     */
    public static function safeCallWithResult(callable $function, array $arguments = [], $returnForException = null)
    {
        try
        {
            return $function(... $arguments);
        }
        catch (\Throwable $exception)
        {
            return $returnForException;
        }
    }

    /**
     * Безопасно вызовет функцию, в случае возникновения исключения - перехватит его и выполнит переданную функцию
     *
     * @param   callable   $function               Функция
     * @param   array      $arguments              Аргументы для вызова функции
     * @param   callable   $callableForException   Функция, будет вызвано, в случае перехвата исключения. Вызов: <code>f(\Throwable $exception, array $arguments, callable $function): mixed</code>
     *
     * @return  mixed
     *
     * @todo PHP8 типизация ответа функции
     */
    public static function safeCallWithCallable(callable $function, array $arguments, callable $callableForException)
    {
        try
        {
            return $function(... $arguments);
        }
        catch (\Throwable $exception)
        {
            return $callableForException($exception, $arguments, $function);
        }
    }

    /**
     * Безопасно вызовет список функций. Результаты выполнения функций - игнорируются
     *
     * @param   callable[]      $functions              Список функций для вызова (Функции должны быть без аргументов)
     * @param   null|callable   $callableForException   Функция, будет вызвано, в случае перехвата исключения. Вызов: <code>f(\Throwable $exception, callable $function): mixed</code>
     *
     * @return  void
     *
     * @todo PHP8 Типизация для аргументов функции
     */
    public static function safeCallFunctions(iterable $functions, ?callable $callableForException = null): void
    {
        foreach ($functions as $function)
        {
            try
            {
                $function();
            }
            catch (\Throwable $exception)
            {
                if ($callableForException !== null) $callableForException($exception, $function);
            }
        }
    }

    /**
     * Выполнит функцию, и если ее выполнение привело к исключению, перехватит это исключение и вернет его
     *
     * @param   callable   $function     Функция
     * @param   array      $arguments    Аргументы для вызова функции
     * @param   mixed     &$_return      Результат работы функции
     *
     * @return  null|\Throwable    Вернет пойманное исключение или NULL, если исключение не было выброшено
     *
     * @todo PHP8 типизация аргументов и ответа функции
     */
    public static function callAndReturnException(callable $function, array $arguments = [], &$_return = null): ?\Throwable
    {
        try
        {
            $_return = $function(... $arguments);
            return null;
        }
        catch (\Throwable $exception)
        {
            $_return = null;
            return $exception;
        }
    }

    /**
     * Выполнит функцию, и проверит, не вернула ли она необходимое исключение
     *
     * @param   callable      $function            Функция
     * @param   array         $arguments           Аргументы функции
     * @param   string        $throwableClass      Класс исключения (или интерфейс)
     * @param   null|string   $throwableMessage    Если не NULL - также проверит тест сообщения исключения
     * @param   mixed         $throwableCode       Если не NULL - также проверит код исключения
     * @param   mixed        &$_return             Результат работы функции
     *
     * @return  bool   Вернет TRUE, если в ходе работы функции было выброшено нужно исключение
     *
     * @throws  \Throwable   Если в ходе выполнения было выброшено неожидаемое исключение, оно будет проброшено далее
     *
     * @todo PHP8 типизация аргументов
     */
    public static function wasCalledWithException(callable $function, array $arguments, string $throwableClass, ?string $throwableMessage = null, $throwableCode = null, &$_return = null): bool
    {
        try
        {
            $_return = $function(... $arguments);
            return false;
        }
        catch (\Throwable $exception)
        {
            if (!is_a($exception, $throwableClass)) throw $exception;

            if ($throwableMessage !== null && $throwableMessage !== $exception->getMessage()) return false;
            if ($throwableCode !== null && $throwableCode !== $exception->getCode()) return false;

            return true;
        }
    }
}
