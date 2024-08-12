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

/**
 * Набор инструментов для работы с ошибками PHP (см {@see \Error})
 *
 * См также
 * <br>- {@see PhpErrorCodeRuDescriptionsConstants} Описание и названия ошибок на русском языке
 *
 * Оглавление:
 * <br>- {@see PhpErrorCodeConstants::NAMES} Список названий констант ошибок
 * <br>- {@see PhpErrorCodeConstants::ALL} Полный список всех типов PHP ошибок
 * <br>--- Списки типов ошибок:
 * <br>- {@see PhpErrorCodeConstants::WARNINGS_AND_NOTICES} Список Предупреждений (Ошибок НЕ приводящих к прекращению выполнения скрипта)
 * <br>- {@see PhpErrorCodeConstants::FATAL_ERRORS} Список ошибок, приводящих к прекращению выполнения скрипта
 * <br>- {@see PhpErrorCodeConstants::SCRIPT_ERRORS} Список ошибок связанный с непосредственным выполнением кода
 * <br>- {@see PhpErrorCodeConstants::CORE_ERRORS} Список ошибок относящихся к ядру PHP (т.е. не связанных с кодом)
 * <br>- {@see PhpErrorCodeConstants::CODE_ERRORS} Список ошибок связанных с компиляцией кода
 * <br>- {@see PhpErrorCodeConstants::DEPRECATED_ERRORS} - Ошибки связанные с использованием "устаревшего" кода
 * <br>- {@see PhpErrorCodeConstants::USER_ERRORS} - "Пользовательские" ошибки
 *
 * Test cases for class {@see PhpErrorCodeDescriptionsConstantsTest}
 */
final class PhpErrorCodeConstants
{
    /** Список названий констант ошибок (ключи - типы констант, значения - имена констант типов ошибок) */
    public const NAMES = [
        E_ERROR => 'E_ERROR',
        E_WARNING => 'E_WARNING',
        E_PARSE => 'E_PARSE',
        E_NOTICE => 'E_NOTICE',
        E_CORE_ERROR => 'E_CORE_ERROR',
        E_CORE_WARNING => 'E_CORE_WARNING',
        E_COMPILE_ERROR => 'E_COMPILE_ERROR',
        E_COMPILE_WARNING => 'E_COMPILE_WARNING',
        E_USER_ERROR => 'E_USER_ERROR',
        E_USER_WARNING => 'E_USER_WARNING',
        E_USER_NOTICE => 'E_USER_NOTICE',
        E_STRICT => 'E_STRICT',
        E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
        E_DEPRECATED => 'E_DEPRECATED',
        E_USER_DEPRECATED => 'E_USER_DEPRECATED',
    ];

    /** Полный список всех типов PHP ошибок (т.е. ошибки которые включает в себя {@see E_ALL}) */
    public const ALL = [
        E_ERROR,
        E_WARNING,
        E_PARSE,
        E_NOTICE,
        E_CORE_ERROR,
        E_CORE_WARNING,
        E_COMPILE_ERROR,
        E_COMPILE_WARNING,
        E_USER_ERROR,
        E_USER_WARNING,
        E_USER_NOTICE,
        E_STRICT,
        E_RECOVERABLE_ERROR,
        E_DEPRECATED,
        E_USER_DEPRECATED,
    ];

    /** Список Предупреждений (Ошибок НЕ приводящих к прекращению выполнения скрипта) */
    public const WARNINGS_AND_NOTICES = [
        E_WARNING,
        E_NOTICE,
        E_CORE_WARNING,
        E_COMPILE_WARNING,
        E_USER_WARNING,
        E_USER_NOTICE,
        E_STRICT,
        E_DEPRECATED,
        E_USER_DEPRECATED,
    ];

    /** Список ошибок, приводящих к прекращению выполнения скрипта */
    public const FATAL_ERRORS = [
        E_ERROR,
        E_PARSE,
        E_CORE_ERROR,
        E_COMPILE_ERROR,
        E_USER_ERROR,
        E_RECOVERABLE_ERROR,
    ];

    /** Список ошибок связанный с непосредственным выполнением кода */
    public const SCRIPT_ERRORS = [
        E_ERROR,
        E_WARNING,
        E_NOTICE,
        E_USER_ERROR,
        E_USER_WARNING,
        E_USER_NOTICE,


        E_STRICT,
        E_RECOVERABLE_ERROR,


        E_DEPRECATED,
        E_USER_DEPRECATED,
    ];

    /** Список ошибок относящихся к ядру PHP (т.е. не связанных с кодом) */
    public const CORE_ERRORS = [
        E_CORE_ERROR,
        E_CORE_WARNING,
    ];

    /** Список ошибок связанных с компиляцией кода */
    public const CODE_ERRORS = [
        E_PARSE,
        E_COMPILE_ERROR,
        E_COMPILE_WARNING,
    ];

    /** "Пользовательские" ошибки, т.е. ошибки выброшенные по задумке разработчика, при использовании PHP функции trigger_error() */
    public const USER_ERRORS = [
        E_USER_ERROR,
        E_USER_WARNING,
        E_USER_NOTICE,
    ];

    /** Ошибки связанные с использованием "устаревшего" кода */
    public const DEPRECATED_ERRORS = [
        E_DEPRECATED,
        E_USER_DEPRECATED,
    ];
}
