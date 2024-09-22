<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\ExceptionTools\PhpErrorCode;

use DraculAid\PhpTools\ExceptionTools\PhpErrorCode\Errors\Interfaces\PhpCodeErrorBasicInterface;

/**
 * Набор инструментов для работы с ошибками PHP (см {@see \Error})
 *
 * Оглавление:
 * <br>- {@see PhpErrorCodeThrowableTools::TYPE_AND_BASIC_ERROR_CLASSES} Соответствие типов ошибок и классов встроенных в PHP ошибок
 * <br>- {@see PhpErrorCodeThrowableTools::TYPE_AND_ERROR_CLASSES} Соответствие типов ошибок и классов ошибок
 * <br>--- Создание объектов ошибок:
 * <br>- {@see PhpErrorCodeThrowableTools::getErrorObject()} Вернет объект ошибку, по типу ошибки
 * <br>- {@see PhpErrorCodeThrowableTools::getBasicErrorObject()} Вернет объект PHP ошибки, по типу ошибки
 */
class PhpErrorCodeThrowableTools
{
    /**
     * Соответствие типов ошибок и классов встроенных в PHP ошибок (ключи - типы ошибок, значения - полные имена классов ошибок)
     *
     * @var array<int, class-string<\Error>>
     */
    public const TYPE_AND_BASIC_ERROR_CLASSES = [
        E_ERROR => \Error::class,
        E_PARSE => \ParseError::class,
        E_COMPILE_ERROR => \CompileError::class,
    ];

    /**
     * Соответствие типов ошибок и классов встроенных в PHP ошибок (ключи - типы ошибок, значения - полные имена классов ошибок)
     *
     * @var array<int, class-string<\Error&PhpCodeErrorBasicInterface>
     */
    public const TYPE_AND_ERROR_CLASSES = [
        E_ERROR => Errors\Error::class,
        E_WARNING => Errors\Warning::class,
        E_PARSE => Errors\Parse::class,
        E_NOTICE => Errors\Notice::class,
        E_CORE_ERROR => Errors\CoreError::class,
        E_CORE_WARNING => Errors\CoreWarning::class,
        E_COMPILE_ERROR => Errors\CompileError::class,
        E_COMPILE_WARNING => Errors\CompileWarning::class,
        E_USER_ERROR => Errors\UserError::class,
        E_USER_WARNING => Errors\UserWarning::class,
        E_USER_NOTICE => Errors\UserNotice::class,
        E_STRICT => Errors\Strict::class,
        E_RECOVERABLE_ERROR => Errors\RecoverableError::class,
        E_DEPRECATED => Errors\Deprecated::class,
        E_USER_DEPRECATED => Errors\UserDeprecated::class,
    ];

    /**
     * Создаст объект с встроенной в PHP ошибкой, по типу ошибки. Если у ошибки нет класса - вернет {@see \Error}
     *
     * @param   int               $errorType   Тип ошибки
     * @param   string            $message     Текст с описанием ошибки
     * @param   int               $code        Код ошибки
     * @param   null|\Throwable   $previous    Предыдущий объект-ошибка
     *
     * @return  \Error
     *
     * @todo PHP8 типизация аргументов
     */
    public static function getBasicErrorObject(int $errorType, string $message = '', int $code = 0, ?\Throwable $previous = null): \Error
    {
        /** @var class-string<\Error> $classError Класс для создания ошибки */
        $classError = self::TYPE_AND_BASIC_ERROR_CLASSES[$errorType] ?? \Error::class;

        return new $classError($message, $code, $previous);
    }

    /**
     * Создаст объект ошибки, по типу ошибки
     *
     * @param   int               $errorType   Тип ошибки
     * @param   string            $message     Текст с описанием ошибки
     * @param   int               $code        Код ошибки
     * @param   null|\Throwable   $previous    Предыдущий объект-ошибка
     *
     * @return  \Error&PhpCodeErrorBasicInterface
     *
     * @throws \RuntimeException Если был передан код несуществующей ошибки
     *
     * @todo PHP8 типизация аргументов и ответа функции
     */
    public static function getErrorObject(int $errorType, string $message = '', int $code = 0, ?\Throwable $previous = null): \Error
    {
        if (!isset(self::TYPE_AND_ERROR_CLASSES[$errorType])) throw new \RuntimeException("Not found class for error code #{$errorType}");

        /** @var class-string<\Error&PhpCodeErrorBasicInterface> $classError Класс для создания ошибки (TODO PHP8 - эта промежуточная переменная нужна только в PHP7) */
        $classError = self::TYPE_AND_ERROR_CLASSES[$errorType];

        return new $classError($message, $code, $previous);
    }
}
