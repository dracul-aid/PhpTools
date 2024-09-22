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

use DraculAid\PhpTools\Classes\ClassNotPublicManager;
use DraculAid\PhpTools\tests\ExceptionTools\ExceptionToolsTest;

/**
 * Инструменты, для облегчения работы с исключениями
 *
 * Оглавление:
 * <br>{@see ExceptionTools::safeCallWithResult()} - В случае возникновения исключения, вернет переданное значение
 * <br>{@see ExceptionTools::safeCallWithCallable()} - В случае возникновения исключения - перехватит его и выполнит переданную функцию
 * <br>{@see ExceptionTools::safeCallFunctions()} - Вызовет список функций. Результаты выполнения функций - игнорируются
 * <br>{@see ExceptionTools::callAndReturnException()} - Перехватит и вернет пойманное исключение (или NULL)
 * <br>{@see ExceptionTools::wasCalledWithException()} - Выполнит функцию, и проверит, не вернула ли она необходимое исключение
 *
 * Test cases for class {@see ExceptionToolsTest}
 */
final class ExceptionTools
{
    /**
     * Безопасно вызовет функцию, в случае возникновения исключения, вернет переданное значение (по умолчанию NULL)
     *
     * (!) $function также может быть массивом, указывающим на непубличный метод класса
     *
     * @param   callable|array   $function             Функция
     * @param   array            $arguments            Аргументы для вызова функции
     * @param   mixed            $returnForException   Что вернет функция, если было перехвачено исключение
     *
     * @return  mixed
     *
     * @todo PHP8 типизация аргументов и ответа функции
     */
    public static function safeCallWithResult($function, array $arguments = [], $returnForException = null)
    {
        try
        {
            return self::functionCall($function, $arguments);
        }
        catch (\Throwable $exception)
        {
            return $returnForException;
        }
    }

    /**
     * Безопасно вызовет функцию, в случае возникновения исключения - перехватит его и выполнит переданную функцию
     *
     * (!) $function также может быть массивом, указывающим на непубличный метод класса
     *
     * @param   callable|array   $function               Функция
     * @param   array            $arguments              Аргументы для вызова функции
     * @param   callable         $callableForException   Функция, будет вызвано, в случае перехвата исключения.
     *                                                   <br>Вызов: <code>f(\Throwable $exception, array $arguments, callable $function): mixed</code>
     *
     * @return  mixed
     *
     * @todo PHP8 типизация ответа функции
     */
    public static function safeCallWithCallable($function, array $arguments, callable $callableForException)
    {
        try
        {
            return self::functionCall($function, $arguments);
        }
        catch (\Throwable $exception)
        {
            return $callableForException($exception, $arguments, $function);
        }
    }

    /**
     * Безопасно вызовет список функций. Результаты выполнения функций - игнорируются
     *
     * (!) $functions также может быть массивом, указывающим на непубличный метод класса
     *
     * @param   iterable<callable|array>   $functions              Список функций для вызова (Функции должны быть без аргументов)
     * @param   null|callable              $callableForException   Функция, будет вызвано, в случае перехвата исключения.
     *                                                             <br>Вызов: <code>f(\Throwable $exception, callable $function): mixed</code>
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
                self::functionCall($function, []);
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
     * (!) $function также может быть массивом, указывающим на непубличный метод класса
     *
     * @param   callable|array   $function     Функция
     * @param   array            $arguments    Аргументы для вызова функции
     * @param   mixed           &$_return      Результат работы функции
     *
     * @return  null|\Throwable    Вернет пойманное исключение или NULL, если исключение не было выброшено
     *
     * @todo PHP8 типизация аргументов и ответа функции
     */
    public static function callAndReturnException($function, array $arguments = [], &$_return = null): ?\Throwable
    {
        try
        {
            $_return = self::functionCall($function, $arguments);
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
     * (!) $function также может быть массивом, указывающим на непубличный метод класса
     *
     * @param   callable|array  $function            Функция
     * @param   array           $arguments           Аргументы функции
     * @param   string          $throwableClass      Класс исключения (или интерфейс)
     * @param   null|string     $throwableMessage    Если не NULL - также проверит тест сообщения исключения
     * @param   mixed           $throwableCode       Если не NULL - также проверит код исключения
     * @param   mixed          &$_return             Результат работы функции
     *
     * @return  bool   Вернет TRUE, если в ходе работы функции было выброшено нужно исключение
     *
     * @throws  \Throwable   Если в ходе выполнения было выброшено неожидаемое исключение, оно будет проброшено далее
     *
     * @todo PHP8 типизация аргументов
     */
    public static function wasCalledWithException($function, array $arguments = [], string $throwableClass = \Throwable::class, ?string $throwableMessage = null, $throwableCode = null, &$_return = null): bool
    {
        try
        {
            $_return = self::functionCall($function, $arguments);
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

    /**
     * Вызовет переданную функцию. Если функция была передана, как массив (с указанием на непубличный метод класса), она
     * все равно будет вызвана
     *
     * @param   callable|array   $function    Вызываемая функция
     * @param   array            $arguments   Список аргументов для вызова
     *
     * @return  mixed   Вернет результат выполнения функции
     *
     * @throws  \TypeError  Если функцию невозможно вызвать
     *
     * @todo PHP8 типизация аргументов и результата работы функции
     */
    private static function functionCall($function, array $arguments)
    {
        if (is_callable($function))
        {
            return $function(... $arguments);
        }
        elseif (is_array($function) && count($function) === 2)
        {
            return ClassNotPublicManager::callMethod($function, $arguments);
        }
        else
        {
            $varDesc = gettype($function);
            if (is_array($function)) $varDesc .= '(' . count($function) . ')';

            /** @todo PHP8 заменить на get_debug_type() */
            throw new \TypeError("Argument \$function must be a callable or be an array, with a non-public method, but it a {$varDesc}");
        }
    }
}
