<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\ExceptionTools\PhpErrorCode\Errors\Interfaces;

/** Интерфейс всех Классов-ошибок (соответствий PHP кодам ошибок) */
interface PhpCodeErrorBasicInterface extends \Throwable
{
    /**
     * Вернет код ошибки PHP с которым ассоциирован класс
     *
     * @return int Вернет один из вариантов описных в {@see PhpErrorCodeThrowableTools::TYPE_AND_ERROR_CLASSES}
     */
    public static function getPhpErrorCode(): int;
}
