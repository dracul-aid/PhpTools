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
 * Набор отладочных функций для получения информации о переменных с HTML форматированием
 *
 * Оглавление:
 * <br>- {@see DebugVarHtmlTools::varDump()} - Аналог {@see var_dump()}
 * <br>- {@see DebugVarHtmlTools::printR()} - Аналог {@see print_r()}
 * <br>- {@see DebugVarHtmlTools::json()} - Вернет переданное значение ввиде отформатированной JSON строки
 * <br>- {@see DebugVarHtmlTools::minDebugValue()} - Вернет короткое дебаг-значение переменной
 * <br>- {@see DebugVarHtmlTools::minDebugValueCases()} - Вернет короткие дебаг значения для переданных переменных
 *
 * @see DebugVarTools Аналогичный инструментарий без HTML оформления
 *
 * @since 0.4.0
 *
 * @todo  Реализовать юнит-тесты для остальных функций
 */
final class DebugVarHtmlTools
{
    /**
     * Вернет HTML отформатированный результат работы {@see var_dump()}
     *
     * @param   mixed   $values
     *
     * @return  string
     */
    public static function varDump(mixed $values): string
    {
        return self::htmlBlock(
            ObHelper::callFunction('var_dump', [$values])
        );
    }

    /**
     * Вернет HTML отформатированный результат работы {@see print_r()}
     *
     * @param   mixed   $values
     *
     * @return  string
     */
    public static function printR(mixed $values): string
    {
        return self::htmlBlock(
            print_r($values, true)
        );
    }

    /**
     * Вернет переданное значение ввиде отформатированной HTML кодом JSON строки
     *
     * @param   mixed   $values
     *
     * @return  string
     */
    public static function json(mixed $values): string
    {
        return self::htmlBlock(
            json_encode($values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    /**
     * Вернет короткое дебаг-значение переменной
     *
     * @param   mixed   $values
     *
     * @return  string
     *
     * @since 1.4.0
     */
    public static function minDebugValue(mixed $values): string
    {
        return self::htmlBlock(
            DebugVarTools::minDebugValue($values)
        );
    }

    /**
     * Обернет переданную строку в HTML теги
     *
     * @param   string   $string
     *
     * @return string
     */
    private static function htmlBlock(string $string): string
    {
        return "<pre><code>{$string}</code></pre>";
    }

    /**
     * Вернет короткие дебаг значения для переданных переменных
     *
     * @param   array   $cases   Массив, со списком значений для вывода
     *
     * @return string
     *
     * @since 1.4.0
     */
    public static function minDebugValueCases(array $cases): string
    {
        $result = '';

        foreach ($cases as $var)
        {
            $result .= self::minDebugValue($var);
        }

        return $result;
    }
}
