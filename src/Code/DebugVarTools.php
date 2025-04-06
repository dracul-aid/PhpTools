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

/**
 * Полезные инструменты для отладки
 *
 * Оглавление:
 * <br>{@see DebugVarTools::varDump()} - Аналог PHP функции {@see var_dump()}, но вернет дебаг информацию о переменной в виде строки
 * <br>{@see DebugVarTools::json()} - Вернет отформатированную JSON строку для переданного значения
 */
final class DebugVarTools
{
    /**
     * Аналог PHP функции {@see var_dump()}, но вернет дебаг информацию о переменной в виде строки
     *
     * @param   mixed   $value
     *
     * @return  string
     */
    public static function varDump(mixed $value): string
    {
        return ObHelper::callFunction('var_dump', [$value]);
    }

    /**
     * Вернет отформатированную JSON строку для переданного значения
     *
     * @param   mixed   $value
     *
     * @return  string
     */
    public static function json(mixed $value): string
    {
        return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
