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

use DraculAid\PhpTools\tests\Code\DebugVarTools\DebugVarToolsTest;

/**
 * Полезные инструменты для отладки
 *
 * Оглавление:
 * <br>- {@see DebugVarTools::varDump()} - Аналог PHP функции {@see var_dump()}, но вернет дебаг информацию о переменной в виде строки
 * <br>- {@see DebugVarTools::json()} - Вернет отформатированную JSON строку для переданного значения
 * <br>- {@see DebugVarTools::minDebugValue()} - Вернет короткое дебаг-значение переменной
 * <br>- {@see DebugVarTools::minDebugValueCases()} - Вернет короткие дебаг значения для переданных переменных
 *
 * @see DebugVarHtmlTools Аналогичный инструментарий с HTML оформлением (для более удобного вывода на HTML страницах)
 *
 * Test cases for class {@see DebugVarToolsTest}
 *
 * @since 0.4.0
 *
 * @todo Реализовать юнит-тесты для остальных функций
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

    /**
     * Вернет короткое дебаг-значение переменной
     *
     * @param   mixed   $value
     *
     * @return string
     *
     * @since 1.4.0
     *
     * @todo Добавить отлоадочный блок посвященный анонимным классам
     * @todo Добавить более расширенный блок посвященный анонимным функциям
     * @todo Добавить более расширенный блок посвященный перечисляемым
     */
    public static function minDebugValue(mixed $value): string
    {
        if (is_callable($value))
        {
            $callableReturn = match (true) {
                is_string($value) => "callable {$value}()",
                is_array($value) && is_string($value[0]) => "callable-array-class {$value[0]}::{$value[1]}()",
                is_array($value) && is_object($value[0]) => "callable-array-object " . $value[0]::class . "::{$value[1]}()",
                $value instanceof \Closure => "callable \Closure",
                default => "callable-function()",
            };

            if ($callableReturn !== '') return $callableReturn;
        }

        return match (true) {
            $value === null => 'NULL',
            $value === false => 'FALSE',
            $value === true => 'TRUE',

            is_float($value) && is_nan($value) => 'float(NAN)',
            $value === INF => 'float(+INF)',
            $value === -INF => 'float(-INF)',
            // @todo заменить на свою функцию, что бы для 0 - вставляла 0.0, аналогично для 1.0 - 1.0 (сейчас дробная часть отбрасывается)
            is_float($value) => sprintf('float: %g', $value),

            is_int($value) && is_resource($value) === false => number_format($value, 0, '.', '_'),

            $value === '' => '""',
            is_string($value) => '"' . mb_substr($value, 0, 75) . (mb_strlen($value)>75 ? '...' : '') . '"(' . mb_strlen($value) . ' chars)',

            $value === [] => '[]',
            is_array($value) && array_is_list($value) => "list(" . count($value) . ")",
            is_array($value) => "array(" . count($value) . ")",

            is_object($value) => $value::class,

            default => print_r($value, true),
        };
    }

    /**
     * Вернет короткие дебаг значения для переданных переменных
     *
     * @param   array    $cases       Массив, со списком значений для вывода
     * @param   string   $separator   Разделитель, между каждым значением
     * @param   string   $before      Будет добавлено к выводу, перед выводом каждого значения
     *
     * @return string
     *
     * @since 1.4.0
     */
    public static function minDebugValueCases(array $cases, string $separator = ', ', string $before = ''): string
    {
        $result = '';

        foreach ($cases as $var)
        {
            if ($result !== '') $result .= $separator;

            $result .= $before . self::minDebugValue($var);
        }

        return $result;
    }
}
