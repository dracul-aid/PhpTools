<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\ExceptionTools\PhpErrorCode\Descriptions;

/**
 * Класс с описанием констант PHP типов ошибок на русском языке
 *
 * См также {@see PhpErrorCodeEnDescriptionsConstants} - описание на английском языке
 *
 * Оглавление:
 * <br>- {@see PhpErrorCodeRuDescriptionsConstants::TITLES} Названия типов ошибок
 * <br>- {@see PhpErrorCodeRuDescriptionsConstants::DESCRIPTIONS} Краткое описание типов ошибок
 *
 * Test cases for class {@see PhpErrorCodeDescriptionsConstantsTest}
 */
final class PhpErrorCodeRuDescriptionsConstants
{
    /** Названия типов ошибок на русском языке (ключи - коды ошибок, см PHP константы E_***) */
    public const TITLES = [
        E_ERROR => 'Фатальная ошибка времени выполнения',
        E_WARNING => 'Предупреждения времени выполнения',
        E_PARSE => 'Ошибки синтаксического анализатора кода',
        E_NOTICE => 'Оповещения в ходе выполнения',
        E_CORE_ERROR => 'Фатальные ошибки ядра PHP',
        E_CORE_WARNING => 'Предупреждения ядра PHP',
        E_COMPILE_ERROR => 'Фатальная ошибка компиляции',
        E_COMPILE_WARNING => 'Предупреждения во время компиляции',
        E_USER_ERROR => 'Пользовательские ошибки',
        E_USER_WARNING => 'Пользовательские предупреждения',
        E_USER_NOTICE => 'Пользовательские оповещения',
        E_STRICT => 'Советы',
        E_RECOVERABLE_ERROR => 'Фатальные ошибки с возможностью обработки',
        E_DEPRECATED => 'Указание на устаревшую конструкцию',
        E_USER_DEPRECATED => 'Указание на пользовательскую устаревшую конструкцию',
    ];

    /** Краткое описание типов ошибок на русском языке (ключи - коды ошибок, см PHP константы E_***) */
    public const DESCRIPTIONS = [
        E_ERROR => 'Указывает на ошибки, которые невозможно исправить во время выполнения (например: расходы памяти, время выполнения...)',
        E_WARNING => 'Не фатальные ошибки, предупреждения что возможно происходит что-то не то (например: обращение к несуществующему индексу)',
        E_PARSE => 'Неверный синтаксис кода, обнаруженный PHP перед перед компиляцией',
        E_NOTICE => 'Во время выполнения произошла подозрительная ситуация, которая может быть как нормальным ходом выполнения, так и потенциальной ошибкой',
        E_CORE_ERROR => 'Не связана непосредственно с выполняемым скриптом (или данными скрипта)',
        E_CORE_WARNING => 'Не связана непосредственно с выполняемым скриптом (или данными скрипта)',
        E_COMPILE_ERROR => 'Во время компиляции кода (работы Zend Scripting Engine)',
        E_COMPILE_WARNING => 'Во время компиляции кода (работы Zend Scripting Engine)',
        E_USER_ERROR => 'Критические ошибки в коде, выброшенные с помощью trigger_error()',
        E_USER_WARNING => 'Не критические ошибки в коде, выброшенные с помощью trigger_error()',
        E_USER_NOTICE => 'Ситуации, выброшенные с помощью trigger_error()',
        E_STRICT => 'Включаются для того, чтобы PHP предлагал изменения в коде, которые обеспечат лучшее взаимодействие и совместимость кода.',
        E_RECOVERABLE_ERROR => 'Такие ошибки указывают, что, вероятно, возникла опасная ситуация, но при этом, скриптовый движок остаётся в стабильном состоянии. Если такая ошибка не обрабатывается функцией, определённой пользователем для обработки ошибок (смотрите set_error_handler()), выполнение приложения прерывается, как происходит при ошибках E_ERROR',
        E_DEPRECATED => 'Используемая в коде PHP конструкция считается устаревшей и в будущих версиях не будет поддерживаться',
        E_USER_DEPRECATED => 'Используемый функционал считается устаревшим, предупреждение устанавливается через trigger_error()',
    ];
}
